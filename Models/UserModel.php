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

    public function updatePasswordHash(int $userId, string $passwordHash): bool
    {
        $updated = $this->update($userId, ['password_hash' => $passwordHash]);
        return $updated !== null;
    }
}
