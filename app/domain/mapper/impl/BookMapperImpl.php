<?php
/*
 * Inputs:
 * - Methods to map between Book entity and BookDto
 * - Methods to map between Book entity and associative array (database row)
 *
 * Outputs:
 * - Mapped BookDto or associative array
 *
 * File: app/domain/mapper/impl/BookMapperImpl.php
 */
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

/**
 * Maps a BookDto object to a Book domain entity.
 *
 * @param BookDto $dto The data transfer object containing book information.
 * @return Book The mapped Book domain entity.
 */
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


  /**
   * Maps a database row to a Book domain object.
   *
   * @param array $row The associative array representing a book row from the database.
   * Expected keys: 'isbn', 'title', 'imageUrl', 'author', 'edition',
   * 'year', 'available', 'category_id', 'category_name', 'created_at', 'updated_at'.
   *
   * @return Book The Book object created from the provided row data.
   *
   * @throws Exception If the 'created_at' or 'updated_at' fields are not valid date strings.
   */
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


  /**
   * Converts a Book domain object into an associative array suitable for database storage.
   *
   * @param Book $book The Book object to be converted.
   * @return array An associative array representing the Book, with keys corresponding to database columns.
   */
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

  /**
   * Maps an associative array of book data to a Book domain object.
   *
   * This method constructs a BookDto from the provided array, including an optional CategoryDto
   * if category data is present, and then converts the DTO to a Book entity.
   *
   * @param array $data Associative array containing book data. Expected keys:
   * - 'isbn' (string): The ISBN of the book.
   * - 'title' (string): The title of the book.
   * - 'imageUrl' (string): The URL of the book's image.
   * - 'author' (string): The author of the book.
   * - 'edition' (int|string): The edition number of the book.
   * - 'year' (int|string): The publication year of the book.
   * - 'available' (bool|mixed): Availability status of the book.
   * - 'category' (array|null): Optional. Category data with keys:
   * - 'name' (string|null): Name of the category.
   * - 'id' (int|string|null): ID of the category.
   *
   * @return Book The mapped Book domain object.
   */
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