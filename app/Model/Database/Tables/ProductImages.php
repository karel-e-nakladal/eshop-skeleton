<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for product image table
 */
enum ProductImages: string{
    /**
     * Name of the product images table
     */
    case Table = 'product_images';
    /**
     * ID of the image
     */
    case Id = 'id';
    /**
     * ID of the product this image belongs to
     */
    case ProductId = 'product_id';
    /**
     * Name of the image (used in alt tags and as a display name)
     */
    case Name = 'name';
    /**
     * Path to the image
     */
    case Path = 'path';
    /**
     * Order of the image (used for sorting)
     */
    case Order = 'order';
    /**
     * When the image was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the image was last edited
     */
    case EditedAt = 'edited_at';
    /**
     * When the image was deleted
     */
    case DeletedAt = 'deleted_at';
}