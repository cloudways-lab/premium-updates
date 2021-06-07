<?php

namespace Cloudways\PremiumUpdates;

use WP_CLI;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! class_exists( 'WP_CLI' ) ) {
    return;
}

if ( ! class_exists( PremiumUpdates::class ) ) {
    $autoloader = __DIR__ . '/vendor/autoload.php';

    if ( file_exists( $autoloader ) ) {
        require_once $autoloader;
    }

    // Requiring the autoloader will include this very file again, so we bail
    // early here to avoid instantiating and registering the class twice.
    return;
}

WP_CLI::add_hook( 'before_wp_load', static function () {
    ( new PremiumUpdates() )->register();
} );
