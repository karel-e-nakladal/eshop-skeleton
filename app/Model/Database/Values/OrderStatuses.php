<?php

namespace App\Model\Database\Values;

/**
 * Enumerator with all available order statuses
 */
enum OrderStatuses: string{
    /**
     * Default order status
     */
    case Pending = 'PENDING';
    /**
     * Orders that are paid and being processed
     */ 
    case Paid = 'PAID';
    /**
     * Orders that have been shipped to the customer
     */
    case Shipped = 'SHIPPED';
    /**
     * Orders that have been canceled
     */
    case Canceled = 'CANCELED';

}