<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 15.08.2017
 * Time: 10:00
 */

namespace Anu;


class testMatrixModel
{
    public function defineAttributes()
    {
        return array(
            'text' => array(
                'text'  => array(AttributeType::Text),
                'headline'  => array(AttributeType::Mixed)
            ),
            'module'    => array(
                'headline'  => array(AttributeType::Mixed)
            )
        );
    }
}