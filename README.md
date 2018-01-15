# yii2-events
Yii2 component allows developers to easily integrate service-events usage.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require andreyv/yii2-events "^1.0"
```

or add

```
"andreyv/yii2-events": "^1.0"
```

to the require section of your `composer.json` file.

## Usage

Add the following to your `bootstrap.php` file

```php
Yii::$container->setSingleton(
    indigerd\oauth2\authfilter\Module::class,
    indigerd\oauth2\authfilter\Module::class,
    [
        'authFilter',
        null,
        [
            'authServerUrl' => Yii::getAlias('@serviceAuthUrl'),
            'clientId' => 'clientId',
            'clientSecret' => 'clientSecret',
            'testMode' => YII_ENV_TEST
        ]
    ]
);

Yii::$container->set(
    andreyv\events\clients\EventsHttpClientInterface::class,
    andreyv\events\clients\EventsHttpClient::class,
    [
        ['base_uri' => Yii::getAlias('@serviceEventsUrl')],
    ]
);

Yii::$container->set(
    andreyv\events\services\EventsServiceInterface::class,
    andreyv\events\services\EventsService::class,
    [
        'event-scopes',
        'token-grant-type',
        YII_ENV_TEST,
    ]
);

```
Now you can use Events Service through [DI](http://www.yiiframework.com/doc-2.0/guide-concept-di-container.html).
