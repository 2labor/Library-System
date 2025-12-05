<?php
/*
 * Inputs:
 * - Methods to perform CRUD operations on Book entities
 *
 * Outputs:
 * - Book entities retrieved or modified in the database
 *
 * File: app/repository/impl/BookRepositoryImpl.php
 */
namespace App\Repository\Impl;

use PDO; 
use DateTime;
use App\Repository\BookRepository;
use App\Domain\Mapper\BookMapper;
use App\Domain\Entity\Book;

class BookRepositoryImpl implements BookRepository {

  private PDO $pdo;
  private BookMapper $mapper;

  public function __construct(PDO $pdo, BookMapper $mapper) {
    $this->pdo = $pdo;
    $this->mapper = $mapper;
  }

  /**
   * Creates a new Book in the database.
   *
   * @param Book $book The Book entity to create.
   * @return Book The created Book entity.
   */
  public function create(Book $book): Book {
    $sql = "INSERT INTO books (isbn, title, imageUrl, author, edition, year, available, category_id, created_at, updated_at)
        VALUES (:isbn, :title, :imageUrl, :author, :edition, :year, :available, :category_id, :created_at, :updated_at)";


    $stmt = $this->pdo->prepare($sql);

    $now = new DateTime();         
    $book->setCreatedAt($now);    
    $book->setUpdatedAt($now);

    $data = $this->mapper->toRow($book);
    $data['created_at'] = $now->format('Y-m-d H:i:s');
    $data['updated_at'] = $now->format('Y-m-d H:i:s');

    $stmt->execute($data);

    return $book;
  }

  /**
   * Updates an existing Book in the database.
   *
   * @param Book $book The Book entity to update.
   * @return Book The updated Book entity.
   */
  public function update(Book $book): Book {
    $sql = "UPDATE books 
        SET 
            title = :title,
            imageUrl = :imageUrl,
            author = :author,
            edition = :edition,
            year = :year,
            available = :available,
            category_id = :category_id,
            updated_at = :updated_at
        WHERE isbn = :isbn";

    $stmt = $this->pdo->prepare($sql);

    $params = $this->mapper->toRow($book);
    unset($params['created_at']);
    $params['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');

    $stmt->execute($params);

    $book->setUpdatedAt(new DateTime());

    return $book;
  }

  /**
   * Deletes a Book from the database by its ISBN.
   *
   * @param string $isbn The ISBN of the Book to delete.
   * @return bool True if the deletion was successful, false otherwise.
   */
  public function delete(string $isbn): bool {
    $sql = "DELETE FROM books WHERE isbn = :isbn";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':isbn' => $isbn]);

    return $stmt->rowCount() > 0;
  }

  /**
   * Finds a Book by its ISBN.
   *
   * @param string $isbn The ISBN of the Book to find.
   * @return Book|null The found Book entity or null if not found.
   */
  public function findBookByIsbn (string $isbn): ?Book {
    $sql = "SELECT * FROM books WHERE isbn = :isbn";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':isbn' => $isbn]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$row) return null;

    return $this->mapper->fromRow($row);
  }

  /**
   * Finds Books by partial criteria such as title, author, or category.
   *
   * @param array $criteria An associative array with optional keys: 'title', 'author', 'category_id'.
   * @return Book[] An array of Book entities matching the criteria.
   */
  public function findByPartial(array $criteria): array {
    $sql = "SELECT b.*, c.name AS category_name
            FROM books b
            LEFT JOIN categories c ON b.category_id = c.id";

    $conditions = [];
    $params = [];

    if (!empty($criteria['title'])) {
      $conditions[] = "b.title LIKE :title";
      $params[':title'] = '%' . $criteria['title'] . '%';
    }

    if (!empty($criteria['author'])) {
      $conditions[] = "b.author LIKE :author";
      $params[':author'] = '%' . $criteria['author'] . '%';
    }

    if (!empty($criteria['category_id'])) {
      $conditions[] = "b.category_id = :category_id";
      $params[':category_id'] = $criteria['category_id'];
    }

    if ($conditions) {
      $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    $books = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $books[] = $this->mapper->fromRow($row);
    }

    return $books;
  }

  /**
   * Retrieves all available Books from the database.
   *
   * @return Book[] An array of available Book entities.
   */
  public function findAllAvailable(): array {
    $sql = "SELECT b.*, c.name AS category_name
            FROM books b
            LEFT JOIN categories c ON b.category_id = c.id
            WHERE b.available = 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    $books = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $books[] = $this->mapper->fromRow($row);
    }

    return $books;
  }

  /**
   * Toggles the availability status of a Book by its ISBN.
   *
   * @param string $isbn The ISBN of the Book to toggle availability.
   * @return bool True if the update was successful, false otherwise.
   */
  public function toggleAvailability(string $isbn): bool {
    $sql = "UPDATE books
    SET available = NOT available,
    updated_at = :updated_at
    WHERE isbn = :isbn";

    $stmt = $this->pdo->prepare($sql);
    $now = (new DateTime()->format('Y-m-d H:i:s'));

    $stmt->execute([
      'updated_at' => $now,
      'isbn' => $isbn 
    ]);

    return $stmt->rowCount() > 0;
  }
}