<?php

namespace WpAutenticarseEnTGD;

use WpAutenticarseEnTGD\Services;
/**
 * Esta es la clase para este plugin.
 */
class Plugin
{
    /**
     * Registramos los tags
     */
    public function addMyRewrites()
    {
        add_rewrite_tag( '%code%', '([^&]+)' );
    }

    /**
     * Modificar la consulta basada en nuestra etiqueta de reescritura.
     */
    public function addTemplateRedirect()
    {
        $code = get_query_var( 'code' );

        if ( !empty( $code ) ) {
            $tgd = new Services\TGDService( $code );
            $user_id = $tgd->getUserByCode( $code );
            $user = get_user_by( 'id', $user_id ); 
            
            if ( $user ) {
                var_dump($user);
                wp_set_current_user( $user_id, $user->user_login );
                wp_set_auth_cookie( $user_id );
                do_action( 'wp_login', $user->user_login );
            }

            wp_redirect( home_url() );
            exit;
        }
    }

    public function init()
    {
        add_action( 'init', array( $this, 'addMyRewrites' ) );
        add_action( 'template_redirect', array( $this, 'addTemplateRedirect' ) );
    }
}