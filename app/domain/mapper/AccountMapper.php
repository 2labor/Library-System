<?php
/*
 * Inputs:
 * - Methods to map between Account entity and AccountDto
 * - Methods to map between Account entity and associative array (database row)
 *
 * Outputs:
 * - Mapped AccountDto or associative array
 *
 * File: app/domain/mapper/AccountMapper.php
 */
namespace App\Domain\Mapper;

use App\Domain\Entity\Account;
use App\Domain\Dto\AccountDto;

interface AccountMapper {
  public function toDto(Account $account): AccountDto;
  public function fromDto(AccountDto $dto, string $plainPassword): Account;
  public function fromRow(array $row): Account;
  public function toRow(Account $account):  array;
}