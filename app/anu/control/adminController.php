<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 02.08.2017
 * Time: 16:59
 */

namespace Anu;

require_once __DIR__ . "/baseController.php";
class adminController extends baseController
{
    /**
     *
     */
    public function getContent(){
        anu()->record->installRecord('fieldLayout', false);
        anu()->template->render('admin/pages/home.twig', array(
            'title'       => "test Title",
            'headline'    => "headline",
            'subheadline' => "subline",
            'navigation'  => "test",
        ));
    }
}