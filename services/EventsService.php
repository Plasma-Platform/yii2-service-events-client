<?php

namespace andreyv\events\services;

use yii\web\HttpException;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use GuzzleHttp\Client as HttpClient;
use indigerd\oauth2\authfilter\Module as AuthFilter;

class EventsService implements EventsServiceInterface
{
    const SCOPES = 'events event-subscriptions';

    const GRANT_TYPE = 'client_credentials';

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
        return $this->sendRequest(
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
        return $this->sendRequest(
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
        return $this->sendRequest(
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
     * @return bool
     * @throws HttpException
     * @throws InvalidConfigException
     */
    protected function sendRequest(string $uri, array $params = [], string $method = 'post')
    {
        if ($this->testMode) {
            return true;
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
        return true;
    }

    /**
     * Returns current client access token or generates new token
     *
     * @return string
     * @throws HttpException
     * @throws InvalidConfigException
     */
    protected function getClientAccessToken()
    {
        return $this->accessToken ?? $this->requestClientAccessToken();
    }

    /**
     * @return string
     * @throws InvalidConfigException
     * @throws HttpException
     */
    protected function requestClientAccessToken()
    {
        $response = $this->authFilter->requestAccessToken('', '', self::SCOPES, false, self::GRANT_TYPE);
        if (empty($response['access_token'])) {
            throw new InvalidCallException('Auth service response don\'t have token: ' . json_encode($response));
        }
        $this->accessToken = $response['access_token'];
        return $response['access_token'];
    }
}
