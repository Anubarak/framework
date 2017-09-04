<?php

namespace Anu;

class pageModel extends entryModel
{
    private $data;

    public function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'page_id'       => array(AttributeType::Hidden, 'index' => AttributeType::PrimaryKey),
            'linkName'      => array(AttributeType::Mixed, 'title' => Anu::t('Link Bezeichnung'))
            //'position'          => array(AttributeType::Position, "relatedField" => 'parent_pid')
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
        return StructureType::Matrix;
    }

}
    