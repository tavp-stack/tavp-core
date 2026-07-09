<?php

declare(strict_types=1);

namespace Tavp\Core\Database\Models;

use Tavp\Core\Database\Model;
use Tavp\Core\Database\Relations;

/**
 * An active login session for a user (used for the "remember me" /
 * device list feature). Token is stored hashed.
 */
class UserSession extends Model
{
    protected string $table = 'user_sessions';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'user_id',
        'token',
        'device',
        'ip_address',
        'last_active',
    ];

    protected array $casts = [
        'last_active' => 'datetime',
    ];

    use Relations;
}
