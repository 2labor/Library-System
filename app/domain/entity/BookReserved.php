<?php
namespace App\Domain\Entity;

use DateTime;

class BookReserved {
  private ?int $id = null;
  private string $isbn;
  private int $userId;
  private DateTime $reservedDate;
  private DateTime $createdAt;
  private DateTime $updatedAt;

  public function __construct(
    string $isbn,
    int $userId,
    DateTime $reservedDate
  ) {
    $this->isbn = $isbn;
    $this->userId = $userId;
    $this->reservedDate = $reservedDate;
    $this->createdAt = new DateTime();
    $this->updatedAt = new DateTime(); 
  }

  public function getId(): ?int {
    return $this->id;
  } 

  public function getIsbn(): string {
    return $this->isbn;
  }

  public function getUserId(): int {
    return $this->userId;
  }

  public function getReservedDate(): DateTime {
    return $this->reservedDate;
  }

  public function getCreatedAt(): DateTime {
    return $this->createdAt;
  }

  public function getUpdatedAt(): DateTime {
    return $this->updatedAt;
  }

  public function setId(int $id): void {
    $this->id = $id;
  }

  public function setReservedDate(DateTime $reservedDate): void {
    $this->reservedDate = $reservedDate;
    $this->updatedAt = new DateTime();
  } 
  
  public function setCreatedAt(DateTime $date): void {
    $this->createdAt = $date;
  }

  public function setUpdatedAt(DateTime $date): void {
    $this->updatedAt = $date;
  }
}