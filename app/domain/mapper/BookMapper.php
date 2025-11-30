<?php 
namespace App\Domain\Mapper;

use App\Domain\Entity\Book;
use App\Domain\Dto\BookDto;

interface BookMapper {
  public function toDto(Book $book): BookDto;
  public function fromDto(BookDto $dto): Book;
  public function fromRow(array $row): Book;
  public function toRow(Book $book): array;
  public function fromArray(array $data): Book;
}