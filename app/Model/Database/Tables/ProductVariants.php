<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for product variants table
 */
enum ProductVariants: string{
    /**
     * Name of the product variants table
     */
    case Table = 'product_variants';
    /**
     * ID of the product variant
     */
    case Id = 'id';
    /**
     * ID of the product this variant belongs to
     */
    case ProductId = 'product_id';
    /**
     * SKU of the product variant
     */
    case Sku = 'sku';
    /**
     * Price of the product variant
     */
    case Price = 'price';
    /**
     * Stock of the product variant
     */
    case Stock = 'stock';
    /**
     * When the product variant was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the product variant was last edited
     */
    case EditedAt = 'edited_at';
    /**
     * When the product variant was deleted
     */
    case DeletedAt = 'deleted_at';
}