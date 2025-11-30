<?php
namespace App\Services\Impl;

use App\Repository\UserRepository;
use App\Services\UserServices;
use App\Services\AccountServices;
use App\Domain\Entity\Account;
use App\Domain\Entity\User;
use Exception;

class UserServicesImpl implements UserServices {

  private UserRepository $repo;
  private AccountServices $accountService;

  public function __construct(
    UserRepository $repo, 
    AccountServices $accountService
  ){
    $this->repo = $repo;
    $this->accountService = $accountService;

    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
  }

  public function createUser(
    string $name,
    string $surname,
    string $addressLine1,
    string $addressLine2,
    ?string $addressLine3,
    string $city,
    Account $account
  ): User {
    if (empty($name) || empty($surname) || empty($addressLine1) || empty($city)) {
      throw new Exception("Required fields cannot be empty!");
    }

    $user = new User(
      null,
      $name,
      $surname,
      $addressLine1,
      $addressLine2,
      $addressLine3,
      $city,
      $account
    );

    $this->repo->create($user);
    
    return $user;
  }

  public function login(string $loginData, string $password): ?User {

    $account = $this->accountService->getAccountByLogin($loginData)
    ?? $this->accountService->getAccountByEmail($loginData);

    if (!$account) return null;
    
    if (!password_verify($password, $account->getPasswordHash())) return null;

    $user = $this->repo->findUserByAccountId($account->getId());
    if (!$user) return null;

    $_SESSION['user_id'] = $user->getId();
    $_SESSION['logged_in'] = true;

    return $user;
  }

  public function logout(?User $user = null): bool {
      if (!$user || !$this->isLoggedIn()) return false;

      $_SESSION = [];
      session_destroy();

      return true;
  }

  public function isLoggedIn(): bool {
      return !empty($_SESSION['logged_in']) && !empty($_SESSION['user_id']);
  }

  public function getUserById(int $id): ?User {
    return $this->repo->findUserById($id);
  }

  public function updateUser(User $user): bool{
    $updateUser = $this->repo->update($user);

    if ($updateUser == null) {
      throw new Exception("User not found");
    }

    return true;
  }

  public function deleteUser(int $id): bool {
    return $this->repo->delete($id);
  }
} 