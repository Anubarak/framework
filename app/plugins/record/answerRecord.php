<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:54
 */

namespace Anu;


class answerRecord extends baseRecord
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
            'answer_id'   => array('primary_key')
        ), parent::defineIndex());
    }

    /**
     * @return string
     */
    public function getTableName(){
        return 'answer';
    }

}