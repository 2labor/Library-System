<?php
namespace App\Controller;

interface CategoriesController {
  public function getAll(): void;
  public function getById(int $id): void;
} 