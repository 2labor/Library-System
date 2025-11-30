<?php
namespace App\Routers;

use App\Controller\BookController;

class RouterBook {

private BookController $controller;

public function __construct(
  BookController $controller
) {
  $this->controller = $controller;
}

public function handle(string $method, string $uri): void {
  if (preg_match('#^/books$#', $uri)) {
    switch ($method) {
      case 'GET':
        $this->controller->find();
        break;
      case 'POST':
        $this->controller->add();
        break;
      default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
  } elseif (preg_match('#^/books/([\w-]+)$#', $uri, $matches)) {
    $isbn = $matches[1];
    switch ($method) {
      case 'GET':
        $this->controller->findByIsbn($isbn);
        break;
      case 'PUT':
        $this->controller->update($isbn);
        break;
      case 'DELETE':
        $this->controller->delete($isbn);
        break;
      case 'PATCH':
        $this->controller->toggleAvailability($isbn);
        break;
      default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
  } else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
  }
}
}