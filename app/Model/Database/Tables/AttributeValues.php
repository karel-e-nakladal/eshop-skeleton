<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for attribute values table
 */
enum AttributeValues: string{
    /**
     * Name of the attribute values table
     */
    case Table = 'attribute_values';
    /**
     * ID of the attribute value
     */
    case Id = 'id';
    /**
     * ID of the attribute this value belongs to
     */
    case AttributeId = 'attribute_id';
    /**
     * Value of the attribute
     */
    case Value = 'value';
    /**
     * When the attribute value was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the attribute value was last edited
     */
    case EditedAt = 'edited_at';
    /**
     * When the attribute value was deleted
     */
    case DeletedAt = 'deleted_at';
}