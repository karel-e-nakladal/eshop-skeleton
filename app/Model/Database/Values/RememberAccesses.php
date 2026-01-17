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
     * Partial the user will be remembered for defined time (week)
     */ 
    case Partial = 'PARTIAL';
    /**
     * Blocked the user will be prohibited from logging in on this address
     */ 
    case Blocked = 'BLOCKED';
    /**
     * True the user will be remembered and will not be notified again
     */ 
    case True = 'TRUE';
}