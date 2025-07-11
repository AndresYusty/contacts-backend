<?php
require_once __DIR__ . '/../controllers/ContactController.php';

class Router
{
    private ContactController $contactController;

    public function __construct()
    {
        $this->contactController = new ContactController();
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $parts = explode('/', $uri);

        // Verificar que la ruta base sea 'contacts'
        if ($parts[0] !== 'contacts') {
            $this->notFound();
            return;
        }

        // Remover 'contacts' de los parts para simplificar el routing
        array_shift($parts);

        try {
            $this->route($method, $parts);
        } catch (Exception $e) {
            $this->serverError();
        }
    }

    private function route(string $method, array $parts): void
    {
        switch ($method) {
            case 'GET':
                $this->handleGet($parts);
                break;
            case 'POST':
                $this->handlePost($parts);
                break;
            case 'PUT':
                $this->handlePut($parts);
                break;
            case 'DELETE':
                $this->handleDelete($parts);
                break;
            default:
                $this->methodNotAllowed();
        }
    }

    private function handleGet(array $parts): void
    {
        if (empty($parts)) {
            // GET /contacts
            $this->contactController->index();
        } else {
            $this->notFound();
        }
    }

    private function handlePost(array $parts): void
    {
        if (empty($parts)) {
            // POST /contacts
            $this->contactController->store();
        } else {
            $this->notFound();
        }
    }

    private function handlePut(array $parts): void
    {
        if (count($parts) === 1 && is_numeric($parts[0])) {
            // PUT /contacts/{id}
            $this->contactController->update((int)$parts[0]);
        } else {
            $this->badRequest();
        }
    }

    private function handleDelete(array $parts): void
    {
        if (count($parts) === 1 && is_numeric($parts[0])) {
            // DELETE /contacts/{id}
            $this->contactController->destroy((int)$parts[0]);
        } else {
            $this->badRequest();
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found'], JSON_UNESCAPED_UNICODE);
    }

    private function badRequest(): void
    {
        http_response_code(400);
        echo json_encode(['error' => 'Bad Request'], JSON_UNESCAPED_UNICODE);
    }

    private function methodNotAllowed(): void
    {
        http_response_code(405);
        header('Allow: GET, POST, PUT, DELETE');
        echo json_encode(['error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
    }

    private function serverError(): void
    {
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error'], JSON_UNESCAPED_UNICODE);
    }
} 