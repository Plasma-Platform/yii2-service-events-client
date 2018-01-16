# yii2-events
Yii2 extension allows developers to easily integrate service-events usage.

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
            'authServerUrl' => 'https://auth.example.com/api/v1/',
            'clientId' => 'clientId',
            'clientSecret' => 'clientSecret',
            'testMode' => YII_ENV_TEST,
        ]
    ]
);

Yii::$container->set(
    'ServiceEventsHttpClient',
    GuzzleHttp\Client::class,
    [
        ['base_uri' => 'https://events.example.com/api/v1/'],
    ]
);

Yii::$container->set(
    andreyv\events\services\EventsServiceInterface::class,
    andreyv\events\services\EventsService::class,
    [
        Yii::$container->get('ServiceEventsHttpClient'),
    ]
);

```
Now you can use Events Service through [DI Container](http://www.yiiframework.com/doc-2.0/guide-concept-di-container.html).
