<?php

class ULID
{
    private const BASE32_ALPHABET = '0123456789ABCDEFGHJKMNPQRSTVWXYZ'; // Crockford's Base32
    private const ENCODING_LENGTH = 32;
    private const TIME_MAX = 281474976710655;
    private const TIME_LENGTH = 10;
    private const RANDOM_LENGTH = 16;

    public static function generate(bool $lowercase = false): string
    {
        $ulid = sprintf(
            '%s%s',
            self::encodeTime(self::getTime(), self::TIME_LENGTH),
            self::encodeRandom(self::RANDOM_LENGTH)
        );

        return $lowercase ? strtolower($ulid) : $ulid;
    }

    private static function encodeTime(int $time, int $length): string
    {
        if ($time > self::TIME_MAX) {
            throw new Exception('Cannot encode time greater than ' + self::TIME_MAX);
        }

        $timestamp = '';

        while ($length > 0) {
            $mod = (int) ($time % self::ENCODING_LENGTH);
            $timestamp = self::BASE32_ALPHABET[$mod] . $timestamp;
            $time = ($time - $mod) / self::ENCODING_LENGTH;
            $length--;
        }

        return $timestamp;
    }

    public static function decodeTime(string $ulid): int
    {
        $time = str_split(strrev(substr($ulid, 0, self::TIME_LENGTH)));
        $timestamp = 0;

        foreach ($time as $index => $char) {
            if (($encoding_index = strripos(self::BASE32_ALPHABET, $char)) === false) {
                throw new Exception('Invalid ULID character: ' . $char);
            }

            $timestamp += ($encoding_index * pow(self::ENCODING_LENGTH, $index));
        }

        if ($timestamp > self::TIME_MAX) {
            throw new Exception('Invalid ULID string: timestamp too large');
        }

        return $timestamp;
    }

    private static function encodeRandom(int $length): string
    {
        $randomness = '';

        while ($length > 0) {
            $randomness = self::BASE32_ALPHABET[random_int(0, 31)] . $randomness;
            $length--;
        }

        return $randomness;
    }

    private static function getTime(): int
    {
        return substr(microtime(true) * 1000, 0, 13);
    }
}
