<?php

namespace andreyv\events\components;

use Yii;
use yii\base\Component;
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
        $authFilter = Yii::$app->hasModule($this->authFilterId);
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
     * @inheritdoc
     */
    public function fire(string $event, array $data)
    {
        throw new \Exception('Method does not implemented yet.');
    }

    /**
     * @inheritdoc
     */
    public function subscribe(string $event, string $endpoint, string $method = 'post')
    {
        throw new \Exception('Method does not implemented yet.');
    }

    /**
     * @inheritdoc
     */
    public function unsubscribe(string $event, string $endpoint)
    {
        throw new \Exception('Method does not implemented yet.');
    }
}
