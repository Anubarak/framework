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
     * @param $baseAttributes array|null
     * @return array
     */
    public function defineAttributes($baseAttributes = null){
        return array(
            'permission_id'           => array(AttributeType::Number),
            'user_id'                 => array(AttributeType::Number),
            'permission'              => array(AttributeType::Mixed),
        );

    }

    /**
     * @param null $index
     * @return array|null
     */
    public function defineIndex($index = null){
        return array(
            'permission_id'   => array(DBIndex::Primary)
        );
    }
}