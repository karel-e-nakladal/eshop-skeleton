<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for categories table
 */
enum Categories: string{
    /**
     * Name of the categories table
     */
    case Table = 'categories';
    /**
     * ID of the category
     */
    case Id = 'id';
    /**
     * Name of the category
     */
    case Name = 'name';
    /**
     * Description of the category
     */
    case Description = 'description';
    /**
     * Parent ID of the category (for nested categories, might be null)
     */
    case ParentId = 'parent_id';
    /**
     * When the category was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the category was last edited
     */
    case EditedAt = 'edited_at';
    /**
     * When the category was deleted
     */
    case DeletedAt = 'deleted_at';
}