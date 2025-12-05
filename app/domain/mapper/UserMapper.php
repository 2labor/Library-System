<?php
/*
 * Inputs:
 * - Methods to map between User entity and UserDto
 * - Methods to map between User entity and associative array (database row)
 *
 * Outputs:
 * - Mapped UserDto or associative array
 *
 * File: app/domain/mapper/UserMapper.php
 */
namespace App\Domain\Mapper;

use App\Domain\Entity\User;
use App\Domain\Entity\Account;
use App\Domain\Dto\UserDto;
use App\Domain\Dto\AccountDto;

interface UserMapper {
  public function toDto(User $user): UserDto;
  public function fromDtoNew(UserDto $userDto, AccountDto $accountDto, string $plainPassword): User;
  public function fromDtoExisting(UserDto $userDto, Account $account): User;
  public function fromRow(array $row): User;
  public function toRow(User $user): array;
}
