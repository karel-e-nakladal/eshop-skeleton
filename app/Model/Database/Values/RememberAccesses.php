<?php

namespace App\Model\Database\Values;

/**
 * Enumerator with all available options for remembering user access
 */
enum RememberAccesses: string{
    /**
     * Default False the address should not be remmembered and the user will be notified again
     */
    case False = 'FALSE';
    /**
     * True the user will be remembered and will not be notified again
     */ 
    case True = 'TRUE';
}