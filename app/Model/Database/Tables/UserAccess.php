<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for user accesses table
 */
enum UserAccesses: string{
    /**
     * Name of the user accesses table
     */
    case Table = 'user_accesses';
    /**
     * ID of the user access
     */
    case Id = 'id';
    /**
     * ID of the user this access belongs to
     */
    case UserId = 'user_id';
    /**
     * Address the user accessed from
     */
    case From = 'from';
    /**
     * If the address should be remembered
     */
    case Remember = 'remember';
    /**
     * When the access happened
     */
    case CreatedAt = 'created_at';
}