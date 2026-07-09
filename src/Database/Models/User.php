<?php

declare(strict_types=1);

namespace Tavp\Core\Database\Models;

use Tavp\Core\Database\Model;
use Tavp\Core\Database\Relations;

/**
 * The application user.
 *
 * Per decision 10.11 (OTP-first), a password is NOT required.
 * Users authenticate via OTP (email/SMS/WhatsApp). A password column
 * may exist as an optional fallback but is never the primary path.
 */
class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'name',
        'email',
        'phone',
        'email_verified_at',
    ];

    protected array $casts = [
        'email_verified_at' => 'datetime',
    ];

    use Relations;
}
