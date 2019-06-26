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

            $user = $tgd->getUserByCode( $code );

            var_dump( $user );die;
        //     // wp_redirect( home_url() );
        //     // exit;
        }
    }

    public function init()
    {
        add_action( 'init', array( $this, 'addMyRewrites' ) );
        add_action( 'template_redirect', array( $this, 'addTemplateRedirect' ) );
    }
}