<?php
namespace App\Controller\Impl;

use App\Controller\BookReservationController;
use App\Services\BookReservedServices;
use Exception;

class BookReservationControllerImpl implements BookReservationController {
  private BookReservedServices $service;

  public function __construct(BookReservedServices $service) {
    $this->service = $service;
  }

  public function reserve(): void {
    try {
      $data = $this->getJsonBody();

      $isbn = $data['isbn'] ?? null;
      $userId = $data['userId'] ?? null;

      if (!$isbn || !$userId) {
        throw new Exception("isbn and userId are required.");
      }

      $reservation = $this->service->reserveBook($isbn, (int)$userId);

      $this->jsonResponse([
        'message' => 'Book reserved successfully',
        'reservation' => [
          'id' => $reservation->getId(),
          'isbn' => $reservation->getIsbn(),
          'userId' => $reservation->getUserId(),
          'reservedUntil' => $reservation->getReservedDate()->format('Y-m-d')
        ]
      ]);

    } catch (Exception $e) {
        $this->errorResponse($e->getMessage());
    }
  }

  public function extend(): void {
    try {
      $isbn = $_GET['isbn'] ?? null;
      if (!$isbn) throw new Exception("Reservation isbn is required");

      $reservation = $this->service->getReservationByBook($isbn);
      if (!$reservation) throw new Exception("Reservation not found");

      $updated = $this->service->extendReservation($reservation);

      $this->jsonResponse([
        'message' => 'Reservation extended successfully',
        'newReservedUntil' => $updated->getReservedDate()->format('Y-m-d')
      ]);

      } catch (Exception $e) {
        $this->errorResponse($e->getMessage());
      }
  }


  public function cancel(): void {
    try {
      $id = $_GET['id'] ?? null;
      if (!$id) throw new Exception("Reservation id is required");

      $this->service->cancelReservation((int)$id);

      $this->jsonResponse([
        'message' => "Reservation #$id canceled successfully"
      ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  public function getByBook(string $isbn): void {
    try {
      $reservation = $this->service->getReservationByBook($isbn);

      if (!$reservation) {
        $this->jsonResponse(["message" => "No reservation found"], 204);
        return;
      }

        $this->jsonResponse([
          'reservation' => [
            'id' => $reservation->getId(),
            'isbn' => $reservation->getIsbn(),
            'userId' => $reservation->getUserId(),
            'reservedUntil' => $reservation->getReservedDate()->format('Y-m-d')
          ]
        ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  public function getByUser(int $userId): void {
    try {
      $reservations = $this->service->getReservationByUserId($userId);

      $this->jsonResponse([
        'reservations' => array_map(function ($r) {
          return [
            'id' => $r->getId(),
            'isbn' => $r->getIsbn(),
            'reservedUntil' => $r->getReservedDate()->format('Y-m-d')
          ];
        }, $reservations)
      ]);

    } catch (Exception $e) {
        $this->errorResponse($e->getMessage());
    }
  }

  private function getJsonBody(): array {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON");
    }

    return $data;
  }

  private function jsonResponse($data, int $status = 200): void {
    if (ob_get_level() > 0) ob_clean();

    http_response_code($status);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
  }

  private function errorResponse(string $msg, int $status = 400): void {
    $this->jsonResponse([
      "error" => $msg,
      "status" => $status
    ], $status);
  }
}
