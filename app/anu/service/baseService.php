<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 11:10
 */

namespace Anu;


class baseService implements \JsonSerializable
{
    protected   $table = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;

    /**
     * @return array
     */
    public function jsonSerialize() {
        $this->class = Anu::getClassName($this);
        return get_object_vars($this);
    }


    /**
     * Permissions
     *
     * @return array
     */
    public function definePersmissions(){
        return array(
            Permission::Insert  =>  array(Permission::LoggedIn),
            Permission::Update  =>  array(Permission::Admin, Permission::Author),
            Permission::Delete  =>  array(Permission::Admin),
            Permission::Read    =>  array(Permission::All)
        );
    }


    /**
     * Define default values for $entry
     * define timestamps and defaults declared in
     * defineAttributes
     *
     * @param $entry baseModel|entryModel
     */
    protected function defineDefaultValues($entry){
        $defaults = $entry->defineAttributes();
        foreach ($defaults as $k => $v){
            if(isset($v['default']) && $v['default'] && property_exists($entry, $k)){
                $default = $v['default'];
                switch ($default){
                    case Defaults::creationTimestamp:
                        if(!$entry->$k){
                            $entry->$k = "now()";
                        }
                        break;
                    case Defaults::currentTimestamp:
                        $entry->$k = "now()";
                        break;
                    case Defaults::currentUserId:
                        if(anu()->user->getCurrentUser() && anu()->user->getCurrentUser()->id && $entry->$k == null){
                            $entry->$k = (int)anu()->user->getCurrentUser()->id;
                        }
                        break;
                    default:
                        if($k !== 'slug' && !$entry->id){
                            $entry->$k = $default;
                        }
                        break;
                }
            }
        }
    }

    /**
     * @param $entry    baseModel
     * @return bool
     */
    protected function validate($entry, $clearErrors = true){
        $attributes = $entry->defineAttributes();
        if($clearErrors){
            $entry->clearErrors();
        }

        foreach ($attributes as $k => $v){
            if(!is_object($entry->$k) && !is_array($entry->$k) && $entry->$k !== 'now()'){
                $validatorList = $this->getValidatorList($v);
                $fieldName = array_key_exists('title', $v)? $v['title'] : $k;

                if($validatorList){
                    $validated = validator::is_valid(array($fieldName => strip_tags($entry->$k)), array(
                        $fieldName => $validatorList
                    ));

                    if($validated !== true){
                        $entry->addError($k, $validated[0]);
                    }
                }

                //check for unique
                if(array_key_exists('unique', $v)){
                    $exists = anu()->database->has($this->getTable(), array(
                       $k => $entry->$k
                    ));

                    if($exists){
                        $entry->addError($k, Anu::parse('There is already a {type} with the {key} {value}', array(
                            'type'  => Anu::getClassName($this),
                            'key'   => $fieldName,
                            'value' => $entry->$k
                        )));
                    }
                }
            }
        }

        if($entry->getErrors() == null){
            return true;
        }

        return false;
    }


    /**
     * TODO need more validators....
     *
     * @param $rules
     * @return string
     * @throws \Exception
     */
    public function getValidatorList($rules){
        if(!isset($rules[0])){
            throw new \Exception('invalid model, no datatype given');
        }

        $arrValidator = array();
        switch ($rules[0]){
            case AttributeType::DateTime:
                $arrValidator[] = 'date';
                break;
            case AttributeType::Number:
                $arrValidator[] = 'numeric';
                break;
            case AttributeType::Mixed:
                //$arrValidator[] = '';
                break;
        }

        $arrMinMaxValidator = array('max_numeric', 'min_numeric', 'min_len', 'max_len');
        foreach ($arrMinMaxValidator as $v){
            if(isset($rules[$v])){
                $arrValidator[] = $v . ',' . $rules[$v];
            }
        }
        if(isset($rules['required'])){
            $arrValidator[] = 'required';
        }

        return implode('|', $arrValidator);
    }


