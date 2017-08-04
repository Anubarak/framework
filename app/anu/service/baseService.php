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
     * @param $entry            entryModel
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
     * @param $data
     * @param $model        baseModel|entryModel
     * @return mixed
     * @throws \Exception
     */
    protected function populateModel($data, $model){
        if($model->setData($data)){
            $attributes = $model->defineAttributes();
            foreach ($attributes as $k => $v){
                if($v[0] == AttributeType::Relation && isset($v['relatedTo'])){
                    if($relation = $v['relatedTo']){
                        /*if(!is_array($data[$k])){
                            $data[$k] = explode(",", $data[$k]);
                        }
                        $data[$k] = array_unique($data[$k]);*/
                        $class = $relation['model'];
                        $criteriaModel = new elementCriteriaModel(anu()->$class);
                        $id = isset($model->id) ? $model->id : null;
                        $criteriaModel->relatedTo  = array(
                            'field' => $k,
                            'id'    => $id,
                            'model' => Anu::getClassName($this)
                        );
                        $ids = $criteriaModel->ids();
                        $criteriaModel->storeIds($ids);
                        $model->$k = $criteriaModel;
                    }
                }
            }
            return $model;
        }
        throw new \Exception('Could not populate Model');
    }


    /**
     * Generate Model from by Post variables
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
    public function renderForm($entry = null, $template = 'forms/index.twig'){
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

            if($v[0] == AttributeType::Bool && $entry->$k){
                $entry->$k = (bool)$entry->$k;
            }

            if($v[0] == AttributeType::Position){
                $entry->$k = null;
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
        return anu()->template->render('lists/index.twig', array(
            'entries'       => $entries,
            'attributes'    => $attributes,
            'list'          => $className
        ));
    }
}