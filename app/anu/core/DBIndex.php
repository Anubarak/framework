<?php
namespace Anu;

/**
 * The AttributeType class is an abstract class that defines all of the attribute types that are available in Craft.
 * Attribute types are used primarily in Records and Models.
 *
 * This class is a poor man's version of an enum, since PHP does not have support for native enumerations.
 *
 * @author    Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @copyright Copyright (c) 2014, Pixel & Tonic, Inc.
 * @license   http://craftcms.com/license Craft License Agreement
 * @see       http://craftcms.com
 * @package   craft.app.enums
 * @since     1.0
 */
require_once BASE . 'app\anu\core\BaseEnum.php';
abstract class DBIndex extends BaseEnum
{
    // Constants
    // =========================================================================

    const Primary      = 'primary_key';
    const Unique       = 'unique';
}
