<?php

namespace App\Model\Database\Values;

/**
 * Enumerator with all available options for user two factor authentification
 */
enum UserTwoFactor: string{
    /**
     * Default False the account wont be prompted to authenticate via email
     */
    case False = 'FALSE';
    /**
     * True the account will be prompted to authenticate via email
     */ 
    case True = 'TRUE';
}