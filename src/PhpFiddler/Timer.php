<?php
namespace PhpFiddler;

class Timer
{
    protected static $time;

    const IN_SECONDS = 1;
    const IN_MILLISECONDS = 1000;
    const IN_MICROSECONDS = 1000000;

    public static $unitSeconds = 's';
    public static $unitMilliseconds = 'ms';
    public static $unitMicroseconds = 'µs';

    public static function start()
    {
        self::$time = microtime(true);
    }

    public static function stop()
    {
        return self::$time = microtime(true) - self::$time;
    }

    public static function getSeconds($precision = 0)
    {
        return self::get($precision, self::IN_SECONDS) . ' ' . self::$unitSeconds;
    }

    public static function getMilliseconds($precision = 0)
    {
        return self::get($precision, self::IN_MILLISECONDS) . ' ' . self::$unitMilliseconds;
    }

    public static function getMicroseconds($precision = 0)
    {
        return self::get($precision, self::IN_MICROSECONDS) . ' ' . self::$unitMicroseconds;
    }

    /**
     * Get current time
     * @param int $precision numbers after floating point, default 0
     * @param int $unit by how much to divide the time stored in seconds (default 1)
     * @return string
     */
    public static function get($precision = 0, $unit = 1)
    {
        return sprintf("%0.{$precision}lf", self::$time * $unit);
    }
}