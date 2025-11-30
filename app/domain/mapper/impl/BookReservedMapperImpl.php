<?php
namespace App\Domain\Mapper\Impl;

use App\Domain\Mapper\BookReservedMapper;
use App\Domain\Entity\BookReserved;
use App\Domain\Dto\BookReservedDto;
use DateTime;

class BookReservedMapperImpl implements BookReservedMapper {

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
