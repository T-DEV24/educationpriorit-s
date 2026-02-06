<?php

declare(strict_types=1);

require_once __DIR__ . '/../Config/database.php';

abstract class BaseModel
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function findAll(int $limit = 100, int $offset = 0): array
    {
        $sql = sprintf('SELECT * FROM %s LIMIT :limit OFFSET :offset', $this->table);
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = sprintf('SELECT * FROM %s WHERE %s = :id', $this->table, $this->primaryKey);
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }

    public function create(array $data): ?array
    {
        $payload = $this->filterData($data);
        if ($payload === []) {
            return null;
        }

        $columns = array_keys($payload);
        $placeholders = array_map(static fn(string $column): string => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->db->prepare($sql);
        foreach ($payload as $column => $value) {
            $stmt->bindValue(':' . $column, $value);
        }
        $executed = $stmt->execute();
        if (! $executed) {
            return null;
        }

        $id = (int) $this->db->lastInsertId();

        return $this->find($id);
    }

    public function update(int $id, array $data): ?array
    {
        $payload = $this->filterData($data);
        if ($payload === []) {
            return null;
        }

        $assignments = [];
        foreach ($payload as $column => $value) {
            $assignments[] = sprintf('%s = :%s', $column, $column);
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :id',
            $this->table,
            implode(', ', $assignments),
            $this->primaryKey
        );

        $stmt = $this->db->prepare($sql);
        foreach ($payload as $column => $value) {
            $stmt->bindValue(':' . $column, $value);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return $this->find($id);
        }

        return $this->find($id);
    }

    public function delete(int $id): bool
    {
        $sql = sprintf('DELETE FROM %s WHERE %s = :id', $this->table, $this->primaryKey);
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function countAll(): int
    {
        $sql = sprintf('SELECT COUNT(*) FROM %s', $this->table);
        return (int) $this->db->query($sql)->fetchColumn();
    }

    protected function filterData(array $data): array
    {
        if ($this->fillable === []) {
            return [];
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }
}
