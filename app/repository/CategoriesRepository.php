<?php 
/*
 * Inputs:
 * - Methods to perform CRUD operations on Category entities
 *
 * Outputs:
 * - Category entities retrieved or modified in the database
 *
 * File: app/repository/CategoriesRepository.php
 */
namespace App\Repository;

use App\Domain\Entity\Category;

interface CategoriesRepository {
  public function findAll(): array;
  public function findCategoryById(int $id): ?Category;
}