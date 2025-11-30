<?php
namespace App\Domain\Dto;

class BookReservedDto {
  private int $id;
  private string $isbn;
  private int $userId;
  private DateTime $reservedDate;

  public function __construct(int $id, string $isbn, int $userId, DateTime $reservedDate)
  {
    $this->id = $id;
    $this->isbn = $isbn;
    $this->userId = $userId;
    $this->reservedDate = $reservedDate;
  }

  public function getId(): int {
    return $this->id;
  }
 
  public function getIsbn(): string
  {
    return $this->isbn;
  }

  public function getUserId(): int
  {
    return $this->userId;
  }

  public function getReservedDate(): DateTime
  {
    return $this->reservedDate;
  }
}