    /**
     * Populate the entryModel, add relations
     *
     * @param $data         array
     * @param $model        baseModel|entryModel
     * @return mixed
     * @throws \Exception
     */
    public function populateModel($data, $model, $attributes = null){
        if($model->setData($data)){
            if($attributes === null){
                $attributes = $model->defineAttributes();
            }
            foreach ($attributes as $k => $v){
                switch ($v[0]){
                    case AttributeType::Relation:
                        if(isset($v['relatedTo']) && $relation = $v['relatedTo']){
                            $class = $relation['model'];
                            $model->$k = $this->getBaseCriteriaModelForPopulatedEntry($model, $data, $class, $k);
                        }
                        break;
                    case AttributeType::Matrix:
                        $class = 'matrix';
                        $model->$k = $this->getBaseCriteriaModelForPopulatedEntry($model, $data, $class, $k);
                        break;
                    case AttributeType::DateTime:
                        $UTC = new \DateTimeZone("UTC");
                        $date = new \DateTime( $data[$k], $UTC );
                        $model->$k = $date->format('Y-m-d H:i:s');
                        break;
                    case AttributeType::JSON:
                        $jsonData = json_decode($data[$k], true);
                        if(is_array($jsonData) && count($jsonData)){
                            foreach ($jsonData as $jsonKey => $json){
                                if(property_exists($model, $jsonKey)){
                                    throw new \Exception($jsonKey . ' is a reserved key an must not be used as an index for matrixcontent');
                                }
                                $model->$jsonKey = $json;
                            }
                        }
                        break;
                }
            }
            return $model;
        }
        throw new \Exception('Could not populate Model');
    }

    /**
     * Create Criteria Model to find related Entries for $entry
     *
     * @param $entry                baseModel|entryModel        Plain Model of the entry
     * @param $data                 array                       array with the data of the field
     * @param $class                string                      className of the related Entry -> matrix|class eg page, answer...
     * @param $field                string                      the field in the data
     * @return elementCriteriaModel
     */
    public function getBaseCriteriaModelForPopulatedEntry($entry, $data, $class, $field){
        $criteriaModel = new elementCriteriaModel(anu()->$class);
        $id = isset($entry->id) ? $entry->id : null;
        //new empty entry at all.... with no id an nothing
        if($data === null || !array_key_exists($field, $data) || $data[$field] === null){
            $criteriaModel->relatedTo  = array(
                'field' => $field,
                'id'    => $id,
                'model' => Anu::getClassName($this)
            );
        }else{
            if(array_key_exists($field, $data) && is_array($data[$field])) {
                if (count($data[$field]) && array_key_exists(0, $data[$field]) && !is_array($data[$field][0])) {
                    // user gave an array with all ids
                    $attributes = $entry->defineAttributes();
                    $primary_key = $attributes[$field]['relatedTo']['field'];
                    $criteriaModel->$primary_key = $data[$field];
                    $criteriaModel->storeIds($data[$field]);
                }elseif(array_key_exists('ids', $data[$field])){
                    //user did not change anything and just returned the origianl CriteriaModel of the entry
                    $criteriaModel->relatedTo  = array(
                        'field' => $field,
                        'id'    => $id,
                        'model' => Anu::getClassName($this)
                    );
                    $criteriaModel->storeIds($data[$field]['ids']);
                }else{
                    //user inserted an array of objects eg matrix elements that contains elements with an id
                    $ids = array();
                    foreach ($data[$field] as $populateField){
                        $ids[] = $populateField['id'];
                    }
                    if($ids){
                        $primary_key = anu()->matrix->getPrimaryKey();
                        $criteriaModel->$primary_key = $ids;
                        $criteriaModel->storeIds($ids);
                    }
                }
            }else{
                //no data from user -> just search the original relations from database
                $criteriaModel->relatedTo  = array(
                    'field' => $field,
                    'id'    => $id,
                    'model' => Anu::getClassName($this)
                );
                $ids = $criteriaModel->ids();
                $criteriaModel->storeIds($ids);
            }
        }

        return $criteriaModel;
    }


    /**
     * Generate Model from by Post variables
     * only for normal not ajax Post request... a little bit depricated since I always use angular
     *
     * @return baseModel|baseService|entryModel|entryService|bool|null|string
     */
    public function generateEntryFromPost(){
        $post = anu()->request->getValue('data');
        if(isset($post[$this->primary_key])){
            $entry = $this->getElementById($post[$this->primary_key]);
        }else{
            $entry = Anu::getClassByName($this, "Model", true);
        }
        $this->populateModel($post, $entry);

        return $entry;
    }

