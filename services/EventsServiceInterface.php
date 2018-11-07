<?php

namespace andreyv\events\services;

interface EventsServiceInterface
{
    /**
     * Fire event
     *
     * @param string $event Event name
     * @param array $data Event data
     * @param string|null $version Event version
     */
    public function fire(string $event, array $data, string $version = null);

    /**
     * Add subscription on event
     *
     * @param string $event Event name
     * @param string $endpoint Event endpoint
     * @param string $method Http method
     * @param string $version|null Event version
     */
    public function subscribe(string $event, string $endpoint, string $method = 'post', string $version = null);

    /**
     * Remove subscription on event
     *
     * @param string $event Event name
     * @param string $endpoint Event endpoint
     */
    public function unsubscribe(string $event, string $endpoint);

    /**
     * Remove versionized subscription on event
     *
     * @param string $event Event name
     * @param string $endpoint Event endpoint
     * @param string $method
     * @param string $version
     */
    public function unsubscribeVersionized(string $event, string $endpoint, $method, string $version);
}
