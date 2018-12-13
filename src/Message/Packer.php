<?php

namespace Jiromm\QueueManager\Message;

class Packer
{
    private const COMPRESSION_LEVEL = 9;
    private const ENCODING = ZLIB_ENCODING_DEFLATE;

    public static function pack(string $payload): string
    {
        return base64_encode(gzcompress($payload, self::COMPRESSION_LEVEL, self::ENCODING));
    }

    public static function unpack(string $compressedPayload): string
    {
        if (!self::isBase64Encoded($compressedPayload)) {
            return $compressedPayload;
        }

        return gzuncompress(base64_decode($compressedPayload, true));
    }

    private static function isBase64Encoded(string $payload): bool
    {
        // Check if length is less than 4 character
        if (mb_strlen($payload) < 4) return false;

        // Check if there is no invalid character in string
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $payload)) return false;

        // Decode the string in strict mode and send the response
        if (!base64_decode($payload, true)) return false;

        // Encode and compare it to original one
        $decoded = base64_decode($payload, true);
        if (base64_encode($decoded) != $payload) return false;

        return true;
    }
}
