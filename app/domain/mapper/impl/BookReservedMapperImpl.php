<?php
/*
 * Inputs:
 * - Methods to map between BookReserved entity and BookReservedDto
 * - Methods to map between BookReserved entity and associative array (database row)
 *
 * Outputs:
 * - Mapped BookReservedDto or associative array
 *
 * File: app/domain/mapper/impl/BookReservedMapperImpl.php
 */
namespace App\Domain\Mapper\Impl;

use App\Domain\Mapper\BookReservedMapper;
use App\Domain\Entity\BookReserved;
use App\Domain\Dto\BookReservedDto;
use DateTime;

class BookReservedMapperImpl implements BookReservedMapper {

  /**
   * Maps a BookReserved domain entity to a BookReservedDto.
   *
   * @param BookReserved $bookReserved The BookReserved domain entity to map.
   * @return BookReservedDto The resulting data transfer object.
   */
  public function toDto(BookReserved $bookReserved): BookReservedDto {
    return new BookReservedDto(
      $bookReserved->getId(),
      $bookReserved->getIsbn(),
      $bookReserved->getUserId(),
      $bookReserved->getReservedDate(),
      $bookReserved->getCreatedAt(),
      $bookReserved->getUpdatedAt()
    );
  }

  /**
   * Maps a BookReservedDto object to a BookReserved domain entity.
   *
   * @param BookReservedDto $dto The data transfer object containing book reservation data.
   * @return BookReserved The mapped BookReserved domain entity.
   */
  public function fromDto(BookReservedDto $dto): BookReserved {
    $bookReserved = new BookReserved(
      $dto->getId() ?? null,
      $dto->getIsbn(),
      $dto->getUserId(),
      $dto->getReservedDate()
    );

    if ($dto->getCreatedAt()) {
      $bookReserved->setCreatedAt($dto->getCreatedAt());
    }
    if ($dto->getUpdatedAt()) {
      $bookReserved->setUpdatedAt($dto->getUpdatedAt());
    }

    return $bookReserved;
  }

  /**
   * Maps a database row to a BookReserved domain object.
   *
   * @param array $row The associative array representing a row from the database.
   *                   Expected keys: 'book_isbn', 'user_id', 'reserved_date', 'id', 'created_at', 'updated_at'.
   * @return BookReserved The mapped BookReserved object.
   * @throws Exception If date parsing fails.
   */
  public function fromRow(array $row): BookReserved {
    $bookReserved = new BookReserved(
      $row['book_isbn'],
      (int)$row['user_id'],
      new DateTime($row['reserved_date'])
    );

    $bookReserved->setId((int)$row['id']);
    $bookReserved->setCreatedAt(new DateTime($row['created_at']));
    $bookReserved->setUpdatedAt(new DateTime($row['updated_at']));

    return $bookReserved;
  }

  /**
   * Converts a BookReserved domain object into an associative array suitable for database storage.
   *
   * @param BookReserved $bookReserved The BookReserved object to convert.
   * @param bool $includeId Whether to include the 'id' field in the resulting array (default: true).
   * @return array The associative array representing the BookReserved object.
   */
  public function toRow(BookReserved $bookReserved, bool $includeId = true): array {
    $data = [
      'book_isbn' => $bookReserved->getIsbn(),
      'user_id' => $bookReserved->getUserId(),
      'reserved_date' => $bookReserved->getReservedDate()->format('Y-m-d H:i:s'),
      'created_at' => $bookReserved->getCreatedAt()->format('Y-m-d H:i:s'),
      'updated_at' => $bookReserved->getUpdatedAt()->format('Y-m-d H:i:s'),
    ];

    if ($includeId) {
      $data['id'] = $bookReserved->getId();
    }

    return $data;
  }
}
