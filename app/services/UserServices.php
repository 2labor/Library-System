<?php
namespace App\Services;

use App\Domain\Entity\User;
use App\Domain\Entity\Account;

interface UserServices {

  public function createUser(
    string $name,
    string $surname,
    string $addressLine1,
    string $addressLine2,
    ?string $addressLine3 = null,
    string $city,
    Account $account
  ): User;
  
  public function login(string $loginData, string $password): ?User;
  public function logout(User $user): bool;
  public function getUserById(int $id): ?User;
  public function updateUser(User $user): bool;
  public function deleteUser(int $id): bool;
}