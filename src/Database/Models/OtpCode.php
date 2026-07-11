<?php

declare(strict_types=1);

namespace Tavp\Core\Database\Models;

use Tavp\Core\Database\Model;
use Tavp\Core\Database\Relations;

/**
 * A single-use OTP code sent to a user's email or phone.
 *
 * Codes are hashed (SHA-256), short-lived (default 5 minutes) and
 * limited in attempts to prevent brute force.
 */
class OtpCode extends Model
{
    protected string $table = 'otp_codes';
    protected string $primaryKey = 'id';

    protected array $fillable = [
        'identifier',
        'code_hash',
        'channel',
        'expires_at',
        'attempts',
    ];

    protected array $casts = [
        'expires_at' => 'datetime',
        'attempts' => 'integer',
    ];

    protected bool $timestamps = false;

    use Relations;
}
