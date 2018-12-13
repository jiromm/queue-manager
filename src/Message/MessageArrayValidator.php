<?php

namespace Jiromm\QueueManager\Message;

use Jiromm\QueueManager\Exception\WrongMessageException;

class MessageArrayValidator
{
    /**
     * @param array $message
     * @throws WrongMessageException
     */
    public function validate(array $message): void
    {
        foreach (['message', 'attempts', 'created_at', 'modified_at'] as $key) {
            if (!isset($message[$key])) {
                throw new WrongMessageException(sprintf('"%s" key is missing from queue message', $key));
            }
        }
    }
}
