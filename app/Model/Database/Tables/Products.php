<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for products table
 */
enum Products: string{
    /**
     * Name of the products table
     */
    case Table = 'products';
    /**
     * ID of the product
     */
    case Id = 'id';
    /**
     * Name of the product
     */
    case Name = 'name';
    /**
     * Description of the product
     */
    case Description = 'description';
    /**
     * Thumbnail of the product (reffers to product_images table)
     */
    case Thumbnail = 'thumbnail';
    /**
     * Price of the product
     */
    case Price = 'price';
    /**
     * When the product was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the product was last edited
     */
    case EditedAt = 'edited_at';
    /**
     * When the product was deleted
     */
    case DeletedAt = 'deleted_at';
}