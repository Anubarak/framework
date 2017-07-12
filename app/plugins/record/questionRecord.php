<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 11.07.2017
 * Time: 15:54
 */

namespace Craft;


class questionRecord extends baseRecord
{

    /**
     * Define Attributes
     */
    public function defineAttributes(){
        return array_merge(array(
            'question_id'   => array(AttributeType::Number, ),
            'text'          => array(AttributeType::Mixed),
            'correctAnswer' => array(AttributeType::Number),
            'pointsPlus'    => array(AttributeType::Number),
            'pointsMinus'   => array(AttributeType::Number),
        ), parent::defineAttributes());
    }

    public function defineIndex(){
        return array(
            'question_id'   => array('primary_key')
        );
    }

    /**
     * @return string
     */
    public function getTableName(){
        return 'question';
    }

}