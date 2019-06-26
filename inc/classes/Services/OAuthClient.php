<?php

namespace WpAutenticarseEnTGD\Services;

use GuzzleHttp\Client;

class OAuthClient
{
    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var Client */
    private $httpClient;

    public function __construct($clientId, $clientSecret, $baseUri)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->httpClient   = new Client(array('base_uri' => $baseUri));
    }

    /**
     * Obtención de token a traves de code
     */
    public function getTokenByCode($code, $redirectUri)
    {
        $query = array(
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $redirectUri,
            'code'          => $code,
        );

        return $this->call($query, 'token');
    }

    public function getTokenByRefresh($refreshToken)
    {
        $query = array(
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
        );

        return $this->call($query, 'token');
    }

    public function getClientCredentialsToken()
    {
        $query = array(
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'client_credentials',
        );

        return $this->call($query, 'token');
    }

    private function call($query, $uri, $method = 'GET')
    {
        $options = array('query' => $query);

        if ($method == 'POST') {
            $options = array('form_params' => $query);
        }

        $response = $this->httpClient->request($method, $uri, $options);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    }
}
