<?php
namespace App\Domain\Entity;

use DateTime;

class Book {
  private string $isbn;
  private string $title;
  private string $imageUrl;
  private string $author;
  private int $edition;
  private int $year;
  private bool $available;
  private Category $category;
  private DateTime $createdAt;
  private DateTime $updatedAt;

  public function __construct(
    string $isbn,
    string $title, 
    string $imageUrl,
    string $author, 
    int $edition,  
    int $year, 
    bool $available,
    ?Category $category = null
    ) {
      $this->isbn = $isbn;
      $this->title = $title;
      $this->imageUrl = $imageUrl;
      $this->author = $author;
      $this->edition = $edition;
      $this->year = $year;
      $this->category = $category;
      $this->available = $available;
      $this->createdAt = new DateTime();
      $this->updatedAt = new DateTime();
  }

  public function getIsbn(): string {
    return $this->isbn;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function getImageUrl(): string {
    return $this->imageUrl;
  }

  public function getAuthor(): string {
    return $this->author;
  }

  public function getEdition(): int {
    return $this->edition;
  }

  public function getYear(): int {
    return $this->year;
  }

  public function getCategory(): ?Category {
    return $this->category;
  }

  public function getAvailable(): bool {
    return $this->available;
  }

  public function getCreatedAt(): DateTime {
    return $this->createdAt;
  }

  public function getUpdatedAt(): DateTime {
    return $this->updatedAt;
  }


  public function setTitle(string $title): void {
    $this->title = $title;
}

  public function setAvailable(bool $available): void {
    $this->available = $available;
  }

  public function setCreatedAt(DateTime $date): void {
    $this->createdAt = $date;
  }

  public function setUpdatedAt(DateTime $date): void {
    $this->updatedAt = $date;
  }

}