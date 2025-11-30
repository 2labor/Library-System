<?php
namespace App\Domain\Dto;

use App\Domain\Dto\CategoryDto;

class BookDto implements \JsonSerializable {
  private string $isbn;
  private string $title;
  private string $imageUrl;
  private string $author;
  private int $edition;
  private int $year;
  private bool $available;
  private ?CategoryDto $category = null;

  public function __construct(
    string $isbn,
    string $title,
    string $imageUrl,
    string $author,
    int $edition,
    int $year,
    bool $available,
    ?CategoryDto $category = null
  ) {
    $this->isbn = $isbn;
    $this->title = $title;
    $this->imageUrl = $imageUrl;
    $this->author = $author;
    $this->edition = $edition;
    $this->year = $year;
    $this->available = $available;
    $this->category = $category;
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
  public function isAvailable(): bool { 
    return $this->available; 
  }
  public function getCategoryDto(): ?CategoryDto { 
    return $this->category; 
  }

  public function jsonSerialize(): array {
    return [
      'isbn' => $this->isbn,
      'title' => $this->title,
      'imageUrl' => $this->imageUrl,
      'author' => $this->author,
      'edition' => $this->edition,
      'year' => $this->year,
      'available' => $this->available,
      'category' => $this->category ? $this->category->jsonSerialize() : null
    ];
  }
}
