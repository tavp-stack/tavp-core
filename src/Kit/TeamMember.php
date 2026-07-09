<?php

declare(strict_types=1);

namespace Tavp\Kit;

/**
 * Team membership model.
 */
class TeamMember
{
    public int $id;
    public int $team_id;
    public int $user_id;
    public string $role;
    public string $created_at;

    public function initialize(): void
    {
        $this->setSource('team_members');
    }

    /**
     * Find members by team ID.
     */
    public static function findByTeamId(int $teamId): array
    {
        return self::find(['conditions' => 'team_id = ?', 'bind' => [$teamId]]);
    }

    /**
     * Find member by team and user ID.
     */
    public static function findFirstByTeamIdAndUserId(int $teamId, int $userId): ?self
    {
        return self::findFirst([
            'conditions' => 'team_id = ? AND user_id = ?',
            'bind' => [$teamId, $userId],
        ]);
    }
}
