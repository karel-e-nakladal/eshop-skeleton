<?php

namespace App\Model\Database\Values;

/**
 * Enumerator with all available user roles
 */
enum Roles: string{
    /**
     * Default user role
     */
    case Customer = 'CUSTOMER';
    /**
     * Users who are subscribed or have elevated privilages/better prices
     */ 
    case VipCustomer = 'VIP_CUSTOMER';
    /**
     * Associated user who has their reviews highlighted and have early access + VIP customer rights
     */
    case Curator = 'CURATOR';
    /**
     * User with rights to moderate user content and reviews
     */
    case Moderator = 'MODERATOR';
    /**
     * User with rights to create and edit blog posts
     */
    case Editor = 'EDITOR';
    /**
     * User with rights to manage accounting and finances
     */
    case Accountant = 'ACCOUNTANT';
    /**
     * User with rights to manage items and its amounts in inventory
     */
    case Inventory = 'INVENTORY';
    /**
     * User with rights to manage orders, users, products, content etc.
     */
    case Manager = 'MANAGER';
    /**
     * User with rights to the whole site and its systems
     */
    case Admin = 'ADMIN';
}