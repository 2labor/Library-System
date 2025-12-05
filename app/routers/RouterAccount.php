<?php
/*
 * Inputs:
 * - HTTP method and URI for account-related requests
 *
 * Outputs:
 * - Delegates handling to AccountControllerImpl methods based on the request
 *
 * File: app/routers/RouterAccount.php
 */
namespace App\Routers;

use App\Controller\Impl\AccountControllerImpl;

class RouterAccount {

  private AccountControllerImpl $controller;

  public function __construct(AccountControllerImpl $controller) {
    $this->controller = $controller;
  }

  /**
   * Handles incoming HTTP requests for account-related routes.
   *
   * Routes:
   * - POST/accounts - Register a new account
   * - GET/accounts - Not implemented (501)
   * - POST/accounts/verify - Verify email for an account
   * - GET/accounts/{id} - Retrieve an account by ID
   * - DELETE/accounts/{id} - Delete an account by ID
   * - POST/accounts/reset - Initiate password reset
   * - POST/accounts/reset/token - Reset password using a token
   *
   * Behavior:
   * - Validates URI patterns using regex
   * - Delegates actions to controller methods
   * - Returns appropriate HTTP error codes:
   * - 404 Not Found
   * - 405 Method Not Allowed
   * - 501 Not Implemented
   *
   * @param string $method The HTTP method of the request (GET, POST, DELETE, etc.).
   * @param string $uri     The URI of the incoming request.
   * @return void
   */
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
