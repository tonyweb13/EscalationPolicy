<?php
declare(strict_types=1);

namespace App\Auth;

use App\Models\User;
use App\Utils\RedisUtil;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Redis;

class CachingUserProvider extends EloquentUserProvider
{
    const CACHE_TIME_MINUTES = 60;
    const KEY_PREFIX = 'user_by_id_';
    /**
     * The cache instance.
     *
     * @var Repository
     */
    protected $cache;
    /**
     * Create a new database user provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $model
     * @param  Repository  $cache
     * @return void
     */
    public function __construct(HasherContract $hasher, $model, Repository $cache)
    {
        $this->model = $model;
        $this->hasher = $hasher;
        $this->cache = $cache;
    }
    public static function clearCache()
    {
        RedisUtil::scanKeys('*' . self::KEY_PREFIX . '*', function (array $keys) {
            Redis::del($keys);
        });
    }
    /**
     * Retrieve a user by their id.
     *
     * @param  mixed  $id
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($id): User
    {
        return $this->cache->remember(self::KEY_PREFIX . $id, self::CACHE_TIME_MINUTES, function () use ($id) {
            $user = User::with(['department', 'office', 'role', 'portfolios'])
                ->where('id', $id)
                ->first();
            // this forces the tags to be loaded and stored in User,
            // since User sets a private variable when getTags is called
            if ($user) {
                $user->getTags();
            }
            return $user;
        });
    }
}
