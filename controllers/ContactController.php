<?php
require_once __DIR__ . '/../services/ContactService.php';

class ContactController
{
    private ContactService $service;

    public function __construct()
    {
        $this->service = new ContactService();
    }

    public function index(): void
    {
        try {
            $contacts = $this->service->getAllContacts();
            $this->respond(200, $contacts);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $this->respond($statusCode, ['error' => $e->getMessage()]);
        }
    }

    public function store(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        
        try {
            $contact = $this->service->createContact($payload);
            $this->respond(201, $contact);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $this->respond($statusCode, ['error' => $e->getMessage()]);
        }
    }

    public function update(int $id): void
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?: [];
        
        try {
            $contact = $this->service->updateContact($id, $payload);
            $this->respond(200, $contact);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $this->respond($statusCode, ['error' => $e->getMessage()]);
        }
    }

    public function destroy(int $id): void
    {
        try {
            if ($this->service->deleteContact($id)) {
                http_response_code(204);
            } else {
                $this->respond(404, ['error' => 'No encontrado']);
            }
        } catch (\Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            $this->respond($statusCode, ['error' => $e->getMessage()]);
        }
    }

    private function respond(int $code, $data): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
