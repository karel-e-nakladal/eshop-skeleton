<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for access tokens table
 */
enum AccessTokens: string{
    /**
     * Name of the access tokens table
     */
    case Table = 'access_tokens';
    /**
     * ID of the access token
     */
    case Id = 'id';
    /**
     * ID of the users access
     */
    case UserAccess = 'user_access_id';
    /**
     * ID of the access token
     */
    case UserToken = 'user_tokens_id';
}