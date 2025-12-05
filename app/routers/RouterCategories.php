<?php
/*
 * Inputs:
 * - HTTP method and URI for category-related requests
 *
 * Outputs:
 * - Delegates handling to CategoriesController methods based on the request
 *
 * File: app/routers/RouterCategories.php
 */
namespace App\Routers;

use App\Controller\CategoriesController;

class RouterCategories {

  private CategoriesController $controller;

  public function __construct(CategoriesController $controller) {
    $this->controller = $controller;
  }

  /**
   * Handles HTTP requests for category-related routes.
   *
   * Routes:
   * - GET/categories - Retrieve a list of all categories
   * - GET/categories/{id} - Retrieve a specific category by its ID
   *
   * Behavior:
   * - Matches URIs using regex patterns (`/categories` and `/categories/{id}`)
   * - Extracts `{id}` when applicable
   * - Delegates processing to controller methods:
   * - getAll
   * - getById
   * - Returns appropriate HTTP status codes:
   * - 405 Method Not Allowed for unsupported methods
   * - 404 Not Found for unmatched routes
   *
   * @param string $method The HTTP request method.
   * @param string $uri    The requested URI.
   * @return void
   */
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
