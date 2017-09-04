<?php

namespace Anu;

use Exception;

class answerModel extends entryModel
{
    public function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'question_id' => array(AttributeType::Number),
            'answer_id' => array(AttributeType::Hidden, 'index' => AttributeType::PrimaryKey),
            'text'          => array(AttributeType::Mixed),
        ));
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
    