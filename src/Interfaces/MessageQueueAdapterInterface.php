<?php

namespace Jiromm\QueueManager\Interfaces;

interface MessageQueueAdapterInterface
{
    public function send(string $queue, string $message, int $delay = 0): void;
    public function receive(string $queue, callable $callback);
    public function getMessageCount(string $queue): int;
}
