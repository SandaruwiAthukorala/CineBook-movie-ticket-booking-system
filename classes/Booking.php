<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class Booking
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(int $userId, int $showtimeId, array $seats, float $totalAmount): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bookings (user_id, showtime_id, seat_numbers, total_amount, booking_status)
             VALUES (:user_id, :showtime_id, :seat_numbers, :total_amount, \'Pending\')'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':showtime_id' => $showtimeId,
            ':seat_numbers' => implode(',', $seats),
            ':total_amount' => $totalAmount,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare($this->joinedSql('WHERE b.booking_id = :id'));
        $stmt->execute([':id' => $id]);
        $booking = $stmt->fetch();

        return $booking ?: null;
    }

    public function byUser(int $userId): array
    {
        $stmt = $this->db->prepare($this->joinedSql('WHERE b.user_id = :user_id ORDER BY b.booking_date DESC'));
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function all(int $limit = 100): array
    {
        $stmt = $this->db->prepare($this->joinedSql('ORDER BY b.booking_date DESC LIMIT :limit'));
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function markConfirmed(int $bookingId): bool
    {
        $stmt = $this->db->prepare("UPDATE bookings SET booking_status = 'Confirmed' WHERE booking_id = :id");
        return $stmt->execute([':id' => $bookingId]);
    }

    public function cancel(int $bookingId, ?int $userId = null): bool
    {
        $sql = "UPDATE bookings SET booking_status = 'Cancelled' WHERE booking_id = :id";
        $params = [':id' => $bookingId];

        if ($userId !== null) {
            $sql .= ' AND user_id = :user_id';
            $params[':user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function bookedSeats(int $showtimeId): array
    {
        $stmt = $this->db->prepare(
            'SELECT seat_numbers FROM bookings
             WHERE showtime_id = :showtime_id AND booking_status IN (\'Pending\', \'Confirmed\')'
        );
        $stmt->execute([':showtime_id' => $showtimeId]);

        $seats = [];
        foreach ($stmt->fetchAll() as $row) {
            foreach (explode(',', (string) $row['seat_numbers']) as $seat) {
                $seat = trim($seat);
                if ($seat !== '') {
                    $seats[] = $seat;
                }
            }
        }

        return array_values(array_unique($seats));
    }

    public function seatsAvailable(int $showtimeId, array $requestedSeats): bool
    {
        return count(array_intersect($this->bookedSeats($showtimeId), $requestedSeats)) === 0;
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
    }

    public function totalRevenue(): float
    {
        $amount = $this->db->query(
            "SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE booking_status = 'Confirmed'"
        )->fetchColumn();

        return (float) $amount;
    }

    private function joinedSql(string $clause = ''): string
    {
        return 'SELECT b.*, u.name AS customer_name, u.email,
                       s.show_date, s.show_time, s.price,
                       m.title AS movie_title, m.poster,
                       t.name AS theatre_name, t.location
                FROM bookings b
                INNER JOIN users u ON b.user_id = u.user_id
                INNER JOIN showtimes s ON b.showtime_id = s.showtime_id
                INNER JOIN movies m ON s.movie_id = m.movie_id
                INNER JOIN theatres t ON s.theatre_id = t.theatre_id ' . $clause;
    }
}
