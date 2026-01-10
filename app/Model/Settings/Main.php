<?php

namespace App\Model\Settings;

/**
 * Enumerator with main settings of the page
 */
enum Main: string{
    /**
     * Timezone of the application
     */
    case Timezone = 'Europe/Prague';
}