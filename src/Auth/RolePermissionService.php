<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

use Phalcon\Db\Adapter\AdapterInterface;

/**
 * Role-based access control (RBAC) system.
 */
class RolePermissionService
{
    public function __construct(private AdapterInterface $db)
    {
    }

    /**
     * Create a new role.
     */
    public function createRole(string $name, string $description = ''): bool
    {
        return $this->db->insert('roles', [
            'name' => $name,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Create a new permission.
     */
    public function createPermission(string $name, string $description = ''): bool
    {
        return $this->db->insert('permissions', [
            'name' => $name,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Assign a permission to a role.
     */
    public function assignPermissionToRole(int $roleId, int $permissionId): bool
    {
        return $this->db->insert('role_permissions', [
            'role_id' => $roleId,
            'permission_id' => $permissionId,
        ]);
    }

    /**
     * Assign a role to a user.
     */
    public function assignRoleToUser(int $userId, int $roleId): bool
    {
        return $this->db->insert('user_roles', [
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);
    }

    /**
     * Check if a user has a specific permission.
     */
    public function userHasPermission(int $userId, string $permission): bool
    {
        $sql = "SELECT COUNT(*) FROM user_roles ur
                JOIN role_permissions rp ON rp.role_id = ur.role_id
                JOIN permissions p ON p.id = rp.permission_id
                WHERE ur.user_id = ? AND p.name = ?";

        $result = $this->db->fetchOne($sql, null, [$userId, $permission]);
        return $result[0] > 0;
    }

    /**
     * Check if a user has a specific role.
     */
    public function userHasRole(int $userId, string $role): bool
    {
        $sql = "SELECT COUNT(*) FROM user_roles ur
                JOIN roles r ON r.id = ur.role_id
                WHERE ur.user_id = ? AND r.name = ?";

        $result = $this->db->fetchOne($sql, null, [$userId, $role]);
        return $result[0] > 0;
    }

    /**
     * Get all permissions for a user.
     */
    public function getUserPermissions(int $userId): array
    {
        $sql = "SELECT DISTINCT p.name FROM user_roles ur
                JOIN role_permissions rp ON rp.role_id = ur.role_id
                JOIN permissions p ON p.id = rp.permission_id
                WHERE ur.user_id = ?";

        return $this->db->fetchAll($sql, null, [$userId]);
    }

    /**
     * Get all roles for a user.
     */
    public function getUserRoles(int $userId): array
    {
        $sql = "SELECT r.name FROM user_roles ur
                JOIN roles r ON r.id = ur.role_id
                WHERE ur.user_id = ?";

        return $this->db->fetchAll($sql, null, [$userId]);
    }
}
