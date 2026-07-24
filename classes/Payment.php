<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class Payment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(int $bookingId, string $cardName, string $paymentMethod, float $amount): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payments (booking_id, card_name, payment_method, amount, status)
             VALUES (:booking_id, :card_name, :payment_method, :amount, \'Completed\')'
        );

        return $stmt->execute([
            ':booking_id' => $bookingId,
            ':card_name' => $cardName,
            ':payment_method' => $paymentMethod,
            ':amount' => $amount,
        ]);
    }

    public function byBooking(int $bookingId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE booking_id = :booking_id LIMIT 1');
        $stmt->execute([':booking_id' => $bookingId]);
        $payment = $stmt->fetch();

        return $payment ?: null;
    }
}
