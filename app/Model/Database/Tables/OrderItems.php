<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for order items table
 */
enum OrderItems: string{
    /**
     * Name of the order items table
     */
    case Table = 'order_items';
    /**
     * ID of the order item
     */
    case Id = 'id';
    /**
     * ID of the order associated with the item
     */
    case OrderId = 'order_id';
    /**
     * ID of the product associated with the order
     */
    case ProductId = 'product_id';
    /**
     * ID of the product variant associated with the order
     */
    case ProductVariantId = 'product_variant_id';
    /**
     * Quantity of the product in the order
     */
    case Quantity = 'quantity';
    /**
     * Price of the product at the time of the order
     */
    case Price = 'price';
    /**
     * When the order item was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the order item was last edited
     */
    case EditedAt = 'edited_at';
    /**
     * When the order item was deleted
     */
    case DeletedAt = 'deleted_at';
}