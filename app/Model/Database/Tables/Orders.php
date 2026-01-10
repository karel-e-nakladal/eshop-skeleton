<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for orders table
 */
enum Orders: string{
    /**
     * Name of the table for orders
     */
    case Table = 'orders';
    /**
     * ID of the order
     */
    case Id = 'id';
    /**
     * ID of the address associated with the order
     */
    case AddressId = 'address_id';
    /**
     * Status of the order (use App\Model\Values\OrderStatus for possible values)
     */
    case Status = 'status';
    /**
     * Total price of the order
     */
    case TotalPrice = 'total_price';
    /**
     * Path to the invoice for the order
     */
    case InvoicePath = 'invoice_path';
    /**
     * When the order was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the order was last edited
     */
    case EditedAt = 'edited_at';
    /**
     * When the order was deleted
     */
    case DeletedAt = 'deleted_at';
}