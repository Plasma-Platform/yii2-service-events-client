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

Add the following to your config file in `components` section

```php
'components' => [
    ...
    'events' => [
        // Use class
        'class' => 'andreyv\events\components\EventsService',

        //Service events API url
        'serviceEventsUrl' => 'https://events.example.com/api/v1/',

        //Access token scopes
        'scopes' => 'events-scopes',

        //Access token grant type
        'grantType' => 'token-grant-type',

        //Id of auth filter module
        'authFilterId' => 'authfilter',

        //Allows to mute http request exceptions
        'muteExceptions' => true,

        //Allows to skip real API requests for test environment
        'testMode' => false,
    ],
    ...
]
```
