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
        add_rewrite_tag( '%state%', '([^&]+)' );
    }

    /**
     * Modificar la consulta basada en nuestra etiqueta de reescritura.
     */
    public function addTemplateRedirect()
    {
        $code  = get_query_var( 'code', null );
        $state = get_query_var( 'state', null );

        if ( ! is_null( $code ) ) {
            $tgd     = new Services\TGDService( $code );
            $user_id = $tgd->getUserByCode( $code );
            $user    = get_user_by( 'id', $user_id );

            // Inicio de sesión en WordPress
            if ( $user ) {
                wp_set_current_user( $user_id, $user->user_login );
                wp_set_auth_cookie( $user_id );
                do_action( 'wp_login', $user->user_login );
            }

            // Comprobamos si inicio sesión desde un popup
            if ( $state === 'popup' ) {
                echo "<script>window.close();</script>"; // Cerramos el popup
            } else {
                wp_redirect( home_url() ); // Redireccionamos
            }

            exit;
        }
    }

    public function init()
    {
        add_action( 'init', array( $this, 'addMyRewrites' ) );
        add_action( 'template_redirect', array( $this, 'addTemplateRedirect' ) );
    }
}