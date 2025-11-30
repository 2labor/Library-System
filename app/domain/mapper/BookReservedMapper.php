<?php 
namespace App\Domain\Mapper;

use App\Domain\Entity\BookReserved;
use App\Domain\Dto\BookReservedDto;

interface BookReservedMapper {
  public function toDto(BookReserved $bookReserved): BookReservedDto;
  public function fromDto(BookReservedDto $dto): BookReserved;
  public function fromRow(array $row): BookReserved;
  public function toRow(BookReserved $bookReserved, bool $includeId = true): array;
}