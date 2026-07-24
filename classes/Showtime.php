<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class Showtime
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(?int $movieId = null): array
    {
        $sql = 'SELECT s.*, m.title AS movie_title, m.poster, t.name AS theatre_name, t.location, t.total_seats
                FROM showtimes s
                INNER JOIN movies m ON s.movie_id = m.movie_id
                INNER JOIN theatres t ON s.theatre_id = t.theatre_id';
        $params = [];

        if ($movieId !== null) {
            $sql .= ' WHERE s.movie_id = :movie_id';
            $params[':movie_id'] = $movieId;
        }

        $sql .= ' ORDER BY s.show_date ASC, s.show_time ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function upcomingByMovie(int $movieId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, t.name AS theatre_name, t.location, t.total_seats
             FROM showtimes s
             INNER JOIN theatres t ON s.theatre_id = t.theatre_id
             WHERE s.movie_id = :movie_id AND s.show_date >= CURDATE()
             ORDER BY s.show_date ASC, s.show_time ASC'
        );
        $stmt->execute([':movie_id' => $movieId]);

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, m.title AS movie_title, m.genre, m.language, m.duration, m.poster,
                    t.name AS theatre_name, t.location, t.total_seats
             FROM showtimes s
             INNER JOIN movies m ON s.movie_id = m.movie_id
             INNER JOIN theatres t ON s.theatre_id = t.theatre_id
             WHERE s.showtime_id = :id'
        );
        $stmt->execute([':id' => $id]);
        $showtime = $stmt->fetch();

        return $showtime ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO showtimes (movie_id, theatre_id, show_date, show_time, price, status)
             VALUES (:movie_id, :theatre_id, :show_date, :show_time, :price, :status)'
        );

        return $stmt->execute($this->showtimeParams($data));
    }

    public function update(int $id, array $data): bool
    {
        $params = $this->showtimeParams($data);
        $params[':showtime_id'] = $id;

        $stmt = $this->db->prepare(
            'UPDATE showtimes
             SET movie_id = :movie_id, theatre_id = :theatre_id, show_date = :show_date,
                 show_time = :show_time, price = :price, status = :status
             WHERE showtime_id = :showtime_id'
        );

        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM showtimes WHERE showtime_id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM showtimes')->fetchColumn();
    }

    private function showtimeParams(array $data): array
    {
        return [
            ':movie_id' => (int) $data['movie_id'],
            ':theatre_id' => (int) $data['theatre_id'],
            ':show_date' => trim((string) $data['show_date']),
            ':show_time' => trim((string) $data['show_time']),
            ':price' => (float) $data['price'],
            ':status' => trim((string) $data['status']),
        ];
    }
}
