<?php
add_action( 'admin_init', 's_maintain_settings_init' );
function s_maintain_settings_init(  ) {
    register_setting( 's_maintain_authentication', 's_maintain_settings' );
    
    add_settings_section(
        's_maintain_authentication_section',
        __( 'Authentication', 's-maintain' ),
        's_maintain_settings_section_callback',
        's_maintain_authentication'
    );
    
    add_settings_field(
        's_maintain_secret',
        __( 'Secret', 's-maintain' ),
        's_maintain_secret_render',
        's_maintain_authentication',
        's_maintain_authentication_section'
    );

    add_settings_field(
        's_maintain_key',
        __( 'Key', 's-maintain' ),
        's_maintain_key_render',
        's_maintain_authentication',
        's_maintain_authentication_section'
    );
}

function s_maintain_settings_section_callback(  ) {
    ?>
    <p><?php echo __('These are the details that allows S:maintain to communicate with your WordPress site. Copy the keys below and paste them in the settings for this site at your S:maintain dashboard.', 's-maintain'); ?></p>
    <p>
        <strong><?php echo __('Important', 's-maintain'); ?></strong>: <?php echo __('If you change the keys here, remember to also update them in the settings for this site at your S:maintain dashboard.', 's-maintain'); ?>
        <span><?php echo __('And as always, never share your keys or passwords with anyone.', 's-maintain'); ?></span>
    </p>
    <hr>
    <?php
}

function s_maintain_secret_render() {
    require_once( ABSPATH . 'wp-config.php' );
    ?>
    <input id="s-maintain-secret" type='text' style="width: 60%;" value='<?php echo SECURE_AUTH_KEY; ?>' disabled>
    <p class="description" style="font-size: 13px;">
        <span><?php echo __( 'This secret is defined in your wp-config.php file as follows: ', 's-maintain' ); ?></span>
        <span style="font-family: monospace;display: inline-block;font-style: normal;background-color: white;padding: 0 4px;">define('SECURE_AUTH_KEY', 'secure_auth_key');</span><br>
        <span><?php echo __( 'Change your authentication keys every 3-6 months to prevent unauthorized access to your site.', 's-maintain' ); ?></span>
        <span><a href="https://themegrill.com/blog/wordpress-salts-and-security-keys/" target="_blank"><?php echo __( 'Find out how', 's-maintain' ); ?></a></span>
    </p>
    <?php
}

function s_maintain_key_render( ) {
    $options = get_option( 's_maintain_settings' );
    ?>
    <input id="s-maintain-key" type='text' style="width: 60%;" name='s_maintain_settings[s_maintain_key]' value='<?php echo $options['s_maintain_key']; ?>'>
    <button type="button" class="button" style="height: 24px; line-height: 0;" onclick="generateKey(this.previousSibling.previousSibling);">Generate key</button>
    <p class="description" style="font-size: 13px;">
        <span><?php echo __( 'Click the "Generate key" button to create a new key.', 's-maintain' ); ?></span>
    </p>
    <?php
}

add_action( 'admin_menu', 's_maintain_add_admin_menu' );
function s_maintain_add_admin_menu(  ) {
    add_options_page( 'S:maintain', 'S:maintain', 'manage_options', 'settings-api-page', 's_maintain_options_page' );
}

function s_maintain_options_page(  ) {
    ?>
    <form action='options.php' method='post'>
        <h1><?php echo esc_html__( get_admin_page_title(), 's-maintain' ); ?></h1>
        <?php
        settings_fields( 's_maintain_authentication' );
        do_settings_sections( 's_maintain_authentication' );
        ?><hr><?php
        submit_button();
        ?>
    </form>
    
    <script>
    function generateKey(elem) {
        const validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let array = new Uint8Array(64);
        window.crypto.getRandomValues(array);
        array = array.map(x => validChars.charCodeAt(x % validChars.length));
        const randomState = String.fromCharCode.apply(null, array);
        elem.value = randomState;
    }
    </script>
    <?php
}
?>