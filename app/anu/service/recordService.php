<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:50
 */

namespace Anu;


class recordService
{
    /* @var baseRecord */
    private $record = null;

    public $records = array();


    /**
     * @return array|bool
     */
    public function getAllRecords($returnModel = false){
        if(!$returnModel){
            return anu()->database->select('records', '*');
        }
        $records = anu()->database->select('records', '*');
        $response = array();
        foreach ($records as $record){
            $response[] = new baseRecord($record);
        }
        return $response;
    }

    /**
     * return record array by Id
     *
     * @param $id
     * @return null
     */
    public function getRecordById($id){
        if($record = anu()->database->select('records', '*', array('id' => $id))){
            return new baseRecord($record[0]);
        }
        return null;
    }

    /**
     * Returns a record
     *
     * @param $name
     * @param $returnModel
     * @return baseRecord|null
     */
    public function getRecordByName($name, $returnModel = false){
        $record = anu()->database->select('records', '*', array(
            'OR' => array(
                'table_name' => $name,
                'name'      => $name
            )));
        if($returnModel && count($record)){
            return new baseRecord($record[0]);
        }
        return count($record)? $record[0] : null;
    }


    /**
     * @return array|baseRecord
     */
    public function loadAllRecords(){
        $dirs = array(
            Anu::getRecordPluginDirectory()
        );

        foreach ($dirs as $dir){
            $files = scandir($dir);
            $countFiles = count($files);
            if($countFiles > 2){
                for($i = 2; $i < $countFiles; $i++){
                    $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]);
                    $withNameSpace = Anu::getNameSpace() . $withoutExt;
                    if(class_exists($withNameSpace)){
                        $this->records[] = new $withNameSpace();
                    }
                }
            }
        }
        return $this->records;
    }


    /**
     * @param $record       baseRecord | string
     * @return bool
     */
    public function installRecord($record, $ignoreRecordTable = false){
        if(is_string($record)){
            $className = Anu::getNameSpace() . $record . "Record";
            if(class_exists($className)){
                $record = new $className();
            }else{
                return false;
            }
        }

        $attributes = $record->defineAttributes();
        $this->record = $record;
        $items = '';
        $success = false;
        foreach ($attributes as $k => $v){
            $default = ' DEFAULT NULL';
            if(isset($v['default'])){
                $default = " DEFAULT " . $v['default'];
            }
            $varType = '';
            switch ($v[0]){
                case AttributeType::Number:
                    $varType = 'int(11)';
                    break;
                case AttributeType::Mixed:
                    $varType = 'varchar(255)';
                    break;
                case AttributeType::DateTime:
                    $varType = 'datetime';
                    break;
                case AttributeType::Text:
                    $varType = 'text';
                    break;
                case AttributeType::Bool:
                    $varType = 'BOOLEAN';
                    break;
            }
            $items .= "`" . $k . "` " . $varType . $default . ",";
        }
        $items = substr($items, 0, -1);
        $this->items = $items;
        if($items){
            $success = anu()->database->action(function($database){
                anu()->database->query("
                    CREATE TABLE `" . $this->record->tableName . "` ( " . $this->items . ") ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;"
                );

                $errors = anu()->database->error();
                if($errors){
                    return false;
                }

                $indexes = $this->record->defineIndex();
                if( $indexes){
                    foreach ($indexes as $k => $rows) {
                        if(!is_array($rows)){
                            $rows = array($rows);
                        }
                        foreach ($rows as $v){
                            if($v === DBIndex::Primary){
                                anu()->database->query('
                                    ALTER TABLE `' . $this->record->tableName . '`
                                        ADD PRIMARY KEY (`' . $k . '`);
                                ');

                                //anu()->database->debugError();
                                if($errors){
                                    return false;
                                }


                                anu()->database->query('
                                    ALTER TABLE `' . $this->record->tableName . '`
                                        MODIFY `' . $k . '` int(11) NOT NULL AUTO_INCREMENT
                                ');

                                //$errors = anu()->database->error();
                                if($errors){
                                    return false;
                                }
                            }
                            if($v === DBIndex::Unique){
                                anu()->database->query('
                                    ALTER TABLE `' . $this->record->tableName . '`
                                        ADD UNIQUE (`' . $k . '`);
                                ');

                                //$errors = anu()->database->error();
                                if($errors){
                                    return false;
                                }
                            }
                        }

                    }
                }
            });
            //anu()->database->debugError();
            if($success && !$ignoreRecordTable){
                anu()->database->insert('records', array(
                    'name'          => $this->record->name,
                    'table_name'    => $this->record->tableName,
                    'primary_key'   => $this->record->primary_key,
                    'model'         => $this->record->model,
                    'structure'     => $this->record->structure
                ));
            }
        }

        return $success;
    }

    /**
     * @param $record array
     */
    public function deleteRecord($record){
        if(is_string($record)){
            if(!$record = $this->getRecordByName($record, true)){
                throw new \Exception(Anu::parse("Could not find Record"));
            };

        }
        /**@var baseRecord $record */
        if(property_exists($record, 'id') && $record->id){
            $id = $record->id;
            anu()->database->dumpTable($record->tableName);
            anu()->database->query('DROP TABLE . ' . $record->tableName);
            anu()->database->debugError();
            anu()->database->delete('records', array('id' => $id));
            anu()->database->delete('relation', array(
                'OR'    => array(
                   'model_1'    => $record->tableName,
                   'model_2'    => $record->tableName
                )
            ));
            //anu()->database->debugError();
            return true;
        }else{
            return false;
        }
    }

    public function dumpRecord($record){
        if(is_string($record)){
            $className = Anu::getNameSpace() . $record . "Record";
            if(class_exists($className)){
                $record = new $className();
            }else{
                return false;
            }
        }

        anu()->database->dumpTable($record->tableName);
    }

    /**
     * @param $record   string|baseRecord
     * @return bool
     */
    public function isRecordInstalled($record){
        if(is_string($record)){
            $className = Anu::getNameSpace() . $record . "Record";
            if(class_exists($className)){
                $record = new $className();
            }else{
                $record = anu()->record->getRecordByName($record, true);
            }
        }

        return anu()->database->has('records', array(
            'table_name'   => $record->tableName
        ));
    }
}