<?php

namespace Jiromm\QueueManager;

use Jiromm\QueueManager\Exception\InvalidException;

/**
 * Class Prefix
 * Can be used in QueueManager as an environment identifier to separate production and development queue names
 * @package QueueManager
 */
class Prefix
{
    protected $prefix;
    protected $delimiter;

    public function __construct(string $prefix, $delimiter = '_')
    {
        $pattern = '/[^a-z0-9_-]/i';

        if (preg_match($pattern, $prefix)) {
            throw new InvalidException('Prefix must match the following pattern /[^a-z0-9_-]/i');
        }

        if (preg_match($pattern, $delimiter)) {
            throw new InvalidException('Prefix delimiter must match the following pattern /[^a-z0-9_-]/i');
        }

        $this->prefix = $prefix;
        $this->delimiter = $delimiter;
    }

    public function __toString()
    {
        return $this->prefix . $this->delimiter;
    }
}
