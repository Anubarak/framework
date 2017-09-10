<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class baseRecord
{
    public $name = '';
    public $tableName = '';
    public $primary_key;
    public $structure = 'channel';
    public $attributes = array();
    public $baseAttributes = array();
    public $index = array();
    public $id = 0;
    public $handle = '';
    public $date = '';
    public $template = '';

    /**
     * Define aliases for javascript and such
     *
     * baseRecord constructor.
     */
    public function __construct($record = array()){
        $this->name = (isset($record['name']))? $record['name'] : $this->getRecordName();
        $this->tableName = (isset($record['table_name']))? $record['table_name'] : $this->getTableName();
        $this->table_name = (isset($record['table_name']))? $record['table_name'] : $this->getTableName();
        $this->installed = (isset($record['name']))? true : $this->isInstalled();
        $this->primary_key = (isset($record['primary_key']))? $record['primary_key'] : $this->getPrimaryKey();

        if(isset($record['structure'])){
            $this->structure = $record['structure'];
        }elseif(method_exists($this, 'defineStructure')){
            $this->structure = $this->defineStructure();
        }
        if(isset($record['id'])){
            $this->id = $record['id'];
        }

        if(isset($record['date'])){
            $this->date = $record['date'];
        }

        $this->handle = isset($record['handle'])? $record['handle'] : $this->getModel();
        $this->template = isset($record['template'])? $record['template'] : $this->getTemplatePath();
    }

    /**
     * @return string
     */
    public function getTableName(){
        return "";
    }

    /**
     * @param null $baseAttributes
     * @return array
     */
    public function defineAttributes($baseAttributes = null){
        if($this->baseAttributes && !$baseAttributes){
            return $this->baseAttributes;
        }

        if(!$baseAttributes){
            $baseAttributes = array(
                'title'         => array(AttributeType::Mixed),
                'createDate'    => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
                'updateDate'    => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP')
            );
        }

        $this->baseAttributes = array_merge($baseAttributes, anu()->field->getAllFieldsForEntry($this->handle,  false, 'bonus'));
        return $this->baseAttributes;

    }

    /**
     * Index
     *
     * @param null $index
     * @return array|null
     */
    public function defineIndex($index = null){
        if($this->index && !$index){
            return $this->index;
        }
        if(!$index){
            $index = array();
        }
        $this->index = $index;

        return $this->index;
    }

    public function getModel(){
        return "";
    }

    public function getTemplatePath(){
        return "";
    }

    /**
     * @return int|string
     */
    public function getPrimaryKey(){
        $indexes = $this->defineIndex();
        foreach ($indexes as $k  => $v){
            if(in_array(DBIndex::Primary, $v)){
                return $k;
            }
        }
    }

    public function getRecordName(){
        return '';
    }

    public function isInstalled(){
        return anu()->record->isRecordInstalled($this);
    }


    /**
     * @return array
     */
    public function jsonSerialize() {;
        return get_object_vars($this);
    }

    /**
     * Return Attributes for new record entry
     *
     * @return array
     */
    public function defaultRecordAttributes(){
        return array(
            'id'            => array(AttributeType::Hidden),
            'date'          => array(AttributeType::DateTime, 'required' => true),
            'name'          => array(AttributeType::Mixed, 'required' => true),
            'handle'        => array(AttributeType::Mixed, 'required' => true),
            'table_name'    => array(AttributeType::Mixed, 'required' => true),
            'structure'     => array(AttributeType::DropDown, 'required' => true, 'options' => array(
                array(
                    'id' => StructureType::Channel,
                    'label' => 'Kanal'
                ),
                array(
                    'id' => StructureType::Matrix,
                    'label' => 'Matrix'
                )
            )),
            'primary_key'   => array(AttributeType::Mixed, 'required' => true),
            'template'      => array(AttributeType::Mixed, 'required' => true),
        );
    }




    public function populate($data = array()){
        $this->attributes = $this->defaultRecordAttributes();
        foreach ($this->attributes as $k => $v){
            switch ($v[0]){
                case AttributeType::DateTime:
                    $time = isset($data[$k])? $data[$k] : null;
                    $date = new \DateTime($time );
                    $this->$k = $date->format('Y-m-d H:i:s');
                    break;
                default:
                    $this->$k = isset($data[$k])? $data[$k] : '';
                    break;
            }
        }
    }
}