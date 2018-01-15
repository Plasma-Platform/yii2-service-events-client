<?php

namespace andreyv\events\services;

use yii\base\BaseObject;
use yii\web\HttpException;
use yii\base\InvalidConfigException;
use indigerd\oauth2\authfilter\Module as AuthFilter;
use andreyv\events\clients\EventsHttpClientInterface;

class EventsService extends BaseObject implements EventsServiceInterface
{
    /**
     * @var string $scopes Access token scopes
     */
    protected $scopes;

    /**
     * @var string $grantType Access token grant type
     */
    protected $grantType;

    /**
     * @var bool $testMode Allows to skip real API requests for test environment
     */
    protected $testMode;

    /**
     * @var AuthFilter $authFilter Auth filter module
     */
    protected $authFilter;

    /**
     * @var EventsHttpClientInterface $httpClient Events http client
     */
    protected $httpClient;

    /**
     * EventsService constructor.
     * @param string $scopes
     * @param string $grantType
     * @param bool $testMode
     * @param AuthFilter $authFilter
     * @param EventsHttpClientInterface $httpClient
     * @param array $config
     */
    public function __construct(
        string $scopes,
        string $grantType,
        bool $testMode,
        AuthFilter $authFilter,
        EventsHttpClientInterface $httpClient,
        array $config = []
    ) {
        $this->scopes = $scopes;
        $this->grantType = $grantType;
        $this->testMode = $testMode;
        $this->authFilter = $authFilter;
        $this->httpClient = $httpClient;
        parent::__construct($config);
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
        return $this->sendRequest('event-subscriptions/' . $event . '/' . urlencode(trim($endpoint)), [], 'delete');
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
                    'Authorization' => $this->getOauthAccessToken()
                ]
            ]
        );
        return true;
    }

    /**
     * @return string
     * @throws InvalidConfigException
     * @throws HttpException
     */
    protected function getOauthAccessToken()
    {
        $response = $this->authFilter->requestAccessToken('', '', $this->scopes, false, $this->grantType);
        return isset($response['access_token']) ? $response['access_token'] : '';
    }
}
