<?php

namespace WpAutenticarseEnTGD\Services;

use GuzzleHttp\Client;

class ApiClient
{
    /** @var Client */
    private $httpClient;

    /** @var  */
    private $accessToken;

    public function __construct()
    {
        $params     = get_option('tgd');
        $apiBaseUri = $params['api_base_url'];

        $this->httpClient  = new Client(array('base_uri' => $apiBaseUri));
        $this->accessToken = null;
    }

    /**
     * Set accessToken
     *
     * @param string $accessToken Access token
     * @return $this
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Call Api
     *
     * @param string $uri    Uri.
     * @param array  $query  key => value array to build query string.
     * @param string $method Http Method.
     * @param array  $body   key => value array to build a json body.
     *
     * @return array
     */
    public function callApi($uri, $query = null, $method = 'GET', $body = null)
    {
        $accessToken = $this->getAccessToken();

        $options = array();
        if ($accessToken) {
            $options = array('headers' => array('Authorization' =>'Bearer ' . $accessToken));
        }

        if (!empty($body)) {
            $options['json'] = $body;
        }

        if (is_array($query)) {
            $options = array_merge($options, array('query' => $query));
        }

        $response = $this->httpClient->request($method, $uri, $options);
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }
}
