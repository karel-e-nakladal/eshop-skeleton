<?php

namespace App\Model\Database\Values;

/**
 * Enumerator with all available token types
 */
enum TokenType: string{
    /**
     * Email verification token
     */
    case Verification = 'VERIFICATION';
    /**
     * Password reset token
     */ 
    case PasswordReset = 'PASSWORDRESET';
    /**
     * Email cancelation token
     */
    case Cancelation = 'CANCELATION';
    /**
     *  Address blocking token
     */
    case AddressBlock = 'ADDRESSBLOCK';
    /**
     *  Two factor authentification token
     */
    case TwoFactor = 'TWOFACTOR';
}