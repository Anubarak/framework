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
    public      $tableName = null;
    public      $model = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;

    /**
     * @return array
     */
    public function jsonSerialize() {
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
                        $k => $entry->$k,
                        $this->primary_key."[!]" => $entry->id
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
        //$data['attributes'] = "";

        if($model->setData($data)){
            if($attributes === null){
                $attributes = $model->defineAttributes();
            }
            if($model instanceof pageModel){
                echo "<pre>tttt";
                var_dump($model);
                echo "</pre>";
            }

            foreach ($attributes as $k => $v){
                $value = isset($data[$k])? $data[$k] : null;
                if($field = anu()->field->getField($v[0])){
                    $field->onPopulate($model, $v, $value, $k);
                }else{
                    $model->$k = $value;
                }

            }

            return $model;
        }
        throw new \Exception('Could not populate Model');
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
    public function iterateDBSelect(){
        $select = array();
        $primaryKey = $this->primary_key;

        $select[] = $this->tableName . "." . $primaryKey . "(id)";
        $record = Anu::getRecordByName($this->model, true);

        $recordAttributes = $record->defineAttributes();

        foreach ($recordAttributes as $k => $v){
            $select[] = $this->tableName . "." . $k;
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

        if($model = Anu::getModelByName($this->model)){
            $attributes = $model->defineAttributes();
            $select = $this->iterateDBSelect($attributes);
            $join = array(
                "[>]relation" => array($this->tableName . "." . $this->primary_key => 'id_1')
            );
            $where = array($this->tableName . "." . $this->primary_key => $elementId);

            $row = anu()->database->get($this->tableName, $join, $select, $where);
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

        if(!$this->tableName || !$this->primary_key){
            //TODO change to record

            $className = Anu::getClassName($this);
            $this->name  = anu()->$className->tableName;
            $this->primary_key = anu()->$className->primary_key;
        }

        //check if its a new entry of if we should update an existing one
        $record = Anu::getRecordByName($this->model);
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

            anu()->database->insert($this->tableName, $values);
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

            anu()->database->update($this->tableName, $values, array(
                $this->tableName . "." . $this->primary_key => $element->id
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
        return $this->tableName;
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
    public function find($attributes = null, $onlyIds = false, $debug = false){
        $record = Anu::getRecordByName($this->model);

        $criteria = new elementCriteriaModel($record);
        return $criteria->find($attributes, $onlyIds, $debug);
    }


    /**
     * @param $element      baseModel|entryModel
     * @param $key          string
     * @param $attributes   array
     */
    public function changePositions($element, $field,  $parentfield){
        /** @var baseRecord $record */
        //TODO update to record
        $record = Anu::getClassByName($element, "Record", true);
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
                    $children = $this->find(['relatedTo' => $parent, $record->getPrimaryKey().'[!]' => $element->id], true);
                }else{
                    //find entries related to nothing...
                    $children = $this->find([
                        'relatedTo' => array(
                            'field' => $parentfield,
                            'id'    => 'nothing',
                            'model' => Anu::getClassName($this)
                    ), $record->getPrimaryKey().'[!]' => $element->id], true);
                }

                $where[$record->getPrimaryKey()] = $children;
            }else{
                //just normal int field relation...
                $where[$parentfield] = $element->$parentfield;
            }
        }else{
            $where[$record->getPrimaryKey() . "[!]"] = $element->id;
        }
        $isInsert = false;
        if(property_exists($element, 'oldSiblings') && property_exists($element, 'oldPosition') && $element->oldSiblings !== null){
            $isInsert = true;
            //TODO record get Table Name
            anu()->database->update($this->getTable(), array("#".$field => "$field-1"), array(
                $record->getPrimaryKey() => $element->oldSiblings,
                $field . "[>=]" => $element->oldPosition
            ));
        }

        if(($oldPosition > $newPosition) || $isInsert){
            if(!$isInsert){
                $where[$field . "[<=]"] = $oldPosition;
            }
            $where[$field . "[>=]"] = $newPosition;
            anu()->database->update($record->tableName, array("#".$field => "$field+1"), $where);
        }else{
            $where[$field . "[<=]"] = $newPosition;
            $where[$field . "[>=]"] = $oldPosition;
            anu()->database->update($record->tableName, array("#".$field => "$field-1"), $where);
        }
    }

    /**
     * @param $entry baseModel
     */
    public function renderForm($entry = null, $template = 'admin/forms/index.twig'){

        if(is_string($entry)){
            $entry = Anu::getModelByName($entry);
            //just to add relationModels
            $this->populateModel(null, $entry);
        }

        //store titles for modules...
        $attributes = $entry->defineAttributes();

        foreach ($entry->defineAttributes() as $k => $v){
            if($v[0] == AttributeType::Relation && $entry->$k){
                $entry->$k = $entry->$k->find(null, true);
            }

            if($v[0] == AttributeType::Bool){
                $entry->$k = property_exists($entry, $k)? (bool)$entry->$k : false;
            }

            if($v[0] == AttributeType::Position){
                $entry->$k = null;
            }
            if($v[0] == AttributeType::Matrix){
                $matrixAttributes = $v['attributes'];

                $attributes[$k]['attributes'] = $matrixAttributes;
                $matrixArray = array();
                $index = 0;
                foreach ($entry->$k as $matrix){
                    $matrixArray[$index] = $matrix->content;
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

        $entry->attributes = $attributes;
        $entry->fieldLayout = anu()->entry->getFieldLayout($entry);

        anu()->template->addAnuJsObject($entry, 'entry');

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
    public function renderList($recordHandle){
        $className = $recordHandle;
        /** @var entryRecord $record */
        $record = Anu::getRecordByName($recordHandle);
        $criteria = new elementCriteriaModel($record);
        $entries = $criteria->find(['enabled' => 'all']);

        $model = Anu::getModelByName($recordHandle);
        $attributes = $model->defineAttributes();
        foreach ($entries as $entry){
            if($entry === null){
               continue;
            }

            if($record->structure === StructureType::Matrix){
                $entry->children = $criteria->find([
                    'relatedTo' => array(
                        'field' => "child",
                        'model' => $entry->class,
                        'id'    => $entry->id
                    )
                ], true);
            }

            //$entry->children = array();
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

        $controller = "entry";
        $controllerName = $recordHandle . "Controller";
        if(class_exists(Anu::getNameSpace() . $controllerName)){
            $controller = $recordHandle;
        }

        return anu()->template->render('admin/lists/index.twig', array(
            'entries'       => $entries,
            'attributes'    => $attributes,
            'list'          => $className,
            'controller'    => $controller
        ));
    }
}