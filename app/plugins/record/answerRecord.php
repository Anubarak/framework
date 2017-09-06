<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:54
 */

namespace Anu;


class answerRecord extends entryRecord
{

    /**
     * Define Attributes
     */
    public function defineAttributes(){
        return array_merge(array(
            'answer_id'   => array(AttributeType::Number, ),
            'question_id'   => array(AttributeType::Number, ),
            'text'          => array(AttributeType::Mixed),
        ), parent::defineAttributes());
    }

    public function defineIndex(){
        return array_merge(array(
            'answer_id'   => array(DBIndex::Primary)
        ), parent::defineIndex());
    }

    /**
     * @return string
     */
    public function getTableName(){
        return 'answer';
    }

    public function getRecordName(){
        return "Antworten";
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

    public function getModel()
    {
        return 'answer';
    }
}