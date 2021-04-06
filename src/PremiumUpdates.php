<?php

namespace Cloudways\PremiumUpdates;

use stdClass;
use WP_CLI;
use WP_Session_Tokens;

final class PremiumUpdates {

    /**
     * Debugging group to use for this package.
     *
     * @var string
     */
    const DEBUG_GROUP = 'premium-updates';

    /**
     * Array of commands to intercept.
     *
     * @var array<array>
     */
    const COMMANDS_TO_INTERCEPT = [
        [ 'plugin', 'list' ],
        [ 'plugin', 'update' ],
    ];

    /**
     * Register the Premium Updates package with WordPress & WP-CLI.
     */
    public function register()
    {
        WP_CLI::debug( 'Registering Cloudways premium updates package...', self::DEBUG_GROUP);

        if ( $this->is_command_to_intercept() ) {

            $this->fake_admin_request();

            WP_CLI::add_wp_hook( 'pre_set_site_transient_update_plugins', [ $this, 'pre_set_site_transient_update_plugins' ] );
        } else {
            WP_CLI::debug( 'Skipping registration of Cloudways premium updates package for this command', self::DEBUG_GROUP);
        }
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

    /**
     * Fake the environment of an administration request running on the WP backend.
     */
    public function fake_admin_request()
    {
        if ( defined( 'WP_ADMIN' ) ) {
            if ( ! WP_ADMIN ) {
                WP_CLI::warning( 'Premium updates package could not fake admin request.' );
            }

            return;
        }

        WP_CLI::debug( 'Faking an admin request', self::DEBUG_GROUP );

        // Define `WP_ADMIN` as being true. This causes the helper method `is_admin()` to return true as well.
        define( 'WP_ADMIN', true );

        // Set a fake entry point to ensure wp-includes/vars.php does not throw notices/errors.
        // This will be reflected in the global `$pagenow` variable being set to 'wp-cli-fake-admin-file.php'.
        $_SERVER['PHP_SELF'] = '/wp-admin/wp-cli-fake-admin-file.php';

        // Bootstrap the WordPress administration area.
        WP_CLI::add_wp_hook( 'init', function () {
            global $wp_db_version, $_wp_submenu_nopriv;

            // Make sure we don't trigger a DB upgrade as that tries to redirect the page.
            $wp_db_version = (int) get_option( 'db_version' );

            // Ensure WP does not iterate over an undefined variable in user_can_access_admin_page().
            if ( ! isset( $_wp_submenu_nopriv ) ) {
                $_wp_submenu_nopriv = [];
            }

            $this->log_in_as_admin_user();

            require_once ABSPATH . 'wp-admin/admin.php';
        }, 0 );
    }

    /**
     * Check whether the current WP-CLI command is amongst those we want to intercept and adapt.
     *
     * As we need to drastically alter the environment under which these commands are run, we want to bail early in case
     * an unrelated command is run.
     *
     * @return bool Whether the current command should be intercepted.
     */
    private function is_command_to_intercept()
    {
        $command = WP_CLI::get_runner()->arguments;

        if ( in_array( $command, self::COMMANDS_TO_INTERCEPT ) ) {
            WP_CLI::debug( 'Detected a command to be intercepted: ' . implode( ' ', $command ), self::DEBUG_GROUP );
            return true;
        }

        return false;
    }

    /**
     * Ensure the current request is done under a logged-in administrator account.
     *
     * A lot of premium plugins/themes have their custom update routines locked behind an is_admin() call.
     */
    private function log_in_as_admin_user()
    {
        $admin_user_id = 1;

        // TODO: Add logic to find an administrator user.

        wp_set_current_user( $admin_user_id );

        $expiration = time() + DAY_IN_SECONDS;
        $manager    = WP_Session_Tokens::get_instance( $admin_user_id );
        $token      = $manager->create( $expiration );

        $_COOKIE[AUTH_COOKIE]        = wp_generate_auth_cookie( $admin_user_id, $expiration, 'auth', $token );
        $_COOKIE[SECURE_AUTH_COOKIE] = wp_generate_auth_cookie( $admin_user_id, $expiration, 'secure_auth', $token );
    }
}
