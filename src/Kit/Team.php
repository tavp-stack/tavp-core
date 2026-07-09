<?php

declare(strict_types=1);

namespace Tavp\Kit;

/**
 * Team model — manage teams and team membership.
 */
class Team
{
    public int $id;
    public string $name;
    public string $owner_id;
    public string $created_at;
    public string $updated_at;

    public function initialize(): void
    {
        $this->setSource('teams');
    }

    /**
     * Get all members of this team.
     */
    public function members(): array
    {
        return TeamMember::findByTeamId($this->id);
    }

    /**
     * Get all projects for this team.
     */
    public function projects(): array
    {
        return TeamProject::findByTeamId($this->id);
    }

    /**
     * Add a member to this team.
     */
    public function addMember(int $userId, string $role = 'member'): bool
    {
        $member = new TeamMember();
        $member->team_id = $this->id;
        $member->user_id = $userId;
        $member->role = $role;
        return $member->save();
    }

    /**
     * Remove a member from this team.
     */
    public function removeMember(int $userId): bool
    {
        $member = TeamMember::findFirstByTeamIdAndUserId($this->id, $userId);
        if ($member) {
            return $member->delete();
        }
        return false;
    }

    /**
     * Check if a user is a member of this team.
     */
    public function hasMember(int $userId): bool
    {
        return TeamMember::findFirstByTeamIdAndUserId($this->id, $userId) !== null;
    }

    /**
     * Check if a user is the owner of this team.
     */
    public function isOwner(int $userId): bool
    {
        return $this->owner_id == $userId;
    }
}
