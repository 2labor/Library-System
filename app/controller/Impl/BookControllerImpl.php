<?php
namespace App\Controller\Impl;

use App\Controller\BookController;
use App\Services\BookServices;
use App\Domain\Mapper\BookMapper;
use Exception;

class BookControllerImpl implements BookController {

  private BookServices $services;
  private BookMapper $mapper;

  public function __construct(
    BookServices $services,
    BookMapper $mapper
  ) {
    $this->services = $services;
    $this->mapper = $mapper;
  }

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

  private function getJsonBody(): array {
    $json = file_get_contents("php://input");

    $data = json_decode($json, true);

    if (!is_array($data)) {
      throw new Exception("Invalid JSON");
    }

    return $data;
  }

  private function jsonResponse($data, int $status = 200): void {
    if (ob_get_level() > 0) {
      ob_clean();
    }

    http_response_code($status);
    header("Content-Type: application/json");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
  }


  private function errorResponse(string $msg, int $status = 400): void {
    $this->jsonResponse([
      "error" => $msg,
      "status" => $status
    ], $status);
  }
}