<?php

class PDNotion_Admin {

    private static $instance = null; // Singleton instance.

    // Get the single instance of the class.
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Private constructor to prevent direct instantiation.
    private function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    // Add the settings page.
    public function add_settings_page() {
        add_options_page(
            'PDNotion Settings',
            'PDNotion',
            'manage_options',
            'pdnotion',
            [ $this, 'render_settings_page' ]
        );
    }

    // Register the plugin settings.
    public function register_settings() {
        register_setting( 'pdnotion_settings', 'pdnotion_api_key' );
        register_setting( 'pdnotion_settings', 'pdnotion_database_id' );
    }

    // Render the settings page.
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>PDNotion Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'pdnotion_settings' );
                do_settings_sections( 'pdnotion_settings' );
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">API Key</th>
                        <td><input type="text" name="pdnotion_api_key" value="<?php echo esc_attr( get_option( 'pdnotion_api_key' ) ); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Database ID</th>
                        <td><input type="text" name="pdnotion_database_id" value="<?php echo esc_attr( get_option( 'pdnotion_database_id' ) ); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
