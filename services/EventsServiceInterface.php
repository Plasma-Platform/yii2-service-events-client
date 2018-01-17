<?php

namespace andreyv\events\services;

interface EventsServiceInterface
{
    /**
     * Fire event
     *
     * @param string $event Event name
     * @param array $data Event data
     */
    public function fire(string $event, array $data);

    /**
     * Add subscription on event
     *
     * @param string $event Event name
     * @param string $endpoint Event endpoint
     * @param string $method Http method
     */
    public function subscribe(string $event, string $endpoint, string $method = 'post');

    /**
     * Remove subscription on event
     *
     * @param string $event Event name
     * @param string $endpoint Event endpoint
     */
    public function unsubscribe(string $event, string $endpoint);
}
