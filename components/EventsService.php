<?php

namespace andreyv\events\components;

use Yii;
use yii\base\Component;
use yii\web\HttpException;
use yii\base\InvalidConfigException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use indigerd\oauth2\authfilter\Module as AuthFilterModule;

class EventsService extends Component implements EventsServiceInterface
{
    /**
     * @var string $serviceEventsUrl Service events API url
     */
    public $serviceEventsUrl;

    /**
     * @var string $scopes Access token scopes
     */
    public $scopes;

    /**
     * @var string $grantType Access token grant type
     */
    public $grantType;

    /**
     * @var string $authFilterId Id of auth filter module
     */
    public $authFilterId = 'authfilter';

    /**
     * @var bool $muteExceptions Allows to mute http request exceptions
     */
    public $muteExceptions = true;

    /**
     * @var bool $testMode Allows to skip real API requests for test environment
     */
    public $testMode = false;

    /**
     * @var AuthFilterModule $authFilter Auth filter module
     */
    protected $authFilter;

    /**
     * @var Client $httpClient Events http client
     */
    protected $httpClient;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty($this->serviceEventsUrl)) {
            throw new InvalidConfigException('Service events API url does not configured.');
        }
        if (!Yii::$app->hasModule($this->authFilterId)) {
            throw new InvalidConfigException('Module "' . $this->authFilterId . '" does not exist.');
        }
        $authFilter = Yii::$app->getModule($this->authFilterId);
        if (!$authFilter instanceof AuthFilterModule) {
            throw new InvalidConfigException('Auth filter module must be instance of ' . AuthFilterModule::class . ".");
        }
        $this->authFilter = $authFilter;
        $this->httpClient = new Client(['base_uri' => $this->serviceEventsUrl]);
    }

    /**
     * Set events http client
     *
     * @param ClientInterface $httpClient
     * @return $this
     */
    public function setHttpClient(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * Set mute exception mode
     *
     * @param bool $muteExceptions
     * @return EventsService
     */
    public function setMuteExceptions(bool $muteExceptions): EventsService
    {
        $this->muteExceptions = $muteExceptions;
        return $this;
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
     * @throws \Exception
     */
    protected function sendRequest(string $uri, array $params = [], string $method = 'post')
    {
        if ($this->testMode) {
            return true;
        }
        try {
            $this->httpClient->{$method}(
                $uri,
                [
                    'form_params' => $params,
                    'headers' => [
                        'Authorization' => $this->getOauthAccessToken()
                    ]
                ]
            );
        } catch (\Exception $e) {
            if (!$this->muteExceptions) {
                throw $e;
            }
            return true;
        }
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
