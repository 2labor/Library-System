<?php
/*
 * Inputs:
 * - HTTP method and URI for user-related requests
 *
 * Outputs:
 * - Delegates handling to UserControllerImpl methods based on the request
 *
 * File: app/routers/RouterUser.php
 */
namespace App\Routers;

use App\Controller\Impl\UserControllerImpl;

class RouterUser {

  private UserControllerImpl $controller;

  public function __construct(UserControllerImpl $controller) {
      $this->controller = $controller;
  }
  /**
   * Handles HTTP requests for user-related routes.
   *
   * Routes:
   * - POST/users - Create a new user
   * - GET/users - Not implemented (501)
   * - PUT/users - Update the currently authenticated user
   * - POST/users/login - Log a user in
   * - POST/users/logout - Log a user out
   * - GET/users/{id} - Retrieve a user by ID
   * - DELETE/users/{id} - Delete a user by ID
   *
   * Behavior:
   * - Matches request URIs using regex for base users path and dynamic `{id}`
   * - Delegates user operations to controller methods:
   * - createUser, updateUser, login, logout, getUserById, deleteUser
   * - Produces proper HTTP status responses:
   * - 501 Not Implemented (for GET /users)
   * - 405 Method Not Allowed (unsupported methods)
   * - 404 Not Found (unknown route)
   *
   * @param string $method The HTTP method of the incoming request.
   * @param string $uri    The request URI.
   * @return void
   */
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
