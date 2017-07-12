<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Craft;


class baseRecord
{
    /**
     * @return array|baseRecord
     */
    public static function getAllRecords(){
        $dirs = array(
            Craft::getRecordPluginDirectory()
        );

        $arrRecords = array();
        foreach ($dirs as $dir){
            $files = scandir($dir);
            $countFiles = count($files);
            if($countFiles > 2){
                for($i = 2; $i < $countFiles; $i++){
                    $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]);
                    $withNameSpace = 'craft\\' . $withoutExt;
                    $arrRecords[] = new $withNameSpace();
                }
            }
        }
        return $arrRecords;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function installRecord(){
        $attributes = $this->defineAttributes();
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
                    CREATE TABLE `" . $this->getTableName() . "` ( " . $this->items . ") ENGINE=InnoDB DEFAULT CHARSET=latin1;"
                );
                $errors = craft()->database->error();
                if($errors){
                    return false;
                }

                $indexes = $this->defineIndex();
                if( $indexes){
                    foreach ($indexes as $k => $v) {
                        if($v[0] === 'primary_key'){
                            craft()->database->query('
                                ALTER TABLE `' . $this->getTableName() . '`
                                    ADD PRIMARY KEY (`' . $k . '`);
                            ');

                            $errors = craft()->database->error();
                            if($errors){
                                return false;
                            }


                            craft()->database->query('
                                ALTER TABLE `' . $this->getTableName() . '`
                                    MODIFY `' . $k . '` int(11) NOT NULL AUTO_INCREMENT
                            ');

                            $errors = craft()->database->error();
                            if($errors){
                                return false;
                            }
                        }
                    }
                }
            });
            if($success){
                craft()->database->insert('records', array(
                     'name' => $this->getTableName(),
                    'table_name' => $this->getTableName()
                ));
            }
        }

        return $success;
    }

    /**
     * @return string
     */
    public function getTableName(){
        return "";
    }

    /**
     * @return array
     */
    public function defineAttributes(){
        return array(
            'createDate'    => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
            'updateDate'   => array(AttributeType::DateTime, 'default' => 'CURRENT_TIMESTAMP'),
        );
    }

    /**
     * @return array
     */
    public function defineIndex(){
        return array();
    }
}