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
                if($validatorList){
                    $validated = validator::is_valid(array($k => $entry->$k), array(
                        $k => $validatorList
                    ));

                    if($validated !== true){
                        $entry->addError($k, $validated[0]);
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
     * @param $join
     * @param null $parentTable
     * @return array
     */
    public function iterateDBSelect($attributes, $parentTable){
        $select = array();
        $primaryKey = $this->primary_key;

        $select[] = $parentTable . "." . $primaryKey . "(id)";
        foreach ($attributes as $k => $v){
            if(isset($v['relatedTo'])){
                /*
                $relation = $v['relatedTo'];
                if(is_array($relation)){
                    if(isset($relation['table'], $relation['field'])){
                        /*$select[] = '#GROUP_CONCAT(relation' . count($join) . ' .id_2 SEPARATOR \',\') as ' . $k;
                        $where['relation' . count($join) . '.field_1'] = $k;
                        $where['relation' . count($join) . '.field_2'] = $relation['field'];
                        $where['relation' . count($join) . '.model_1'] = Anu::getClassName($this);
                        $where['relation' . count($join) . '.model_2'] = $relation['model'];
                        $join["[>]relation(relation" . count($join) . ")"] = array($this->table . "." . $this->primary_key => 'id_1');*/
                    /*}
                }*/
            }elseif(!isset($v['ignoreInDatabase'])){
                $select[] = $parentTable . "." . $k;
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

            $select = $this->iterateDBSelect($attributes, $this->table);

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
     * @param $element
     * @param $key
     * @param $attributes
     */
    public function changePositions($element, $key,  $attributes){

        //TODO: check for children... this is suspicious <.<
        $children = null;
        if($attributes){
            if(is_array($element->$attributes) && $element->$attributes[0]){
                $parentId = $element->$attributes[0];
                $parent = $this->getElementById($parentId);
                $children = $this->find(['relatedTo' => $parent, $this->primary_key.'[!]' => $element->id], true);
            }
        }

        $where = array();
        if(isset($attributes['relatedField'])){
            $field = $attributes['relatedField'];
            $where[$field] = $element->$field;
        }

        //get Old Position of element
        $oldPosition = anu()->database->get($this->getTable(), 'position', array($this->primary_key => $element->id));
        //Todo: check new Entry
        $newPosition = $element->$key;

        if($oldPosition > $newPosition){
            $where[$key . "[<=]"] = $oldPosition;
            $where[$key . "[>=]"] = $newPosition;
            anu()->database->update($this->getTable(), array("#".$key => "$key+1"), $where);
        }else{
            $where[$key . "[>=]"] = $oldPosition;
            $where[$key . "[<=]"] = $newPosition;
            anu()->database->update($this->getTable(), array("#".$key => "$key-1"), $where);
        }
    }

    /**
     * @param $entry baseModel
     */
    public function renderForm($entry = null){
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
        }

        anu()->template->addJsCode('
            var entry = ' . json_encode($entry) . ';
        ');
        anu()->template->addJsCode('
            var attributes = ' . json_encode($entry->defineAttributes()) . ';
        ');

        return anu()->template->render('forms/index.twig', array(
            'entry' => $entry,
            'attributes' => $entry->defineAttributes()
        ));
    }

    /**
     * @param $entry baseModel
     */
    public function renderList(){
        $className = Anu::getClassName($this);
        $entries = anu()->$className->find();
        $model = Anu::getClassByName($className, "Model", true);
        $attributes = $model->defineAttributes();
        foreach ($entries as $entry){
            $entry->children = array();
        }

        anu()->template->addJsCode('
            var entries = ' . json_encode($entries) . ';
        ');
        anu()->template->addJsCode('
            var attributes = ' . json_encode($attributes) . ';
        ');
        return anu()->template->render('lists/index.twig', array(
            'entries'       => $entries,
            'attributes'    => $attributes,
            'list'          => $className
        ));
    }
}