<?php

namespace Anu;

use Exception;

class answerModel extends entryModel
{
    private $data;

    public function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'question_id' => array(AttributeType::Number),
            'answer_id' => array(AttributeType::Number),
            'text'          => array(AttributeType::Mixed),
        ));
    }

}
    