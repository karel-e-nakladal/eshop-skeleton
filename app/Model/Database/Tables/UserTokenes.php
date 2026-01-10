<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for user tokens table
 */
enum UserTokenes: string{
    /**
     * Name of the user tokens table
     */
    case Table = 'user_tokens';
    /**
     * ID of the user token
     */
    case Id = 'id';
    /**
     * ID of the user this token belongs to
     */
    case UserId = 'user_id';
    /**
     * Hash of the token
     */
    case TokenHash = 'token_hash';
    /**
     * Type of the token
     */
    case Type = 'type';
    /**
     * When the token was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the token expires
     */
    case ExpiresAt = 'expires_at';
    /**
     * When the token was used
     */
    case UsedAt = 'used_at';
    /**
     * When the token was deleted
     */
    case DeletedAt = 'deleted_at';
}