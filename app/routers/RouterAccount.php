<?php
namespace App\Routers;

use App\Controller\Impl\AccountControllerImpl;

class RouterAccount {

  private AccountControllerImpl $controller;

  public function __construct(AccountControllerImpl $controller) {
    $this->controller = $controller;
  }

  public function handle(string $method, string $uri): void {
    if (preg_match('#^/accounts$#', $uri)) {
      switch ($method) {
        case 'POST':
          $this->controller->register();
          break;
        case 'GET':
          http_response_code(501);
          echo json_encode(['error' => 'Not implemented']);
          break;
        default:
          http_response_code(405);
          echo json_encode(['error' => 'Method not allowed']);
      }
    }
    elseif (preg_match('#^/accounts/verify$#', $uri)) {
      if ($method === 'POST') {
        $this->controller->verifyEmail();
      } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
      }
    }
    elseif (preg_match('#^/accounts/(\d+)$#', $uri, $matches)) {
    $id = (int)$matches[1];
    switch ($method) {
        case 'GET':
          $this->controller->getById($id);
          break;
        case 'DELETE':
          $this->controller->delete($id);
          break;
        default:
          http_response_code(405);
          echo json_encode(['error' => 'Method not allowed']);
      }
    }
    elseif (preg_match('#^/accounts/reset$#', $uri)) { 
      if ($method === 'POST') $this->controller->resetPassword();
      else http_response_code(405);
    } elseif (preg_match('#^/accounts/reset/token$#', $uri)) {
      if ($method === 'POST') $this->controller->resetPasswordWithToken();
      else http_response_code(405);
    }
    else {
      http_response_code(404);
      echo json_encode(['error' => 'Not found']);
    }
  }
}
