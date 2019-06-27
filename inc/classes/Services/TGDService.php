<?php

namespace WpAutenticarseEnTGD\Services;

use WpAutenticarseEnTGD\Services\ApiClient;
use WpAutenticarseEnTGD\Services\OAuthClient;

/**
 * Class TGDService
 */
class TGDService
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var OAuthClient */
    private $oauthClient;

    /** @var ApiClient */
    private $apiClient;
    
    /** @var EntityManager */
    private $em;
    
    public function __construct()
    {
        $params            = get_option('tgd');
        $oauthBaseUri      = $params['oauth_base_uri'];
        $oauthClientId     = $params['oauth_client_id'];
        $oauthClientSecret = $params['oauth_client_secret'];
        $this->redirectUri = $params['oauth_redirect_uri'];

        //Se van a utilizar dos clientes http, uno que maneje el protocolo OAuth, y el otro para llamar a las Apis.
        $this->oauthClient  = new OAuthClient($oauthClientId, $oauthClientSecret, $oauthBaseUri);
        $this->apiClient    = new ApiClient();
        $this->em           = $em;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * TGD lanza esta aplicacion con con code.
     */
    public function getUserByCode($code, $gestor = null)
    {
        $tokenInfo = $this->oauthClient->getTokenByCode($code, $this->redirectUri);

        $this->apiClient->setAccessToken($tokenInfo['access_token']);

        $personaInfo = $this->apiClient->callApi('persona');

        $user = $this->saveOrUpdateUser($personaInfo, $tokenInfo);

        return $user;
    }

   /**
     * Crea o actualiza el usuario con los datos de la persona y los del token.
     *
     * Se deben seleccionar los datos que vienen de la petición de la api de persona.
     * Lo importante es guardar siempre el id de la persona en tgd.
     */
    protected function saveOrUpdateUser($personaInfo, $tokenInfo)
    {
        // Busco si el usuario existe en WordPress
        $user_email = $personaInfo['emails'][0]['email'];
        $cuil       = $personaInfo['cuitCuil'];
        $first_name = $personaInfo['apellidos'];
        $last_name  = $personaInfo['nombres'];
        $user_id    = username_exists( $cuil );

        if ( !$user_id and email_exists($user_email) == false ) {          
            $user_id = wp_insert_user(
                array(
                    'user_login' => $cuil,         // username
                    'user_pass'  => NULL,          // When creating a new user, `user_pass` is expected.
                    'user_email' => $user_email,
                    'nickname'   => $user_email,
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                )
            ) ;

            // Asignamos el rol (TGD) cuando creamos al usuario por primera vez
            wp_update_user( array ('ID' => $user_id, 'role' => 'tgd') ) ;
        } else {
            $user_id = wp_update_user(
                array(
                    'ID'         => $user_id,
                    'nickname'   => $user_email,
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                )
            );
        }

        return $user_id;
    }
}
