<?php 

namespace App\Repository;

use App\Domain\Entity\Category;

interface CategoriesRepository {
  public function findAll(): array;
  public function findCategoryById(int $id): ?Category;
}