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
     * @param string|null $method
     * @param string|null $version
     */
    public function unsubscribe(string $event, string $endpoint, $method = null, string $version = null);
}
