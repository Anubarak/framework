<?php
namespace Anu;

/**
 * The AttributeType class is an abstract class that defines all of the attribute types that are available in Anu.
 * Attribute types are used primarily in Records and Models.
 *
 * This class is a poor man's version of an enum, since PHP does not have support for native enumerations.
 *
 */
abstract class StructureType
{
    // Constants
    // =========================================================================
    const Matrix = 'matrix';
    const Channel =  'channel';
}
