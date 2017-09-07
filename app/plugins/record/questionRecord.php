<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:54
 */

namespace Anu;


class questionRecord extends entryRecord
{

    /**
     * @param $baseAttributes array|null
     * @return array
     */
    public function defineAttributes($baseAttributes = null){
        return array_merge(array(
            'question_id'   => array(AttributeType::Number, ),
            'text'          => array(AttributeType::Mixed),
            'correctAnswer' => array(AttributeType::Number),
            'pointsPlus'    => array(AttributeType::Number),
            'pointsMinus'   => array(AttributeType::Number),
            'position'  => array(AttributeType::Number),
        ), parent::defineAttributes());
    }

    /**
     * @param null $index
     * @return array|null
     */
    public function defineIndex($index = null){
        return array_merge(array(
            'question_id'   => array(DBIndex::Primary)
        ), parent::defineIndex());
    }

    /**
     * @return string
     */
    public function getTableName(){
        return 'question';
    }

    public function getRecordName(){
        return "Fragen";
    }

    public function getModel()
    {
        return 'question';
    }

    /**
     * Set Type of structure possible fieled = StructureType enum
     * Channel = not sortable all entries in one level
     * Matrix = parent <-> child relation.. are sortable
     *
     * @return string
     */
    public function defineStructure()
    {
        return StructureType::Channel;
    }
}