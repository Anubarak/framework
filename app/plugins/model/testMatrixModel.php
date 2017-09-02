<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 15.08.2017
 * Time: 10:00
 */

namespace Anu;


class testMatrixModel extends baseModel
{
    public function defineAttributes()
    {
        return array(
            'text' => array(
                'headline'  => array(AttributeType::Mixed),
                'text'  => array(AttributeType::Text)
            ),
            'module'    => array(
                'headline'  => array(AttributeType::Mixed),
                'test_id'   => array(AttributeType::Relation, 'title' => Anu::t('Fragen'), 'relatedTo' => array(
                    'table' => 'answer',
                    'field' => 'answer_id',
                    'model' => 'answer'
                )),
            ),
            'boolean'   => array(
                'headline'  => array(AttributeType::Mixed),
                'checkbox'  => array(AttributeType::Bool),
            ),
            'comic'    => array(
                'headline'  => array(AttributeType::Mixed),
                'comic'   => array(AttributeType::Relation, 'title' => Anu::t('comic'), 'relatedTo' => array(
                    'table' => 'comic',
                    'field' => 'comic_id',
                    'model' => 'comic'
                )),
            ),
        );
    }
}