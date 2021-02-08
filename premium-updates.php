<?php

namespace Cloudways\PremiumUpdates;

use WP_CLI;

if ( ! class_exists( 'WP_CLI' ) ) {
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

$premium_updates = new PremiumUpdates();

WP_CLI::debug( 'Cloudways premium updates package instantiated', PremiumUpdates::DEBUG_GROUP);

WP_CLI::add_wp_hook( 'init', [ $premium_updates, 'register' ] );
