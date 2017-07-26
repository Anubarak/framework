<?php

namespace Anu;

class pageModel extends entryModel
{
    private $data;

    public function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'page_id' => array(AttributeType::Hidden, 'index' => AttributeType::PrimaryKey),
            'parent_pid' => array(AttributeType::Relation, 'relatedTo' => array(
                'table' => 'pages',
                'field' => 'page_id',
                'model' => 'page',
            )),
            'linkName'          => array(AttributeType::Mixed),
        ));
    }

}
    