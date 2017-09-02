<?php

namespace Anu;

use Exception;

class comicModel extends entryModel
{
    public function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'comic_id'     => array(AttributeType::Hidden, 'index' => AttributeType::PrimaryKey),
            'text'          => array(AttributeType::Text),
            'matrix'        => array(AttributeType::Matrix, 'testMatrix')
        ));
    }

}
    