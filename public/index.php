<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../models/Phone.php';
require_once __DIR__ . '/../repositories/ContactRepository.php';
require_once __DIR__ . '/../repositories/PhoneRepository.php';
require_once __DIR__ . '/../services/ContactService.php';
require_once __DIR__ . '/../routes/Router.php';

// Configurar headers de respuesta
header('Content-Type: application/json; charset=utf-8');

// Manejar la petición a través del router
$router = new Router();
$router->handleRequest();
