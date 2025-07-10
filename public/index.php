<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../models/Phone.php';
require_once __DIR__ . '/../repositories/ContactRepository.php';
require_once __DIR__ . '/../repositories/PhoneRepository.php';
require_once __DIR__ . '/../controllers/ContactController.php';

$ctrl = new ContactController();
$method = $_SERVER['REQUEST_METHOD'];
$uri    = trim($_SERVER['REQUEST_URI'], '/');
$parts  = explode('/', $uri);

if ($parts[0] !== 'contacts') {
    http_response_code(404);
    exit;
}

switch ($method) {
    case 'GET':
        if (count($parts) === 1) {
            $ctrl->index();
        } else {
            http_response_code(404);
        }
        break;

    case 'POST':
        $ctrl->store();
        break;

    case 'DELETE':
        if (isset($parts[1]) && is_numeric($parts[1])) {
            $ctrl->destroy((int)$parts[1]);
        } else {
            http_response_code(400);
        }
        break;

    case 'PUT':
        if (isset($parts[1]) && is_numeric($parts[1])) {
            $ctrl->update((int)$parts[1]);
        } else {
            http_response_code(400);
        }
        break;

    default:
        http_response_code(405);
        header('Allow: GET, POST, PUT, DELETE');
}
