<?php

namespace LaPress\Database\Models;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use LaPress\Database\ModelResolver;
use LaPress\Database\Traits\HasMeta;
use LaPress\Database\UrlGenerators\PostAuthorUrlGenerator;

class User extends Authenticatable
{
    use Notifiable, HasMeta;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * @var array
     */
    protected $dates = [
        'user_registered',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_login',
        'user_email',
        'user_pass',
        'user_nicename',
        'user_url',
        'display_name',
        'user_status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_pass',
        'remember_token',
    ];

    /**
     *
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($user) {
            if (empty($user->user_registered)) {
                $user->user_registered = Carbon::now();
            }
        });
    }

    /**
     * @param array $data
     * @param array $meta
     * @return User
     */
    public static function add(array $data, array $meta = [])
    {
        $user = self::create($data);

        $user->saveMeta(array_merge([
            'nickname'              => '',
            'first_name'            => '',
            'last_name'             => '',
            'description'           => '',
            'rich_editing'          => 'true',
            'syntax_highlighting'   => 'true',
            'comment_shortcuts'     => 'false',
            'admin_color'           => 'fresh',
            'use_ssl'               => '0',
            'show_admin_bar_front'  => 'true',
            'locale'                => '',
            'wp_capabilities'       => 'a:1:{s:6:\"author\";b:1;}',
            'wp_user_level'         => '2',
            'dismissed_wp_pointers' => 'wp496_privacy',
        ], $meta));

        return $user;
    }

    /**
     * Posts
     * Define a relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(ModelResolver::resolve('Post'), 'post_author')->recent();
    }

    public static function byName($name)
    {
        return static::whereUserNicename($name)->first();
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function toArray()
    {
        return [
            'name'   => $this->display_name,
            'key'    => $this->user_nicename,
            'avatar' => $this->avatar,
        ];
    }
}
