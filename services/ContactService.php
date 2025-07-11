<?php
require_once __DIR__ . '/../repositories/ContactRepository.php';
require_once __DIR__ . '/../repositories/PhoneRepository.php';

class ContactService
{
    private ContactRepository $contactRepo;
    private PhoneRepository $phoneRepo;

    public function __construct()
    {
        $this->contactRepo = new ContactRepository();
        $this->phoneRepo = new PhoneRepository();
    }

    public function getAllContacts(): array
    {
        $contacts = $this->contactRepo->getAll();
        return array_map(fn($c) => $c->toArray(), $contacts);
    }

    public function createContact(array $payload): array
    {
        // Validaciones básicas
        $this->validateContactData($payload);
        $this->validatePhones($payload['phones'] ?? []);
        
        try {
            $phones = $payload['phones'] ?? [];
            $id = $this->contactRepo->createWithPhones(
                $payload['first_name'],
                $payload['last_name'],
                $payload['email'],
                $phones
            );
            
            $contact = $this->contactRepo->getById($id);
            return $contact ? $contact->toArray() : ['error' => 'No encontrado'];
        } catch (\Exception $e) {
            throw new \Exception('Error interno del servidor');
        }
    }

    public function updateContact(int $id, array $payload): array
    {
        // Validaciones básicas
        $this->validateContactData($payload);
        $this->validatePhones($payload['phones'] ?? []);
        
        $phones = $payload['phones'] ?? [];
        
        try {
            $ok = $this->contactRepo->update(
                $id,
                $payload['first_name'],
                $payload['last_name'],
                $payload['email'],
                $phones
            );
            
            if (!$ok) {
                throw new \Exception('Contacto no encontrado', 404);
            }
            
            $contact = $this->contactRepo->getById($id);
            return $contact ? $contact->toArray() : ['error' => 'No encontrado'];
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                throw $e;
            }
            throw new \Exception('Error interno del servidor');
        }
    }

    public function deleteContact(int $id): bool
    {
        return $this->contactRepo->delete($id);
    }

    private function validateContactData(array $payload): void
    {
        foreach (['first_name', 'last_name', 'email'] as $field) {
            if (empty($payload[$field] ?? '')) {
                throw new \Exception("$field es requerido", 400);
            }
        }
        
        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email no válido", 400);
        }
    }

    private function validatePhones(array $phones): void
    {
        foreach ($phones as $num) {
            if (trim($num) === '') {
                throw new \Exception('Número de teléfono vacío', 400);
            }
        }
    }


} 