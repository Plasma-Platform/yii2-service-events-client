<?php

namespace andreyv\events\services;

use yii\web\HttpException;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use GuzzleHttp\Client as HttpClient;
use indigerd\oauth2\authfilter\Module as AuthFilter;

class EventsService implements EventsServiceInterface
{
    /**
     * @var string $scopes OAuth token scopes
     */
    protected static $scopes = 'events event-subscriptions';

    /**
     * @var string $grantType OAuth token grant type
     */
    protected static $grantType = 'client_credentials';

    /**
     * @var bool $testMode Allows to skip real API requests for test environment
     */
    protected $testMode;

    /**
     * @var AuthFilter $authFilter Auth filter module
     */
    protected $authFilter;

    /**
     * @var HttpClient $httpClient Events http client
     */
    protected $httpClient;

    /**
     * @var string $accessToken Oauth access token
     */
    protected $accessToken;

    /**
     * @param HttpClient $httpClient
     * @param AuthFilter $authFilter
     * @param bool $testMode
     */
    public function __construct (HttpClient $httpClient, AuthFilter $authFilter, bool $testMode = false )
    {
        $this->httpClient = $httpClient;
        $this->authFilter = $authFilter;
        $this->testMode = $testMode;
    }

    /**
     * @inheritdoc
     */
    public function fire(string $event, array $data)
    {
        $this->sendRequest(
            'events',
            [
                'name' => $event,
                'data' => $data,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function subscribe(string $event, string $endpoint, string $method = 'post')
    {
        $this->sendRequest(
            'event-subscriptions',
            [
                'event' => $event,
                'endpoint' => $endpoint,
                'method' => $method,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function unsubscribe(string $event, string $endpoint)
    {
        $this->sendRequest(
            'event-subscriptions/' . $event . '/' . urlencode(trim(trim($endpoint), '/')),
            [],
            'delete'
        );
    }

    /**
     * Send http request
     *
     * @param string $uri
     * @param array $params
     * @param string $method
     * @throws HttpException
     * @throws InvalidConfigException
     */
    protected function sendRequest(string $uri, array $params = [], string $method = 'post')
    {
        if ($this->testMode) {
            return;
        }
        $this->httpClient->{$method}(
            $uri,
            [
                'form_params' => $params,
                'headers' => [
                    'Authorization' => $this->getClientAccessToken()
                ]
            ]
        );
    }

    /**
     * Return current client access token or generate new token
     *
     * @return string
     * @throws HttpException
     * @throws InvalidConfigException
     */
    protected function getClientAccessToken(): string
    {
        return $this->accessToken ?? $this->requestClientAccessToken();
    }

    /**
     * Request new OAuth client access token
     *
     * @return string
     * @throws InvalidConfigException
     * @throws HttpException
     */
    protected function requestClientAccessToken(): string
    {
        $response = $this->authFilter->requestAccessToken('', '', self::$scopes, false, self::$grantType);
        if (empty($response['access_token'])) {
            throw new InvalidCallException('Auth service response does not have token: ' . json_encode($response));
        }
        $this->accessToken = $response['access_token'];
        return $response['access_token'];
    }
}
