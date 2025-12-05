<?php 
/*
 * Inputs:
 * - Methods to map between Book entity and BookDto
 * - Methods to map between Book entity and associative array (database row)
 *
 * Outputs:
 * - Mapped BookDto or associative array
 *
 * File: app/domain/mapper/BookMapper.php
 */
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