<?php
/*
 * Inputs:
 * - Methods to perform CRUD operations on User entities
 *
 * Outputs:
 * - User entities retrieved or modified in the database
 *
 * File: app/repository/UserRepository.php
 */
namespace App\Repository;

use App\Domain\Entity\User;

interface UserRepository {
  public function create(User $user): User;
  public function update(User $user): ?User;  
  public function delete(int $id): bool; 
  public function findUserById(int $id): ?User;
  public function findUserByAccountId(int $accountId): ?User;
} 