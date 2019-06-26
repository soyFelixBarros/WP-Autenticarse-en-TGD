<?php

namespace WpAutenticarseEnTGD\Services;

use OAuth2;

/**
 * Class Tgd service
 */
class Tgd
{
    /** @var OAuthClient */
    private $oauthClient;

    /** @var ApiClient */
    private $apiClient;

    public function __construct($code) {
        $params = get_option('tgd');
        $oauthBaseUri = $params['oauth_base_uri'];
        $oauthClientId = $params['oauth_client_id'];
        $oauthClientSecret = $params['oauth_client_secret'];
        $redirectUri = $params['oauth_redirect_uri'];
        $apiBaseUrl = $params['api_base_url'];

        $this->oauthClient = new OAuth2\Client($oauthClientId, $oauthClientSecret);

        $params = array('code' => $code, 'redirect_uri' => $redirectUri);
        $response = $this->oauthClient->getAccessToken($oauthBaseUri, 'client_credentials', $params);
        $this->oauthClient->setAccessToken($response['result']['access_token']);
        $this->oauthClient->setAccessTokenType(1);

        $parameters = array();
        $response = $this->oauthClient->fetch( $apiBaseUrl.'persona', $parameters );
        var_dump($response, $response['result']);die;
    }
}
