<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    protected string $table = 'users';
    protected array $fillable = [
        'role_id',
        'full_name',
        'email',
        'password_hash',
        'is_active',
    ];

    public function findByEmail(string $email): ?array
    {
        $sql = sprintf('SELECT * FROM %s WHERE email = :email LIMIT 1', $this->table);
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }

    public function createUser(array $data): ?array
    {
        return $this->create($data);
    }

    public function getRegistrationRoleId(): ?int
    {
        $preferredRoles = ['Lecteur'];
        $placeholders = implode(', ', array_fill(0, count($preferredRoles), '?'));
        $sql = sprintf(
            'SELECT id FROM roles WHERE name IN (%s) ORDER BY FIELD(name, %s), id LIMIT 1',
            $placeholders,
            $placeholders
        );
        $stmt = $this->db->prepare($sql);
        $index = 1;
        foreach ($preferredRoles as $role) {
            $stmt->bindValue($index, $role);
            $index++;
        }
        foreach ($preferredRoles as $role) {
            $stmt->bindValue($index, $role);
            $index++;
        }
        $stmt->execute();
        $result = $stmt->fetchColumn();
        if ($result !== false) {
            return (int) $result;
        }

        $fallback = $this->db->query('SELECT id FROM roles ORDER BY id LIMIT 1')->fetchColumn();
        return $fallback !== false ? (int) $fallback : null;
    }

    public function updatePasswordHash(int $userId, string $passwordHash): bool
    {
        $updated = $this->update($userId, ['password_hash' => $passwordHash]);
        return $updated !== null;
    }
}
