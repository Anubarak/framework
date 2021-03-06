<?php
namespace Anu;

/**
 * The AttributeType class is an abstract class that defines all of the attribute types that are available in Anu.
 * Attribute types are used primarily in Records and Models.
 *
 * This class is a poor man's version of an enum, since PHP does not have support for native enumerations.
 *
 */
require_once BASE . 'app\anu\core\BaseEnum.php';
abstract class DBIndex extends BaseEnum
{
    // Constants
    // =========================================================================

    const Primary      = 'primary_key';
    const Unique       = 'unique';
}
