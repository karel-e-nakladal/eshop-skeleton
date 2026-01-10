<?php

namespace App\Model\Database\Values;

/**
 * Enumerator with all available token types
 */
enum TokenType: string{
    /**
     * Verification token
     */
    case Verification = 'VERIFICATION';
    /**
     * Password reset token
     */ 
    case PasswordReset = 'PASSWORDRESET';
    /**
     * Cancelation token
     */
    case Cancelation = 'CANCELATION';
}