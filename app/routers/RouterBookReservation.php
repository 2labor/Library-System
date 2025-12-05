<?php
/*
 * Inputs:
 * - HTTP method and URI for book reservation-related requests
 *
 * Outputs:
 * - Delegates handling to BookReservationController methods based on the request
 *
 * File: app/routers/RouterBookReservation.php
 */
namespace App\Routers;

use App\Controller\BookReservationController;

class RouterBookReservation
{
    private BookReservationController $controller;

    public function __construct(BookReservationController $controller)
    {
      $this->controller = $controller;
    }

  /**
   * Handles HTTP requests for reservation-related routes.
   *
   * Routes:
   * - POST/reservation/reserve - Create a new reservation
   * - PUT/reservation/extend - Extend an existing reservation
   * - DELETE/reservation/cancel - Cancel an existing reservation
   * - GET/reservation/book/{isbn} - Get reservation information by book ISBN
   * - GET/reservation/user/{userId} - Get reservations for a specific user
   *
   * Behavior:
   * - Matches request URIs using regex patterns for reservation actions
   * - Extracts dynamic parameters:
   * - `{isbn}` for book-based reservation lookup
   * - `{userId}` for user-based reservation lookup
   * - Delegates operations to controller methods:
   * - reserve, extend, cancel, getByBook, getByUser
   * - Returns proper HTTP status codes:
   * - 405 Method Not Allowed (via methodNotAllowed())
   * - 404 Not Found (for unmatched routes)
   *
   * @param string $method The HTTP request method.
   * @param string $uri    The incoming request URI.
   * @return void
   */
  public function handle(string $method, string $uri): void {
    if (preg_match('#^/reservation/reserve$#', $uri)) {
        if ($method === 'POST') {
          $this->controller->reserve();
        } else {
          $this->methodNotAllowed();
        }
        return;
    }
    if (preg_match('#^/reservation/extend$#', $uri)) {
        if ($method === 'PUT') {
          $this->controller->extend();
        } else {
          $this->methodNotAllowed();
        }
        return;
    }
    if (preg_match('#^/reservation/cancel$#', $uri)) {
        if ($method === 'DELETE') {
          $this->controller->cancel();
        } else {
          $this->methodNotAllowed();
        }
        return;
    }
    if (preg_match('#^/reservation/book/([\w-]+)$#', $uri, $matches)) {
      $isbn = $matches[1];
      if ($method === 'GET') {
        $this->controller->getByBook($isbn);
      } else {
        $this->methodNotAllowed();
      }
      return;
    }
    if (preg_match('#^/reservation/user/(\d+)$#', $uri, $matches)) {
      $userId = (int)$matches[1];
      if ($method === 'GET') {
        $this->controller->getByUser($userId);
      } else {
        $this->methodNotAllowed();
      }
      return;
    }
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
  }

  /**
   * Sends a standardized "Method Not Allowed" response.
   *
   * @return void
   */
  private function methodNotAllowed(): void
  {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
  }
}
