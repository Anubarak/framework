<?php

/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 23.06.2017
 * Time: 10:18
 */
namespace Anu;

class questionService extends entryService
{
    protected  $table = 'question';
    protected  $primary_key = 'question_id';

    protected $template = 'question/index.twig';
}