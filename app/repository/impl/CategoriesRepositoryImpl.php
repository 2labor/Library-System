<?php
/*
 * Inputs:
 * - Methods to perform CRUD operations on Category entities
 *
 * Outputs:
 * - Category entities retrieved or modified in the database
 *
 * File: app/repository/impl/CategoriesRepositoryImpl.php
 */
namespace App\Repository\Impl;

use App\Repository\CategoriesRepository;
use App\Domain\Entity\Category;
use PDO;

class CategoriesRepositoryImpl implements CategoriesRepository {

  private PDO $pdo;

  public function __construct(
    PDO $pdo
  ) {
    $this->pdo = $pdo;
  }

  /**
   * Retrieves all categories from the database.
   *
   * @return array An array of categories with their IDs and names.
   */
  public function findAll(): array {
    $sql = "SELECT id, name FROM categories";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) return [];

    return $rows;
  }

  /**
   * Finds a Category by its ID.
   *
   * @param int $id The ID of the Category to find.
   * @return Category|null The found Category entity or null if not found.
   */
  public function findCategoryById(int $id): ?Category {
    $sql = "SELECT * FROM categories WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$row) return null;

    return new Category(
      id: $row['id'],
      name: $row['name']
    );
  }
}