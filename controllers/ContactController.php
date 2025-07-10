<?php
require_once __DIR__ . '/../repositories/ContactRepository.php';

class ContactController
{
    private ContactRepository $repo;

    public function __construct()
    {
        $this->repo = new ContactRepository();
    }

    public function index(): void
    {
        $contacts = $this->repo->getAll();
        $this->respond(200, array_map(fn($c) => $c->toArray(), $contacts));
    }

    public function store(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        // Validaciones básicas
        foreach (['first_name','last_name','email'] as $field) {
            if (empty($payload[$field] ?? '')) {
                $this->respond(400, ['error' => "$field es requerido"]);
                return;
            }
        }
        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->respond(400, ['error' => "Email no válido"]);
            return;
        }

        try {
            // Transacción: contacto + teléfonos
            $db = Database::getConnection();
            $db->beginTransaction();
            $id = $this->repo->create(
                $payload['first_name'],
                $payload['last_name'],
                $payload['email']
            );
            // bonus: teléfonos opcionales
            if (!empty($payload['phones']) && is_array($payload['phones'])) {
                foreach ($payload['phones'] as $num) {
                    if (trim($num) === '') {
                        $db->rollBack();
                        $this->respond(400, ['error'=>'Número de teléfono vacío']);
                        return;
                    }
                    (new PhoneRepository())->create($id, $num);
                }
            }
            $db->commit();
            $contact = $this->repo->getById($id);
            $this->respond(201, $contact ? $contact->toArray() : ['error' => 'No encontrado']);
        } catch (\Exception $e) {
            $db->rollBack();
            $this->respond(500, ['error' => 'Error interno']);
        }
    }

    public function update(int $id): void
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        foreach (['first_name','last_name','email'] as $field) {
            if (empty($payload[$field] ?? '')) {
                $this->respond(400, ['error' => "$field es requerido"]);
                return;
            }
        }
        if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $this->respond(400, ['error' => "Email no válido"]);
            return;
        }
        $phones = $payload['phones'] ?? [];
        try {
            $db = Database::getConnection();
            $db->beginTransaction();
            $ok = $this->repo->update(
                $id,
                $payload['first_name'],
                $payload['last_name'],
                $payload['email'],
                $phones
            );
            if (!$ok) {
                $db->rollBack();
                $this->respond(404, ['error' => 'No encontrado']);
                return;
            }
            $db->commit();
            $contact = $this->repo->getById($id);
            $this->respond(200, $contact ? $contact->toArray() : ['error' => 'No encontrado']);
        } catch (\Exception $e) {
            $db->rollBack();
            $this->respond(500, ['error' => 'Error interno']);
        }
    }

    public function destroy(int $id): void
    {
        if ($this->repo->delete($id)) {
            http_response_code(204);
        } else {
            $this->respond(404, ['error' => 'No encontrado']);
        }
    }

    private function respond(int $code, $data): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
