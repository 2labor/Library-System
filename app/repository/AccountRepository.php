<?php
/*
 * Inputs:
 * - Methods to perform CRUD operations on Account entities
 *
 * Outputs:
 * - Account entities retrieved or modified in the database
 *
 * File: app/repository/AccountRepository.php
 */
namespace App\Repository;

use App\Domain\Entity\Account;

interface AccountRepository {
  public function create(Account $account): Account;
  public function update(Account $account): Account;
  public function delete(int $id): bool;
  public function findAccountById(int $id): ?Account;
  public function findAccountByLogin(string $login): ?Account;
  public function findAccountByEmail(string $email): ?Account;
}