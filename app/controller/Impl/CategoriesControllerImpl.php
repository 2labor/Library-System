<?php
/**
 * Purpose: Implementation of category management HTTP endpoints.
 *
 * Responsibilities:
 * - Implement methods for category CRUD (create, list, get, update, delete)
 * - Handle exceptions and return JSON responses
 *
 * Inputs:
 * - Path parameter (categoryId) in relevant methods
 *
 * Outputs:
 * - Methods return void; implementations emit JSON with category DTOs
 *
 * File: app/controller/Impl/CategoriesControllerImpl.php
 */

namespace App\Controller\Impl;

use App\Controller\CategoriesController;
use App\Repository\CategoriesRepository;
use Exception;

class CategoriesControllerImpl implements CategoriesController {

  private CategoriesRepository $repo;

  public function __construct(CategoriesRepository $repo) {
    $this->repo = $repo;
  }
  /**
   * List all categories.
   * 
   * Returns array of category DTOs.
   */
  public function getAll(): void {
    try {
      $categories = $this->repo->findAll();

      $this->jsonResponse([
        'categories' => $categories
      ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }
  /**
   * Get category by ID.
   * 
   * Path parameter: categoryId (int)
   * Returns category DTO or 404 if not found.
   */
  public function getById(int $id): void {
    try {
      $category = $this->repo->findCategoryById($id);

      if (!$category) {
        throw new Exception("Category not found");
      }

      $this->jsonResponse([
        'category' => [
          'id' => $category->getId(),
          'name' => $category->getName()
        ]
      ]);
    } catch (Exception $e) {
      $this->errorResponse($e->getMessage(), 404);
    }
  }
  /**
   * Send JSON response and exit.
   *
   * @param mixed $data Data to encode as JSON.
   * @param int $status HTTP status code (default 200).
   * @return void Outputs JSON and exits.
   */
  private function jsonResponse($data, int $status = 200): void {
    if (ob_get_level() > 0) {
      ob_clean();
    }

    http_response_code($status);
    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
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
