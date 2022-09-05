<?php
declare(strict_types=1);

namespace App\Utils;

use App;

class EnvironmentUtil
{
    const WORKER_SUFFIX = '-worker';
    const LOCAL_EB_NAME = 'local';
    const LIVE_KEY = 'live_worker_eb_name';

    public static function isBeanstalkWebServer(): bool
    {
        return self::isBeanstalk() && !self::isBeanstalkWorker();
    }

    public static function isBeanstalkWorker(): bool
    {
        $eb_name = config('app.eb_name', self::LOCAL_EB_NAME);
        return str_contains($eb_name, self::WORKER_SUFFIX);
    }

    public static function isBeanstalk(): bool
    {
        // we're not using config_safe here because EP uses this, and we don't want EP to ever throw exceptions
        $eb_name = config('app.eb_name', self::LOCAL_EB_NAME);
        return $eb_name !== self::LOCAL_EB_NAME;
    }

    public static function isRunningLive(): bool
    {
        $eb_name = config('app.eb_name', self::LOCAL_EB_NAME);
        $live = Redis::get(self::LIVE_KEY);
        return !$live || $live === $eb_name;
    }

    public static function isProduction(): bool
    {
        return !App::environment(['testing', 'local', 'travis', 'dev', 'demo']);
    }

    public static function isTest(): bool
    {
        return App::environment(['testing', 'travis']);
    }

    public static function isLocalOrTest(): bool
    {
        return self::isLocal() || self::isTest();
    }

    public static function isLocal() : bool
    {
        return App::environment('local');
    }

    public static function isDevelopment() : bool
    {
        return App::environment('local') || App::environment('demo') ;
    }

    public static function isTravis(): bool
    {
        return App::environment('travis');
    }
}
