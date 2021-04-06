<?php

namespace Cloudways\PremiumUpdates;

use stdClass;
use WP_CLI;

final class PremiumUpdates {

    const DEBUG_GROUP = 'premium-updates';

    /**
     * Register the Premium Updates package with WordPress & WP-CLI.
     */
    public function register()
    {
        \add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'pre_set_site_transient_update_plugins' ] );

        WP_CLI::debug( 'Cloudways premium updates package registered', self::DEBUG_GROUP);
    }

    /**
     * The site transient "update_plugins" is about to be set to a new value.
     *
     * @param stdClass|null $transient Value that the "update_plugins" site transient is about to be set to.
     */
    public function pre_set_site_transient_update_plugins( $transient )
    {
        WP_CLI::debug(
            'About to set site transient "update_plugins" - ' . $this->dump_update_plugins_transient( $transient ),
            self::DEBUG_GROUP
        );

        return $transient;
    }

    /**
     * Dump the "update_plugins" site transient for debugging purposes.
     *
     * @param stdClass|null $transient "update_plugins" site transient to dump.
     * @return string Debugging dump of the "update_plugins" site transient.
     */
    private function dump_update_plugins_transient( $transient )
    {
        if ( null === $transient ) {
            return '<null>';
        }

        $output = [];

        if ( isset( $transient->last_checked ) ) {
            $output[] = '%ylast_checked%n: ' . $transient->last_checked;
        }

        foreach ( [ 'response', 'no_update' ] as $entry ) {
            if ( isset( $transient->$entry ) ) {
                $entry_plugins = [];

                foreach ( $transient->$entry as $entry_plugin ) {
                    $entry_plugins[] = $entry_plugin->slug;
                }

                $output[] = "%y{$entry}%n: " . implode( ', ', $entry_plugins );
            }
        }

        return WP_CLI::colorize( implode( '; ', $output ) );
    }
}
