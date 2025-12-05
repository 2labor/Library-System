<?php
/*
 * Inputs:
 * - Methods to perform CRUD operations on BookReserved entities
 *
 * Outputs:
 * - BookReserved entities retrieved or modified in the database
 *
 * File: app/repository/impl/BookReservedRepositoryImpl.php
 */
namespace App\Repository\Impl;

use PDO; 
use DateTime;
use App\Repository\BookReservedRepository;
use App\Domain\Mapper\BookReservedMapper;
use App\Domain\Entity\BookReserved;


class BookReservedRepositoryImpl implements BookReservedRepository {

  private PDO $pdo;
  private BookReservedMapper $mapper;

  public function __construct(PDO $pdo, BookReservedMapper $mapper) {
    $this->pdo = $pdo;
    $this->mapper = $mapper;
  }

  /**
   * Creates a new BookReserved in the database.
   *
   * @param BookReserved $bookReserved The BookReserved entity to create.
   * @return BookReserved The created BookReserved entity.
   */
  public function create(BookReserved $bookReserved): BookReserved {
    $sql = "INSERT INTO books_reserved (
      book_isbn, user_id, reserved_date, created_at, updated_at
    ) 
    VALUES(:book_isbn, :user_id, :reserved_date, :created_at, :updated_at)";

    $stmt = $this->pdo->prepare($sql);

    $now = new DateTime();
    $data = $this->mapper->toRow($bookReserved, false);
    $data['created_at'] = $now->format('Y-m-d H:i:s');
    $data['updated_at'] = $now->format('Y-m-d H:i:s');

    $stmt->execute($data);

    $bookReserved->setId((int)$this->pdo->lastInsertId());
    $bookReserved->setCreatedAt($now);
    $bookReserved->setUpdatedAt($now);

    return $bookReserved;
  }

  /**
   * Updates an existing BookReserved in the database.
   *
   * @param BookReserved $bookReserved The BookReserved entity to update.
   * @return BookReserved The updated BookReserved entity.
   */
  public function update(BookReserved $bookReserved): BookReserved {
    $sql = "UPDATE books_reserved SET
      book_isbn = :book_isbn, 
      user_id = :user_id, 
      reserved_date = :reserved_date, 
      updated_at = :updated_at
    WHERE id = :id";

    $stmt = $this->pdo->prepare($sql);

    $param = [
      'book_isbn' => $bookReserved->getIsbn(),
      'user_id' => $bookReserved->getUserId(),
      'reserved_date' => $bookReserved->getReservedDate()->format('Y-m-d H:i:s'),
      'updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
      'id' => $bookReserved->getId()
    ];

    $stmt->execute($param);

    $bookReserved->setUpdatedAt(new DateTime());

    return $bookReserved;
  }

  /**
   * Deletes a BookReserved from the database by its ID.
   *
   * @param int $id The ID of the BookReserved to delete.
   * @return bool True if the deletion was successful, false otherwise.
   */
  public function delete(int $id): bool {
    $sql = "DELETE FROM books_reserved WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
  }

  /**
   * Finds a BookReserved by the book's ISBN.
   *
   * @param string $bookIsbn The ISBN of the book.
   * @return BookReserved|null The found BookReserved entity or null if not found.
   */
  public function findReservationByBookIsbn(string $bookIsbn): ?BookReserved {
    $sql = "SELECT * FROM books_reserved WHERE book_isbn = :book_isbn";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':book_isbn' => $bookIsbn]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$row) return null;
    
    return $this->mapper->fromRow($row);
  }
  
  /**
   * Finds all BookReserved entries for a given user ID.
   *
   * @param int $userId The ID of the user.
   * @return BookReserved[] An array of BookReserved entities for the user.
   */
  public function findReservationsByUserId(int $userId): array {
    $sql = "SELECT * FROM books_reserved WHERE user_id = :user_id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':user_id' => $userId]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) return [];

    $reservations = [];

    foreach ($rows as $row) {
      $reservations[] = $this->mapper->fromRow($row);
    }

    return $reservations;
  }

  /**
   * Finds a BookReserved by its reservation ID.
   *
   * @param int $reservationId The ID of the reservation.
   * @return BookReserved|null The found BookReserved entity or null if not found.
   */
  public function findReservationById(int $reservationId): ?BookReserved {
    $sql = "SELECT * FROM books_reserved WHERE id = :id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $reservationId]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return null;

    return $this->mapper->fromRow($row);
  }

  /**
   * Finds the most recent active BookReserved by the book's ISBN.
   *
   * @param string $isbn The ISBN of the book.
   * @return BookReserved|null The found BookReserved entity or null if not found.
   */
  public function findActiveReservationByBookIsbn(string $isbn): ?BookReserved {
    $sql = "SELECT * FROM books_reserved 
            WHERE book_isbn = :isbn 
            ORDER BY id DESC 
            LIMIT 1";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['isbn' => $isbn]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? $this->mapper->fromRow($row) : null;
  }
}