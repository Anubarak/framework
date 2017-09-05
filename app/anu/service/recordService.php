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

    public function getRecordById($id){
        return anu()->database->select('records', '*', array('id' => $id));
    }

    public function getRecordByName($name){
        $record = anu()->database->select('records', '*', array(
            'OR' => array(
                'table_name' => $name,
                'name'      => $name
            )));
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
                    $this->records[] = new $withNameSpace();
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
                    CREATE TABLE `" . $this->record->getTableName() . "` ( " . $this->items . ") ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;"
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
                                    ALTER TABLE `' . $this->record->getTableName() . '`
                                        ADD PRIMARY KEY (`' . $k . '`);
                                ');

                                //anu()->database->debugError();
                                if($errors){
                                    return false;
                                }


                                anu()->database->query('
                                    ALTER TABLE `' . $this->record->getTableName() . '`
                                        MODIFY `' . $k . '` int(11) NOT NULL AUTO_INCREMENT
                                ');

                                //$errors = anu()->database->error();
                                if($errors){
                                    return false;
                                }
                            }
                            if($v === DBIndex::Unique){
                                anu()->database->query('
                                    ALTER TABLE `' . $this->record->getTableName() . '`
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
                    'name' => $this->record->getTableName(),
                    'table_name' => $this->record->getTableName(),
                    'primary_key' => $this->record->getPrimaryKey()
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
            $record = $this->getRecordByName($record);
            if($record){
                $record = $record[0];
            }else{
                return false;
            }
        }

        if(isset($record['id']) && $record['id']){
            $id = $record['id'];
            anu()->database->query('DROP TABLE . ' . $record['table_name']);
            anu()->database->debugError();
            anu()->database->delete('records', array('id' => $id));
            anu()->database->delete('relation', array(
                'OR'    => array(
                   'model_1'    => $record['name'],
                   'model_2'    => $record['name']
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

        anu()->database->dumpTable($record->getTableName());
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
                return false;
            }
        }

        return anu()->database->has('records', array(
            'table_name'   => $record->getTableName()
        ));
    }
}