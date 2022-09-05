<?php
declare(strict_types=1);

namespace App\Utils;

use Closure;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;
class RedisUtil
{
    const START_VALUE = '0';  // '0' because Redis stores strings only
    const KEYS_PER_ITERATION = 500;
    private static $redis = null;
    public static function getSharedRedis(): Connection
    {
        if (!self::$redis) {
            self::$redis = Redis::connection('shared');
        }
        return self::$redis;
    }
    public static function scanKeys(string $pattern, Closure $handle_keys_func): void
    {
        $cursor = self::START_VALUE;
        do {
            [$cursor, $keys] = Redis::scan($cursor, 'match', $pattern, 'count', self::KEYS_PER_ITERATION);
            if (count($keys) > 0) {
                $handle_keys_func($keys);
            }
        } while ($cursor !== self::START_VALUE);
    }
}
