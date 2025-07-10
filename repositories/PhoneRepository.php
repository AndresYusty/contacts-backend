<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Phone.php';

class PhoneRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** @return Phone[] */
    public function getByContactId(int $contactId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM phones WHERE contact_id = ?');
        $stmt->execute([$contactId]);
        return array_map(fn($row) => new Phone($row), $stmt->fetchAll());
    }

    public function create(int $contactId, string $number): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO phones (contact_id, phone_number) VALUES (?, ?)'
        );
        $stmt->execute([$contactId, $number]);
    }

    public function deleteByContactId(int $contactId): void
    {
        $stmt = $this->db->prepare('DELETE FROM phones WHERE contact_id = ?');
        $stmt->execute([$contactId]);
    }
}
