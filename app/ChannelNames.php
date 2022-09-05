<?php
declare(strict_types=1);

namespace app;

use Illuminate\Support\Facades\App;

/**
 * This class serves as a centralized place to store the names of channels (used for WebSocket communication) via
 * Echo and also provides functions to generate the channel names from parameters.
 */
class ChannelNames
{
    // The USER channel is a channel for each individual user.
    // the variables embedded in USER must coincide with those in channels.php
    public const USER = 'App.EP.User.{id}';

    public static function getForUser($id)
    {
        // it is important that these fields be "none" rather than the empty string because otherwise the
        // the laravel channel parsing doesn't work right

        $user_tmp = static::USER;
        $user_tmp = str_replace('{id}', $id, $user_tmp);

        return $user_tmp;
    }
}
