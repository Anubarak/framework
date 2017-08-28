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
abstract class AttributeType extends BaseEnum
{
    // Constants
    // =========================================================================

    const Mixed      = 'mixed';
    const Bool       = 'bool';
    const Number     = 'number';
    const Position   = 'position';
    const Text       = 'text';
    const JSON       = 'json';
    const Relation   = 'relation';
    const Hidden     = 'hidden';
    const Matrix     = 'matrix';

    const ClassName  = 'classname';
    const DateTime   = 'datetime';
    const Email      = 'email';
    const Enum       = 'enum';
    const Handle     = 'handle';
    const Locale     = 'locale';
    const Name       = 'name';



    const Slug       = 'slug';
    const SortOrder  = 'sortorder';
    const String     = 'string';
    const Template   = 'template';
    const Url        = 'url';
    const UrlFormat  = 'urlformat';
    const Uri        = 'uri';


    const PrimaryKey = 'primarykey';
    const File       = 'file';
    const Password      = 'password';
    const NewPassword   = 'newpassword';
}
