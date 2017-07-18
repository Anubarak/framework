<?php

/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 23.06.2017
 * Time: 10:18
 */
namespace Anu;

class pageService extends entryService
{
    protected  $table = 'page';
    protected  $primary_key = 'page_id';

    protected $template = 'pages/index.twig';
}