<?php

namespace Cloudways\PremiumUpdates;

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

( new PremiumUpdates() )->register();
