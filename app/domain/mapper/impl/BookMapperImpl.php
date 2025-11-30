<?php
namespace App\Domain\Mapper\Impl;

use App\Domain\Mapper\BookMapper;
use App\Domain\Entity\Book;
use App\Domain\Dto\BookDto;
use App\Domain\Entity\Category;
use App\Domain\Dto\CategoryDto;
use DateTime;

class BookMapperImpl implements BookMapper {
  public function toDto(Book $book): BookDto {
    $categoryDto = null;
    $category = $book->getCategory();
    if ($category !== null) {
      $categoryDto = new CategoryDto(
        $category->getName(),
        $category->getId()
      );
    }

    return new BookDto(
      $book->getIsbn(),
      $book->getTitle(),
      $book->getImageUrl(),
      $book->getAuthor(),
      $book->getEdition(),
      $book->getYear(),
      $book->getAvailable(),
      $categoryDto
    );
  }

 public function fromDto(BookDto $dto): Book {
    $category = null;
    $dtoCategory = $dto->getCategoryDto();
    if ($dtoCategory !== null) {
      $category = new \App\Domain\Entity\Category(
        $dtoCategory->getName(),
        $dtoCategory->getId()
      );
    }

    return new Book(
      $dto->getIsbn(),
      $dto->getTitle(),
      $dto->getImageUrl(),
      $dto->getAuthor(),
      $dto->getEdition(),
      $dto->getYear(),
      $dto->isAvailable(),
      $category
    );
  }


  public function fromRow(array $row): Book {
    $category = null;
    if (!empty($row['category_id']) && !empty($row['category_name'])) {
      $category = new Category($row['category_name'], (int)$row['category_id']);
    } elseif (!empty($row['category_id'])) {
      $category = new Category('', (int)$row['category_id']);
    }

    $book = new Book(
      $row['isbn'],
      $row['title'],
      $row['imageUrl'],
      $row['author'],
      $row['edition'],
      $row['year'],
      $row['available'] = (bool)$row['available'],
      $category
    );

    $book->setCreatedAt(new DateTime($row['created_at']));
    $book->setUpdatedAt(new DateTime($row['updated_at']));

    return $book;
}


  public function toRow(Book $book): array {
    return [
      'isbn' => $book->getIsbn(),
      'title' => $book->getTitle(),
      'imageUrl' => $book->getImageUrl(),
      'author' => $book->getAuthor(),
      'edition' => $book->getEdition(),
      'year' => $book->getYear(),
      'available' => $book->getAvailable(),
      'category_id' => $book->getCategory()->getId(),
      'created_at' => $book->getCreatedAt()->format('Y-m-d H:i:s'),
      'updated_at' => $book->getUpdatedAt()->format('Y-m-d H:i:s'),
    ];
  }

  public function fromArray(array $data): Book {
    $categoryDto = null;
    if (!empty($data['category']) && is_array($data['category'])) {
      $categoryDto = new CategoryDto(
        $data['category']['name'] ?? null,
        isset($data['category']['id']) ? (int)$data['category']['id'] : null
      );
    }


    $dto = new BookDto(
      $data['isbn'],
      $data['title'],
      $data['imageUrl'],
      $data['author'],
      (int)$data['edition'],
      (int)$data['year'],
      (bool)$data['available'],
      $categoryDto
    );

    return $this->fromDto($dto);
  }
}