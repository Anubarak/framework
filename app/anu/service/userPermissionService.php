<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 16:39
 */

namespace Anu;


class userPermissionService extends baseService
{

    protected   $table = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = null;


    public function init(){
        $class = Anu::getClassByName($this, "Record", true);
        $this->table = $class->getTableName();
        $this->primary_key = $class->getPrimaryKey();
    }


    /**
     * @param $userPermission       userModel
     * @return mixed
     */
    public function saveUser($userPermission){
        return $this->saveElement($userPermission);
    }

    /**
     * @param $userId
     * @return baseModel|null
     */
    public function getUserPermissionById($userPermissionId){
        return $this->getElementById($userPermissionId);
    }
}