<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class Movie
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(string $search = '', string $genre = '', string $language = '', string $status = ''): array
    {
        $sql = 'SELECT * FROM movies WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (title LIKE :search OR description LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if ($genre !== '') {
            $sql .= ' AND genre = :genre';
            $params[':genre'] = $genre;
        }

        if ($language !== '') {
            $sql .= ' AND language = :language';
            $params[':language'] = $language;
        }

        if ($status !== '') {
            $sql .= ' AND status = :status';
            $params[':status'] = $status;
        }

        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function featured(int $limit = 4): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM movies WHERE status = 'Now Showing' ORDER BY created_at DESC LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM movies WHERE movie_id = :id');
        $stmt->execute([':id' => $id]);
        $movie = $stmt->fetch();

        return $movie ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO movies (title, genre, language, duration, description, poster, status)
             VALUES (:title, :genre, :language, :duration, :description, :poster, :status)'
        );

        return $stmt->execute($this->movieParams($data));
    }

    public function update(int $id, array $data): bool
    {
        $params = $this->movieParams($data);
        $params[':movie_id'] = $id;

        $stmt = $this->db->prepare(
            'UPDATE movies
             SET title = :title, genre = :genre, language = :language, duration = :duration,
                 description = :description, poster = :poster, status = :status
             WHERE movie_id = :movie_id'
        );

        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM movies WHERE movie_id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM movies')->fetchColumn();
    }

    public function genres(): array
    {
        return $this->distinctColumn('genre');
    }

    public function languages(): array
    {
        return $this->distinctColumn('language');
    }

    private function movieParams(array $data): array
    {
        return [
            ':title' => trim((string) $data['title']),
            ':genre' => trim((string) $data['genre']),
            ':language' => trim((string) $data['language']),
            ':duration' => (int) $data['duration'],
            ':description' => trim((string) $data['description']),
            ':poster' => trim((string) ($data['poster'] ?? '')),
            ':status' => trim((string) $data['status']),
        ];
    }

    private function distinctColumn(string $column): array
    {
        $allowed = ['genre', 'language'];

        if (!in_array($column, $allowed, true)) {
            return [];
        }

        $stmt = $this->db->query("SELECT DISTINCT {$column} FROM movies WHERE {$column} <> '' ORDER BY {$column}");
        return array_column($stmt->fetchAll(), $column);
    }
}
