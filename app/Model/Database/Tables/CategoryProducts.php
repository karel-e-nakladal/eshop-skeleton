<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for category products table
 */
enum CategoryProducts: string{
    /**
     * Name of the category products table
     */
    case Table = 'category_products';
    /**
     * ID of the category product
     */
    case Id = 'id';
    /**
     * ID of the category this product belongs to
     */
    case CategoryId = 'category_id';
    /**
     * ID of the product in this category
     */
    case ProductId = 'product_id';
}