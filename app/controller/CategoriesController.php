<?php
/**
 * Purpose: Interface defining category management HTTP endpoints.
 *
 * Responsibilities:
 * - Declare method signatures for category CRUD (create, list, get, update, delete)
 * - Define contracts for category queries
 *
 * Inputs:
 * - Path parameter (categoryId) in relevant methods
 *
 * Outputs:
 * - Methods return void; implementations emit JSON with category DTOs
 *
 * File: app/controller/CategoriesController.php
 */

namespace App\Controller;

interface CategoriesController {
  public function getAll(): void;
  public function getById(int $id): void;
} 