<?php
namespace Anu;

require_once BASE . 'app\anu\core\BaseEnum.php';
abstract class Permission extends BaseEnum
{
    // Constants
    // =========================================================================

    const Edit          = 'edit';
    const Delete        = 'delete';
    const Update        = 'update';
    const Insert        = 'insert';
    const Read          = 'read';

    const All           = 'all';
    const Admin         = 'admin';
    const LoggedIn      = 'loggedIn';
    const InGroup       = 'inGroup';
    const Author        = 'author';
}
