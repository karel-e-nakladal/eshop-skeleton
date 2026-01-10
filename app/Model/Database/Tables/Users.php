<?php

namespace App\Model\Database\Tables;

/**
 * Enumerator with all available columns for users table
 */
enum Users: string{
    /**
     * Name of the users table
     */
    case Table = 'users';
    /**
     * ID of the user
     */
    case Id = 'id';
    /**
     * Username of the user
     */ 
    case Username = 'username';
    /**
     * Password hash of the user
     */
    case Password = 'password';
    /**
     * First name of the user
     */
    case Firstname = 'firstname';
    /**
     * Last name of the user
     */
    case Lastname = 'lastname';
    /**
     * Email of the user
     */
    case Email = 'email';
    /**
     * Phone number of the user (might be null)
     */
    case Phone = 'phone';
    /**
     * Roles assigned to the user
     */
    case Roles = 'roles';
    /**
     * When the user was created
     */
    case CreatedAt = 'created_at';
    /**
     * When the user was last edited
     */
    case EditedAt = 'edited_at';
    /**
     * When the user was deleted
     */
    case DeletedAt = 'deleted_at';
}