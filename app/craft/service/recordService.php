<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:50
 */

namespace Craft;


class recordService
{
    /* @var baseRecord */
    private $record = null;

    public $records = array();
    /**
     * @return array|bool
     */
    public function getAllRecords(){
        return craft()->database->select('records', '*');
    }

    public function getRecordById($id){
        return craft()->database->select('records', '*', array('id' => $id));
    }

    public function getRecordByName($name){
        return craft()->database->select('records', '*', array(
            'OR' => array(
                'table_name' => $name,
                'name'      => $name
            )));
    }


    /**
     * @return array|baseRecord
     */
    public function loadAllRecords(){
        $dirs = array(
            Craft::getRecordPluginDirectory()
        );

        foreach ($dirs as $dir){
            $files = scandir($dir);
            $countFiles = count($files);
            if($countFiles > 2){
                for($i = 2; $i < $countFiles; $i++){
                    $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]);
                    $withNameSpace = 'craft\\' . $withoutExt;
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
    public function installRecord($record){
        if(is_string($record)){
            $className = Craft::getNameSpace() . $record . "Record";
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
                case 'number':
                    $varType = 'int(11)';
                    break;
                case 'mixed':
                    $varType = 'varchar(255)';
                    break;
                case 'datetime':
                    $varType = 'datetime';
                    break;
            }
            $items .= "`" . $k . "` " . $varType . $default . ",";
        }
        $items = substr($items, 0, -1);
        $this->items = $items;
        if($items){
            $success = craft()->database->action(function($database){
                craft()->database->query("
                    CREATE TABLE `" . $this->record->getTableName() . "` ( " . $this->items . ") ENGINE=InnoDB DEFAULT CHARSET=latin1;"
                );
                $errors = craft()->database->error();
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
                            if($v === 'primary_key'){
                                craft()->database->query('
                                    ALTER TABLE `' . $this->record->getTableName() . '`
                                        ADD PRIMARY KEY (`' . $k . '`);
                                ');

                                $errors = craft()->database->error();
                                if($errors){
                                    return false;
                                }


                                craft()->database->query('
                                    ALTER TABLE `' . $this->record->getTableName() . '`
                                        MODIFY `' . $k . '` int(11) NOT NULL AUTO_INCREMENT
                                ');

                                $errors = craft()->database->error();
                                if($errors){
                                    return false;
                                }
                            }
                            if($v === 'unique'){
                                craft()->database->query('
                                    ALTER TABLE `' . $this->record->getTableName() . '`
                                        ADD UNIQUE (`' . $k . '`);
                                ');

                                $errors = craft()->database->error();
                                if($errors){
                                    return false;
                                }
                            }
                        }

                    }
                }
            });
            if($success){
                craft()->database->insert('records', array(
                    'name' => $this->record->getTableName(),
                    'table_name' => $this->record->getTableName()
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
            craft()->database->query('DROP TABLE . ' . $record['table_name']);
            craft()->database->debugError();
            craft()->database->delete('records', array('id' => $id));
            craft()->database->debugError();
            return true;
        }else{
            return false;
        }
    }
}