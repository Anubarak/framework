<?php

namespace Anu;

class questionModel extends entryModel
{
    public function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'question_id' => array(AttributeType::Hidden, 'index' => AttributeType::PrimaryKey),
            'test_id' => array(AttributeType::Relation, 'title' => Anu::t('Fragen'), 'relatedTo' => array(
                'model' => 'answer',
                'limit' => 1
            )),
            'answer' => array(AttributeType::Relation, 'title' => Anu::t('Fragen 2'), 'relatedTo' => array(
                'model' => 'answer',
                'limit' => 1
            )),
            'text'          => array(AttributeType::Text, 'min_len' => 5, 'max_len' => 16, 'title' => Anu::t('Text')),
            'correctAnswer' => array(AttributeType::Number),
            'pointsPlus'    => array(AttributeType::Number),
            'pointsMinus'   => array(AttributeType::Number),
            'position'      => array(AttributeType::Position),
            'matrix'        => array(AttributeType::Matrix, 'testMatrix', 'attributes' => array(
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
            ))
        ));
    }
}
    