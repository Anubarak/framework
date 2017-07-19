<?php
namespace Anu;


require_once BASE . 'app\anu\core\BaseEnum.php';
abstract class Defaults extends BaseEnum
{
    // Constants
    // =========================================================================

    const currentTimestamp      = 'currentTimestamp';
    const creationTimestamp     = 'creationTimestamp';
    const currentUserId         = 'currentUserId';
}
