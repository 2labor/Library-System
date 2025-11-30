<?php
namespace App\Routers;

use App\Controller\CategoriesController;

class RouterCategories {

  private CategoriesController $controller;

  public function __construct(CategoriesController $controller) {
    $this->controller = $controller;
  }

  public function handle(string $method, string $uri): void {
    if (preg_match('#^/categories$#', $uri)) {
      switch ($method) {
        case 'GET':
          $this->controller->getAll();
          break;

        default:
          http_response_code(405);
          echo json_encode(['error' => 'Method not allowed']);
      }
      return;
    }

    if (preg_match('#^/categories/(\d+)$#', $uri, $matches)) {
      $id = (int)$matches[1];

      switch ($method) {
        case 'GET':
          $this->controller->getById($id);
          break;

        default:
          http_response_code(405);
          echo json_encode(['error' => 'Method not allowed']);
      }
      return;
    }

    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
  }
}
