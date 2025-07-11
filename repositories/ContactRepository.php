<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/PhoneRepository.php';

class ContactRepository
{
    private PDO $db;
    private PhoneRepository $phoneRepo;

    public function __construct()
    {
        $this->db        = Database::getConnection();
        $this->phoneRepo = new PhoneRepository();
    }

    /** @return Contact[] */
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM contacts');
        $contacts = [];
        while ($row = $stmt->fetch()) {
            $contact = new Contact($row);
            $contact->phones = $this->phoneRepo->getByContactId($contact->id);
            $contacts[] = $contact;
        }
        return $contacts;
    }

    public function create(string $fn, string $ln, string $email): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO contacts (first_name, last_name, email) VALUES (?, ?, ?)'
        );
        $stmt->execute([$fn, $ln, $email]);
        return (int)$this->db->lastInsertId();
    }

    public function createWithPhones(string $fn, string $ln, string $email, array $phones): int
    {
        try {
            $this->db->beginTransaction();
            
            $id = $this->create($fn, $ln, $email);
            
            foreach ($phones as $num) {
                if (trim($num) !== '') {
                    $this->phoneRepo->create($id, $num);
                }
            }
            
            $this->db->commit();
            return $id;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM contacts WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function update(int $id, string $fn, string $ln, string $email, array $phones): bool
    {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare(
                'UPDATE contacts SET first_name = ?, last_name = ?, email = ? WHERE id = ?'
            );
            $ok = $stmt->execute([$fn, $ln, $email, $id]);
            if (!$ok) {
                $this->db->rollBack();
                return false;
            }
            
            // Actualizar teléfonos: eliminar todos y volver a insertar
            $this->phoneRepo->deleteByContactId($id);
            foreach ($phones as $num) {
                if (trim($num) !== '') {
                    $this->phoneRepo->create($id, $num);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getById(int $id): ?Contact
    {
        $stmt = $this->db->prepare('SELECT * FROM contacts WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $contact = new Contact($row);
        $contact->phones = $this->phoneRepo->getByContactId($contact->id);
        return $contact;
    }
}