    /**
     * loop through all attributes to create the select command for the db
     *
     * @param $attributes
     * @return array
     */
    public function iterateDBSelect($attributes){
        $select = array();
        $primaryKey = $this->primary_key;

        $select[] = $this->table . "." . $primaryKey . "(id)";
        $record = Anu::getClassByName($this, 'Record', true);
        $recordAttributes = $record->defineAttributes();
        foreach ($recordAttributes as $k => $v){
            if(array_key_exists($k, $attributes)){
                $select[] = $this->table . "." . $k;
            }
        }

        return $select;
    }


    /**
     * Get an element based on its ID
     *
     * @param $userId
     * @return baseModel|null
     */
    public function getElementById($elementId){
        if(!is_numeric($elementId) || !$elementId){
            return null;
        }
        $this->id = $elementId;
        if($model = Anu::getClassByName($this, 'Model', true)){
            $attributes = $model->defineAttributes();

            $select = $this->iterateDBSelect($attributes);

            $join = array(
                "[>]relation" => array($this->table . "." . $this->primary_key => 'id_1')
            );
            $where = array($this->table . "." . $this->primary_key => $elementId);

            $row = anu()->database->get($this->table, $join, $select, $where);

            if($row){
                return $this->populateModel($row, $model);
            }
        }else{
            throw new \Exception('could not find ' . Anu::getClassName($this));
        }
        return null;
    }

    /**
     * TODO update this.... I only worked with entryService saveElement for a loooong time <.<
     *
     * @param $element            baseModel
     * @return bool|int
     */
    public function saveElement($element){
        $this->defineDefaultValues($element);
        if(!$this->validate($element) || !$this->checkSavePermission($element)){
            return false;
        }

        if(!$this->table || !$this->primary_key){
            $className = Anu::getClassName($this);
            $this->table = anu()->$className->table;
            $this->primary_key = anu()->$className->primary_key;
        }

        //check if its a new entry of if we should update an existing one
        $record = Anu::getClassByName($this, "Record", true);
        $recordAttributes = $record->defineAttributes();
        if(!$element->id){
            // new entry -> insert
            $data = $element->getData();
            $values = array();
            foreach ($element->defineAttributes() as $key => $value){
                if($data[$key] !== 'now()'){
                    if(array_key_exists($key, $recordAttributes)){
                        if($value[0] == AttributeType::Position){
                            $this->changePositions($element, $key, $value);
                        }
                        $values[$key] = $element->$key;
                    }
                }else{
                    $values["#".$key] = $data[$key];
                }
            }

            anu()->database->insert($this->table, $values);
            $id = anu()->database->id();
            $element->id = $id;
            return $id;
        }else{
            // existing entry -> update
            $data = $element->getData();
            $values = array();
            foreach ($element->defineAttributes() as $key => $value){
                if($data[$key] !== 'now()'){
                    if(array_key_exists($key, $recordAttributes)){
                        if($value[0] == AttributeType::Position){
                            $this->changePositions($element, $key, $value);
                        }
                        $values[$key] = $element->$key;
                    }
                }else{
                    $values["#".$key] = $data[$key];
                }
            }

            anu()->database->update($this->table, $values, array(
                $this->table . "." . $this->primary_key => $element->id
            ));

            return anu()->database->id();
        }
    }


    /**
     * @param $element baseModel
     * @return bool
     */
    public function checkSavePermission($element){
        if($element->id){
            if(!anu()->user->can(Anu::getClassName($this), Permission::Update, $element)){
                $element->addError('permission', "Currentuser has no update permissions");
                return false;
            }
        }else{
            if(!anu()->user->can(Anu::getClassName($this), Permission::Insert)){
                $element->addError('permission', "user has no insert permissions");
                return false;
            }
        }
        return true;
    }


    /**
     * @return string
     */
    public function getTable(){
        return $this->table;
    }

    /**
     * @return string
     */
    public function getPrimaryKey(){
        return $this->primary_key;
    }

    /**
     * @param $attributes
     */
    public function find($attributes = null, $onlyIds = false){
        $criteria = new elementCriteriaModel($this);
        return $criteria->find($attributes, $onlyIds);
    }


