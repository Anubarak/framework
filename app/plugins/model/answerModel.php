<?php

namespace Craft;

use Exception;

class answerModel extends entryModel
{
    private $data;

    public function defineAttributes()
    {
        return array(
            'question_id' => array(AttributeType::Number),
            'answer_id' => array(AttributeType::Number),
            'text'          => array(AttributeType::Mixed),
        );
    }

}
    