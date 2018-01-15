<?php

namespace andreyv\events\components;

interface EventsServiceInterface
{
    /**
     * Fire event
     *
     * @param string $event Event name
     * @param array $data Event data
     * @return bool
     */
    public function fire(string $event, array $data);

    /**
     * Add subscription on event
     *
     * @param string $event Event name
     * @param string $endpoint Event endpoint
     * @param string $method Http method
     * @return bool
     */
    public function subscribe(string $event, string $endpoint, string $method = 'post');

    /**
     * Remove subscription on event
     *
     * @param string $event Event name
     * @param string $endpoint Event endpoint
     * @return bool
     */
    public function unsubscribe(string $event, string $endpoint);
}
