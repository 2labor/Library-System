<?php
namespace App\Routers;

use App\Controller\BookReservationController;

class RouterBookReservation
{
    private BookReservationController $controller;

    public function __construct(BookReservationController $controller)
    {
      $this->controller = $controller;
    }

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

  private function methodNotAllowed(): void
  {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
  }
}
