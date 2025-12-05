<?php
/*
 * Inputs:
 * - HTTP method and URI for book-related requests
 *
 * Outputs:
 * - Delegates handling to BookController methods based on the request
 *
 * File: app/routers/RouterBook.php
 */
namespace App\Routers;

use App\Controller\BookController;

class RouterBook {

private BookController $controller;

public function __construct(
  BookController $controller
) {
  $this->controller = $controller;
}

/**
 * Handles HTTP requests for book-related routes.
 *
 * Routes:
 * - GET/books - Retrieve a list of books
 * - POST/books - Add a new book
 * - GET/books/{isbn} - Retrieve a book by its ISBN
 * - PUT/books/{isbn} - Update a book by its ISBN
 * - DELETE/books/{isbn} - Delete a book by its ISBN
 * - PATCH/books/{isbn} - Toggle book availability status
 *
 * Behavior:
 * - Matches URIs with regex patterns (/books and /books/{isbn})
 * - Extracts ISBN from the URI when necessary
 * - Delegates processing to controller methods:
 * - find, add, findByIsbn, update, delete, toggleAvailability
 * - Returns appropriate HTTP error responses:
 * - 404 Not Found
 * - 405 Method Not Allowed
 *
 * @param string $method The HTTP method of the incoming request.
 * @param string $uri    The request URI.
 * @return void
 */

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