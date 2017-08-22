<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 16:39
 */

namespace Anu;


class matrixService extends entryService
{
    protected   $table = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;


    public function init(){
        $class = Anu::getClassByName($this, "Record", true);
        $this->table = $class->getTableName();
        $this->primary_key = $class->getPrimaryKey();
    }


    /**
     * @param $matrix            matrixModel
     * @return bool|int
     */
    public function saveMatrix($matrix){
        return $this->saveEntry($matrix);
    }

    /**
     * @param $matrixId
     * @return baseModel|null
     */
    public function getMatrixById($matrixId){
        return $this->getEntryById($matrixId);
    }

    public function getMatrixByName($name){
        $matrix = Anu::getClassByName($name, "Model", true);
        return $matrix;
    }
}