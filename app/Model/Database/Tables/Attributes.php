<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for attributes table
 */
enum Attributes: string{
    /**
     * Name of the attributes table
     */
    case Table = 'attributes';
    /**
     * ID of the attribute
     */
    case Id = 'id';
    /**
     * Name of the attribute
     */
    case Name = 'name';
    /**
     * Description of the attribute
     */
    case Description = 'description';
    /**
     * When the attribute was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the attribute was last edited
     */
    case EditedAt = 'edited_at';
    /**
     * When the attribute was deleted
     */
    case DeletedAt = 'deleted_at';
}