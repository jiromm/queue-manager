<?php

namespace Jiromm\QueueManager\Message;

use Jiromm\QueueManager\Exception\DatetimeException;
use Jiromm\QueueManager\Exception\WrongMessageException;

class Job implements JobInterface, \Serializable
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    protected $message;
    protected $attempts;
    protected $createdAt;
    protected $modifiedAt;
    protected $meta;

    public function __construct(string $message = '', int $attempts = 1, string $createdAt = '', array $meta = [])
    {
        if ($attempts < 1) {
            throw new WrongMessageException('Attempt value cannot be less than 1');
        }

        $createdAt = $createdAt ?? date(self::DATE_FORMAT);

        $this->message = $message;
        $this->attempts = $attempts;
        $this->meta = $meta;

        try {
            $this->createdAt = new \DateTime($createdAt);
            $this->modifiedAt = new \DateTime();
        } catch (\Exception $e) {
            throw new DatetimeException($e->getMessage());
        }
    }

    public function serialize(): string
    {
        return Packer::pack(
            json_encode([
                'attempts' => $this->getAttempts(),
                'message' => $this->getMessage(),
                'created_at' => $this->getCreatedAt(),
                'modified_at' => $this->getModifiedAt(),
                'meta' => $this->getMeta(),
            ])
        );
    }

    public function unserialize($serialized)
    {
        $message = json_decode(
            Packer::unpack($serialized),
            true
        );

        $this->build($message);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format(self::DATE_FORMAT);
    }

    public function getModifiedAt(): string
    {
        return $this->modifiedAt->format(self::DATE_FORMAT);
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function increaseAttempts(): void
    {
        $this->attempts++;
    }

    public function decreaseAttempts(): void
    {
        if ($this->attempts > 1) {
            $this->attempts--;
        }
    }

    public function resetAttempts(): void
    {
        $this->attempts = 1;
    }

    private function build(array $message): void
    {
        $validator = new MessageArrayValidator();
        $validator->validate($message);

        $this->message = $message['message'];
        $this->attempts = (int)$message['attempts'];
        $this->meta = $message['meta'] ?? [];

        try {
            $this->createdAt = new \DateTime($message['created_at']);
            $this->modifiedAt = new \DateTime();
        } catch (\Exception $e) {
            throw new DatetimeException($e->getMessage());
        }
    }
}
