<?php
/**
 * Purpose: HTTP endpoints for book reservation flows (reserve, cancel, extend, list).
 *
 * Responsibilities:
 * - Parse JSON body and path parameters
 * - Validate reservation requests and user context
 * - Delegate business logic to BookReservationServices
 * - Use BookReservedMapper to convert entities to DTOs
 * - Return JSON responses with appropriate status codes
 * - Handle exceptions and return standardized error responses
 *
 * Inputs:
 * - JSON body (bookId, userId, reservationId, extension days, etc.)
 * - Path parameters (reservationId, userId)
 * - HTTP headers (Authorization, authentication context)
 *
 * Outputs:
 * - JSON: reservation DTOs, status messages, error objects
 * - HTTP status: 200 (success), 201 (created), 400 (invalid), 404 (not found), 500 (error)
 *
 * Errors:
 * - Throws Exception on invalid input or service failure
 * - Converts all exceptions to standardized errorResponse (JSON with error, status)
 * - Maps domain validation errors to HTTP 400
 *
 * File: app/controller/Impl/BookReservationControllerImpl.php
 */

namespace App\Controller\Impl;

use App\Controller\BookReservationController;
use App\Services\BookReservedServices;
use Exception;

class BookReservationControllerImpl implements BookReservationController {
  private BookReservedServices $service;

  public function __construct(BookReservedServices $service) {
    $this->service = $service;
  }

  /**
   * Reserve a book for the authenticated user.
   * 
   * Expects JSON: { "bookId": int, "userId": int, "start_date": "YYYY-MM-DD", "end_date": "YYYY-MM-DD" }
   * Creates a BookReserved record and returns the created DTO with status 201.
   */
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

  /**
   * Extend reservation end date.
   * 
   * Path parameter: reservationId (int)
   * Expects JSON: { "new_end_date": "YYYY-MM-DD" }
   * Returns updated reservation DTO.
   */
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

  /**
   * Cancel an existing reservation.
   * 
   * Path parameter: reservationId (int)
   * Returns success status message or error.
   */
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

  /**
   * Get reservation details for a specific book by ISBN.
   * 
   * Path parameter: isbn (string)
   * Returns reservation DTO or 204 if not found.
   */
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

  /**
   * List all reservations for a specific user.
   * 
   * Path parameter: userId (int)
   * Returns array of reservation DTOs.
   */
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

  /**
   * Helper to parse JSON body from request.
   *
   * @throws Exception on invalid JSON.
   * @return array Parsed JSON data.
   */
  private function getJsonBody(): array {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON");
    }

    return $data;
  }

  /**
   * Send JSON response and exit.
   *
   * @param mixed $data Data to encode as JSON.
   * @param int $status HTTP status code (default 200).
   * @return void Outputs JSON and exits.
   */
  private function jsonResponse($data, int $status = 200): void {
    if (ob_get_level() > 0) ob_clean();

    http_response_code($status);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
  }

  /**
   * Send standardized error JSON response.
   *
   * @param string $msg Error message.
   * @param int $status HTTP status code (default 400).
   * @return void Outputs error JSON and exits.
   */
  private function errorResponse(string $msg, int $status = 400): void {
    $this->jsonResponse([
      "error" => $msg,
      "status" => $status
    ], $status);
  }
}
