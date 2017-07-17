<?php

namespace Anu;

class questionModel extends entryModel
{
    private $data;

    public function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'question_id' => array(AttributeType::Number, 'index' => AttributeType::PrimaryKey),
            'test_id' => array(AttributeType::Number, 'relatedTo' => array(
                'table' => 'answer',
                'field' => 'answer_id',
                'model' => 'answer',
                'limit' => 1
            )),
            'text'          => array(AttributeType::Mixed),
            'correctAnswer' => array(AttributeType::Number),
            'pointsPlus'    => array(AttributeType::Number),
            'pointsMinus'   => array(AttributeType::Number),
        ));
    }

}
    