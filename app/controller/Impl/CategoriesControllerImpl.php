<?php

namespace App\Controller\Impl;

use App\Controller\CategoriesController;
use App\Repository\CategoriesRepository;
use Exception;

class CategoriesControllerImpl implements CategoriesController {

  private CategoriesRepository $repo;

  public function __construct(CategoriesRepository $repo) {
    $this->repo = $repo;
  }

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

  private function jsonResponse($data, int $status = 200): void {
    if (ob_get_level() > 0) {
      ob_clean();
    }

    http_response_code($status);
    header("Content-Type: application/json; charset=UTF-8");

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
