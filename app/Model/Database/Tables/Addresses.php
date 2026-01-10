<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for addresses table
 */
enum Addresses: string{
    /**
     * Name of the address table
     */
    case Table = 'addresses';
    /**
     * ID of the address
     */
    case Id = 'id';
    /**
     * ID of the user this address belongs to (may be null for anonymous user)
     */
    case UserId = 'user_id';
    /**
     * First name of the recipient (may be null if the address is for logged user)
     */
    case FirstName = 'first_name';
    /**
     * Last name of the recipient (may be null if the address is for logged user)
     */
    case LastName = 'last_name';
    /**
     * Country of the address
     */
    case Country = 'country';
    /**
     * Street of the address
     */
    case Street = 'street';
    /**
     * City of the address
     */
    case City = 'city';
    /**
     * Zip code of the address
     */
    case ZipCode = 'zip';
    /**
     * Phone number of the recipient (may be null if the address is for logged user)
     */
    case Phone = 'phone';
    /**
     * Email of the recipient (may be null if the address is for logged user)
     */
    case Email = 'email';
    /**
     * When the address was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the address was last edited
     */
    case EditedAt = 'edited_at';
    /**
     * When the address was deleted
     */
    case DeletedAt = 'deleted_at';
}