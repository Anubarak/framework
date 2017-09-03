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
            'pages' => array(AttributeType::Relation, 'title' => Anu::t('Seiten'), 'relatedTo' => array(
                'table' => 'page',
                'field' => 'page_id',
                'model' => 'page',
                'limit' => 1
            )),
        ));
    }

}
    