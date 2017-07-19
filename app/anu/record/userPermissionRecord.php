<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:57
 */

namespace Anu;


class userPermissionRecord extends baseRecord
{
    /**
     * @return string
     */
    public function getTableName(){
        return "userPermission";
    }

    /**
     * @return array
     */
    public function defineAttributes(){
        return array(
            'permission_id'           => array(AttributeType::Number),
            'user_id'                 => array(AttributeType::Number),
            'permission'              => array(AttributeType::Mixed),
        );

    }

    /**
     * @return array
     */
    public function defineIndex(){
        return array(
            'permission_id'   => array(DBIndex::Primary)
        );
    }
}