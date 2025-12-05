<?php
/*
 * Inputs:
 * - Methods for user management operations
 *
 * Outputs:
 * - User entity or boolean status for operations
 *
 * File: app/services/impl/UserServicesImpl.php
 */
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

  /**
   * Creates a new user in the repository.
   *
   * @param string $name The name of the user.
   * @param string $surname The surname of the user.
   * @param string $addressLine1 The first line of the user's address.
   * @param string $addressLine2 The second line of the user's address.
   * @param string|null $addressLine3 The third line of the user's address (optional).
   * @param string $city The city of the user.
   * @param Account $account The associated Account entity for the user.
   * @return User The newly created User entity.
   * @throws Exception If required fields are empty or there is an error during creation.
   */
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

  /**
   * Logs in a user using their login data and password.
   *
   * @param string $loginData The login data (username or email).
   * @param string $password The password for authentication.
   * @return User|null The logged-in User entity, or null if authentication fails.
   */
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

  /**
   * Logs out the currently logged-in user.
   *
   * @param User|null $user The User entity to log out (optional).
   * @return bool True if the user was logged out successfully, false otherwise.
   */
  public function logout(?User $user = null): bool {
      if (!$user || !$this->isLoggedIn()) return false;

      $_SESSION = [];
      session_destroy();

      return true;
  }

  /**
   * Checks if a user is currently logged in.
   *
   * @return bool True if a user is logged in, false otherwise.
   */
  public function isLoggedIn(): bool {
      return !empty($_SESSION['logged_in']) && !empty($_SESSION['user_id']);
  }

  /**
   * Retrieves a user by their ID.
   *
   * @param int $id The ID of the user to retrieve.
   * @return User|null The found User entity or null if not found.
   */
  public function getUserById(int $id): ?User {
    return $this->repo->findUserById($id);
  }

  /**
   * Updates an existing user in the repository.
   *
   * @param User $user The User entity with updated information.
   * @return bool True if the user was successfully updated.
   * @throws Exception If the user is not found or there is an error during update.
   */
  public function updateUser(User $user): bool{
    $updateUser = $this->repo->update($user);

    if ($updateUser == null) {
      throw new Exception("User not found");
    }

    return true;
  }

  /**
   * Deletes a user by their ID.
   *
   * @param int $id The ID of the user to delete.
   * @return bool True if the user was successfully deleted, false otherwise.
   */
  public function deleteUser(int $id): bool {
    return $this->repo->delete($id);
  }
} 