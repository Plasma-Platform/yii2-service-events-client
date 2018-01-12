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
     * @param string $event
     * @param string $endpoint
     * @param string $method
     * @return mixed
     */
    public function subscribe(string $event, string $endpoint, string $method = 'post');

    /**
     * Remove subscription on event
     *
     * @param string $event
     * @param string $endpoint
     * @return mixed
     */
    public function unsubscribe(string $event, string $endpoint);
}