    /**
     * @param $element      baseModel|entryModel
     * @param $key          string
     * @param $attributes   array
     */
    public function changePositions($element, $field,  $parentfield){
        $children = null;
        $where = array();
        $oldPosition = $element->oldPosition;
        $newPosition = $element->$field;
        $attributes = $element->defineAttributes();
        if($parentfield){
            if($attributes[$parentfield][0] == AttributeType::Relation){
                //relation
                if(is_array($element->$parentfield) && count($element->$parentfield) && $element->$parentfield[0]){
                    $parentId = $element->$parentfield[0];
                    $parent = $this->getElementById($parentId);
                    $children = $this->find(['relatedTo' => $parent, $this->primary_key.'[!]' => $element->id], true);
                }else{
                    //find entries related to nothing...
                    $children = $this->find([
                        'relatedTo' => array(
                            'field' => $parentfield,
                            'id'    => 'nothing',
                            'model' => Anu::getClassName($this)
                    ), $this->primary_key.'[!]' => $element->id], true);
                }

                $where[$this->primary_key] = $children;
            }else{
                //just normal int field relation...
                $where[$parentfield] = $element->$parentfield;
            }
        }else{
            $where[$this->primary_key . "[!]"] = $element->id;
        }
        $isInsert = false;
        if(property_exists($element, 'oldSiblings') && property_exists($element, 'oldPosition') && $element->oldSiblings !== null){
            $isInsert = true;
            anu()->database->update($this->getTable(), array("#".$field => "$field-1"), array(
                $this->primary_key => $element->oldSiblings,
                $field . "[>=]" => $element->oldPosition
            ));
        }

        if(($oldPosition > $newPosition) || $isInsert){
            if(!$isInsert){
                $where[$field . "[<=]"] = $oldPosition;
            }
            $where[$field . "[>=]"] = $newPosition;
            anu()->database->update($this->getTable(), array("#".$field => "$field+1"), $where);
        }else{
            $where[$field . "[<=]"] = $newPosition;
            $where[$field . "[>=]"] = $oldPosition;
            anu()->database->update($this->getTable(), array("#".$field => "$field-1"), $where);
        }
    }

    /**
     * @param $entry baseModel
     */
    public function renderForm($entry = null, $template = 'admin/forms/index.twig'){
        if(!$entry){
            $entry = Anu::getClassByName($this, "Model", true);
            //just to add relationModels
            $this->populateModel(null, $entry);
        }

        //store titles for modules...
        foreach ($entry->defineAttributes() as $k => $v){
            if($v[0] == AttributeType::Relation && $entry->$k){
                $entry->$k->storeTitles();
            }

            if($v[0] == AttributeType::Bool){
                $entry->$k = property_exists($entry, $k)? (bool)$entry->$k : false;
            }

            if($v[0] == AttributeType::Position){
                $entry->$k = null;
            }
            if($v[0] == AttributeType::Matrix){
                $matrixAttributes = anu()->matrix->getMatrixByName($v[1])->defineAttributes();
                $matrixArray = array();
                $index = 0;
                foreach ($entry->$k as $matrix){
                    $matrixArray[$index] = json_decode($matrix->content, true);
                    $matrixArray[$index]['type'] = $matrix->type;
                    $matrixArray[$index]['title'] = $matrix->type;
                    $matrixArray[$index]['attributes'] = $matrixAttributes[$matrix->type];
                    $matrixArray[$index]['matrixId']    = $v[1];
                    $matrixArray[$index]['id']    = $matrix->id;
                    $index++;
                }
                $entry->$k = $matrixArray;
            }
        }

        anu()->template->addJsCode('
            var entry = ' . json_encode($entry) . ';
        ');
        anu()->template->addJsCode('
            var attributes = ' . json_encode($entry->defineAttributes()) . ';
        ');

        return anu()->template->render($template, array(
            'entry' => $entry,
            'attributes' => $entry->defineAttributes()
        ));
    }

    /**
     * @param $entry baseModel
     */
    public function renderList(){
        $className = Anu::getClassName($this);
        $entries = anu()->$className->find(['enabled' => 'all']);
        $model = Anu::getClassByName($className, "Model", true);
        $attributes = $model->defineAttributes();
        foreach ($entries as $entry){
            $entry->children = array();
            $entry->url = $entry->getUrl();
        }

        anu()->template->addJsCode('
            if(typeof entries === "undefined"){
                var entries = {};
            }
            entries["' . $className .  '"] = ' . json_encode($entries) . ';
        ');
        anu()->template->addJsCode('
            if(typeof attributes === "undefined"){
                var attributes = {};
            }
            attributes["' . $className .  '"] = ' . json_encode($attributes) . ';
        ');
        return anu()->template->render('admin/lists/index.twig', array(
            'entries'       => $entries,
            'attributes'    => $attributes,
            'list'          => $className
        ));
    }
}