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

        return $this->apiClient->callApi('persona');
    }
}
