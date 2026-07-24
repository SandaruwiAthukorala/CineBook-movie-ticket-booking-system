<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class Theatre
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(bool $activeOnly = false): array
    {
        $sql = 'SELECT * FROM theatres';

        if ($activeOnly) {
            $sql .= " WHERE status = 'Active'";
        }

        $sql .= ' ORDER BY name';
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM theatres WHERE theatre_id = :id');
        $stmt->execute([':id' => $id]);
        $theatre = $stmt->fetch();

        return $theatre ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO theatres (name, location, total_seats, status)
             VALUES (:name, :location, :total_seats, :status)'
        );

        return $stmt->execute($this->theatreParams($data));
    }

    public function update(int $id, array $data): bool
    {
        $params = $this->theatreParams($data);
        $params[':theatre_id'] = $id;

        $stmt = $this->db->prepare(
            'UPDATE theatres
             SET name = :name, location = :location, total_seats = :total_seats, status = :status
             WHERE theatre_id = :theatre_id'
        );

        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM theatres WHERE theatre_id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM theatres')->fetchColumn();
    }

    private function theatreParams(array $data): array
    {
        return [
            ':name' => trim((string) $data['name']),
            ':location' => trim((string) $data['location']),
            ':total_seats' => (int) $data['total_seats'],
            ':status' => trim((string) $data['status']),
        ];
    }
}
