<?php

namespace WpAutenticarseEnTGD\Forms;

/**
 * Clase para la configruación del plugin.
 * 
 * @since 1.0.0
 */
class Tgd
{
    /**
     * Mantiene los valores a utilizar en los campos de devoluciones de llamada.
     */
    private $options;
    
    /**
     * Titulo para la página de configuración.
     */
    private $titlePage = 'WP Autenticarse en TGD';

    /**
     * Titulo para el menu.
     */
    private $titleMenu = 'Autenticarse en TGD';

    /**
     * Comenzar
     */
    public function __construct()
    {
        $this->options = get_option('tgd');
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Añadir página de submenú al menú de configuración.
     */
    public function admin_menu()
    {
        $titleMenu = $this->title;
        add_options_page(
            $this->titlePage,
            $this->titleMenu,
            'manage_options', 
            'wp-autenticarse-en-tgd', // $menu_slug
            array(
                $this,
                'settings_page'
            )
        );
    }

    /**
     * Este es el contenido de la página.
     */
    public function settings_page()
    {
        ?>
        <div class="wrap">
            <h1><?php echo $this->titlePage; ?></h1>
            <form method="post" action="options.php">
            <?php
                // Esto imprime todos los campos de configuración ocultos
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Registrar y añadir ajustes
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group',
            'tgd',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'oauth_section_id',
            'Ingrese los datos para autenticarse con OAuth2:',
            null,
            'my-setting-admin'
        );

        add_settings_field(
            'oauth_client_id', 
            'CLIENT ID', 
            array( $this, 'tgd_oauth_client_id_callback' ), 
            'my-setting-admin', 
            'oauth_section_id'
        );
        
        add_settings_field(
            'oauth_client_secret', 
            'SECRET', 
            array( $this, 'tgd_oauth_client_secret_callback' ), 
            'my-setting-admin', 
            'oauth_section_id'
        ); 

        add_settings_field(
            'oauth_base_uri', 
            'URL Base', 
            array( $this, 'tgd_oauth_base_uri_callback' ), 
            'my-setting-admin', 
            'oauth_section_id'
        );

        add_settings_field(
            'oauth_redirect_uri', 
            'URL Redireccionamiento', 
            array( $this, 'tgd_oauth_redirect_uri_callback' ), 
            'my-setting-admin', 
            'oauth_section_id'
        );

        add_settings_section(
            'api_section_id',
            'Ingrese los datos de la API:',
            null,
            'my-setting-admin'
        );
        
        add_settings_field(
            'api_base_url', 
            'URL base', 
            array( $this, 'tgd_api_base_url_callback' ), 
            'my-setting-admin', 
            'api_section_id'
        );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if ( isset( $input['oauth_client_id'] ) )
            $new_input['oauth_client_id'] = sanitize_text_field( $input['oauth_client_id'] );
        
        if ( isset( $input['oauth_client_secret'] ) )
            $new_input['oauth_client_secret'] = sanitize_text_field( $input['oauth_client_secret'] );
        
        if ( isset( $input['oauth_base_uri'] ) )
            $new_input['oauth_base_uri'] = esc_url_raw( $input['oauth_base_uri'] );

        if ( isset( $input['oauth_redirect_uri'] ) )
            $new_input['oauth_redirect_uri'] = sanitize_text_field( $input['oauth_redirect_uri'] );
        
        if ( isset( $input['api_base_url'] ) )
            $new_input['api_base_url'] = esc_url_raw( $input['api_base_url'] );

        return $new_input;
    }

    public function tgd_oauth_client_id_callback()
    {
        printf(
            '<input type="text" id="oauth_client_id" name="tgd[oauth_client_id]" value="%s" class="regular-text" />',
            isset( $this->options['oauth_client_id'] ) ? esc_attr( $this->options['oauth_client_id']) : ''
        );
    }

    public function tgd_oauth_client_secret_callback()
    {
        printf(
            '<input type="text" id="oauth_client_secret" name="tgd[oauth_client_secret]" value="%s" class="regular-text" />',
            isset( $this->options['oauth_client_secret'] ) ? esc_attr( $this->options['oauth_client_secret']) : ''
        );
    }
    
    public function tgd_oauth_base_uri_callback()
    {
        printf(
            '<input type="text" id="oauth_base_uri" name="tgd[oauth_base_uri]" value="%s" class="regular-text" />',
            isset( $this->options['oauth_base_uri'] ) ? esc_attr( $this->options['oauth_base_uri']) : ''
        );
    }
    
    public function tgd_oauth_redirect_uri_callback()
    {
        printf(
            '<input type="text" id="oauth_redirect_uri" name="tgd[oauth_redirect_uri]" value="%s" class="regular-text"/>',
            isset( $this->options['oauth_redirect_uri'] ) ? esc_attr( $this->options['oauth_redirect_uri']) : ''
        );
    }

    public function tgd_api_base_url_callback()
    {
        printf(
            '<input type="text" id="api_base_url" name="tgd[api_base_url]" value="%s" class="regular-text" />',
            isset( $this->options['api_base_url'] ) ? esc_attr( $this->options['api_base_url']) : ''
        );
    }
}