<?php
namespace App\Controller\Impl;

use App\Controller\BookController;
use App\Services\BookServices;
use App\Domain\Mapper\BookMapper;
use Exception;

/**
 * BookControllerImpl
 *
 * HTTP controller handling book-related endpoints.
 *
 * Responsibilities:
 * - Parse JSON input and query parameters
 * - Delegate business logic to BookServices
 * - Map domain objects to DTOs via BookMapper
 * - Return JSON responses and standardized error payloads
 */
class BookControllerImpl implements BookController {

  private BookServices $services;
  private BookMapper $mapper;

  /**
   * Constructor.
   *
   * @param BookServices $services Service layer for book operations.
   * @param BookMapper $mapper Mapper to convert between arrays, domain objects and DTOs.
   */
  public function __construct(
    BookServices $services,
    BookMapper $mapper
  ) {
    $this->services = $services;
    $this->mapper = $mapper;
  }

  /**
   * Add a new book.
   *
   * Expects JSON request body describing the book fields (e.g. title, author,
   * isbn, category, etc.). Uses the mapper to create a domain Book from the
   * input array, delegates creation to the service and returns the created
   * book as a DTO with HTTP 201.
   *
   * Errors are returned as JSON with appropriate status (default 400).
   *
   * @throws Exception on invalid JSON or service failure.
   * @return void Sends JSON response and exits.
   */
  public function add(): void{
    try {
      $data = $this->getJsonBody();

      $book = $this->mapper->fromArray($data);
      $created = $this->services->addBook($book);

      $this->jsonResponse(
        $this->mapper->toDto($created),
        201
      );

    } catch(Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  /**
   * Update a book by ISBN.
   *
   * Path parameter: isbn. Expects JSON body with updated fields. The ISBN
   * path parameter is merged into the payload to ensure correct target.
   * Uses mapper -> domain object -> service to perform update; returns the
   * updated book DTO. If book not found, returns a 400/exception.
   *
   * @param string $isbn ISBN of the book to update.
   * @throws Exception if book not found or on invalid input.
   * @return void Sends JSON response and exits.
   */
  public function update(string $isbn): void{
    try {
      $data = $this->getJsonBody();
      $data['isbn'] = $isbn;

      $book = $this->mapper->fromArray($data);
      $updated = $this->services->updateBook($book);

      if (!$updated) {
        throw new Exception("Book not found");
      }

      $this->jsonResponse(
        $this->mapper->toDto($updated)
      );
    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  /**
   * Delete a book by ISBN.
   *
   * Delegates deletion to the service. Returns a JSON object indicating
   * success { "deleted": true } or throws an exception if not found.
   *
   * @param string $isbn ISBN of the book to delete.
   * @throws Exception on deletion failure or if not found.
   * @return void Sends JSON response and exits.
   */
  public function delete(string $isbn): void{
    try {
      $response = $this->services->deleteBook($isbn);

      if (!$response) {
        throw new Exception("Book not found");
      }

      $this->jsonResponse(['deleted' => true]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  /**
   * Find books by query parameters or return all.
   *
   * If query param 'isbn' is present delegates to findByIsbn. Otherwise
   * treats $_GET as search criteria and returns an array of book DTOs.
   *
   * Supported criteria depend on service implementation (title, author,
   * category, availability, etc.).
   *
   * @throws Exception on service failure or invalid criteria.
   * @return void Sends JSON array of DTOs and exits.
   */
  public function find(): void {
    try {
      if (!empty($_GET['isbn'])) {
        $this->findByIsbn($_GET['isbn']);
        return;
      }
      $criteria = $_GET;  
      $books = $this->services->findBook($criteria);

      $dtoList = array_map(
        fn($b) => $this->mapper->toDto($b),
        $books
      );

      $this->jsonResponse($dtoList);
    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  /**
   * Find a single book by ISBN.
   *
   * Returns a single-element array with the book DTO or an empty array with
   * HTTP 404 when not found.
   *
   * @param string $isbn ISBN to look up.
   * @throws Exception on service error.
   * @return void Sends JSON and exits.
   */
  public function findByIsbn(string $isbn): void {
    try {
      $book = $this->services->findBookByIsbn($isbn);

      if (!$book) {
            $this->jsonResponse([], 404);
            return;
        }

        $this->jsonResponse([$this->mapper->toDto($book)]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  /**
   * List all available books.
   *
   * Delegates to the service which returns only available books. Maps results
   * to DTOs and returns JSON array.
   *
   * @throws Exception on service failure.
   * @return void Sends JSON array of available book DTOs and exits.
   */
  public function findAllAvailable(): void{
    try {
      $books = $this->services->findAllAvailableBooks();

      $dtoList = array_map(
        fn($b) => $this->mapper->toDto($b),
        $books
      );

      $this->jsonResponse($dtoList);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  /**
   * Toggle availability flag for a book.
   *
   * Flips the availability status for the given ISBN. Returns a JSON status
   * message when successful. Throws if the book cannot be found.
   *
   * @param string $isbn ISBN of the book to toggle.
   * @throws Exception if book not found or service fails.
   * @return void Sends JSON response and exits.
   */
  public function toggleAvailability(string $isbn): void{
    try {
      $success = $this->services->toggleBookAvailability($isbn);

      if (!$success) {
        throw new Exception("Book not found");
      }

      $this->jsonResponse([
        "isbn" => $isbn,
        "status" => "updated"
      ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  /**
   * Read and decode JSON request body.
   *
   * Reads php://input, decodes JSON to associative array and validates.
   *
   * @throws Exception if body is not valid JSON or not an array.
   * @return array Decoded request data.
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
   * Send JSON response and terminate execution.
   *
   * Sets HTTP status code and Content-Type header, outputs encoded JSON and
   * exits. Cleans output buffer if active.
   *
   * @param mixed $data Data to encode as JSON.
   * @param int $status HTTP status code (default 200).
   * @return void This method exits after sending output.
   */
  private function jsonResponse($data, int $status = 200): void {
    if (ob_get_level() > 0) {
      ob_clean();
    }

    http_response_code($status);
    header("Content-Type: application/json");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
  }


  /**
   * Send standardized error JSON response.
   *
   * Format: { "error": "...", "status": <code> }
   *
   * @param string $msg Error message.
   * @param int $status HTTP status code (default 400).
   * @return void This method exits after sending output.
   */
  private function errorResponse(string $msg, int $status = 400): void {
    $this->jsonResponse([
      "error" => $msg,
      "status" => $status
    ], $status);
  }
}