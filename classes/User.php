<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function register(string $name, string $email, string $password, string $phone, string $role = 'user'): bool
    {
        if ($this->findByEmail($email)) {
            throw new InvalidArgumentException('Email address is already registered.');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, phone, role) VALUES (:name, :email, :password, :phone, :role)'
        );

        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':phone' => $phone,
            ':role' => $role,
        ]);
    }

    public function login(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }

        return null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT user_id, name, email, phone, role, created_at FROM users WHERE user_id = :id');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function latest(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT user_id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
