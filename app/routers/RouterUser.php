<?php
namespace App\Routers;

use App\Controller\Impl\UserControllerImpl;

class RouterUser {

  private UserControllerImpl $controller;

  public function __construct(UserControllerImpl $controller) {
      $this->controller = $controller;
  }

  public function handle(string $method, string $uri): void {
    if (preg_match('#^/users$#', $uri)) {
      switch ($method) {
        case 'POST': 
          $this->controller->createUser();
          break;
        case 'GET':
          http_response_code(501);
          echo json_encode(['error' => 'Not implemented']);
          break;
        case 'PUT':
          $this->controller->updateUser();
          break;
        default:
          http_response_code(405);
          echo json_encode(['error' => 'Method not allowed']);
      }
    }
    elseif (preg_match('#^/users/login$#', $uri)) {
      if ($method === 'POST') $this->controller->login();
      else http_response_code(405);
    }
    elseif (preg_match('#^/users/logout$#', $uri)) {
      if ($method === 'POST') $this->controller->logout();
      else http_response_code(405);
    }
    elseif (preg_match('#^/users/(\d+)$#', $uri, $matches)) {
      $id = (int)$matches[1];
      switch ($method) {
        case 'GET':
          $this->controller->getUserById($id);
          break;
        case 'DELETE':
          $this->controller->deleteUser($id);
          break;
        default:
          http_response_code(405);
          echo json_encode(['error' => 'Method not allowed']);
      }
    }
    else {
      http_response_code(404);
      echo json_encode(['error' => 'Not found']);
    }
  }
}
