<?php
namespace App\Controller\Impl;

use App\Controller\UserController;
use App\Services\UserServices;
use App\Services\AccountServices;
use Exception;

class UserControllerImpl implements UserController {

  private UserServices $userService;
  private AccountServices $accountService;

  public function __construct(UserServices $userService, AccountServices $accountService) {
      $this->userService = $userService;
      $this->accountService = $accountService;
  }

  public function createUser(): void {
    try {
      $data = $this->getJsonBody();

      $email = $data['email'] ?? null;
      if (!$email) throw new Exception("Email is required");

      $account = $this->accountService->getAccountByEmail($email);
      if (!$account) throw new Exception("Account not found.");

      $user = $this->userService->createUser(
        $data['name'],
        $data['surname'],
        $data['addressLine1'],
        $data['addressLine2'],
        $data['addressLine3'] ?? null,
        $data['city'],
        $account
      );

      $this->jsonResponse([
        'user_id' => $user->getId(),
        'message' => 'User created successfully'
      ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  public function updateUser(): void {
    try {
      if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
        throw new Exception("Unauthorized");
      }

      $userId = $_SESSION['user_id'];
      $user = $this->userService->getUserById($userId);
      if (!$user) throw new Exception("User not found");

      $data = $this->getJsonBody();

      $user->setName($data['name'] ?? $user->getName());
      $user->setSurname($data['surname'] ?? $user->getSurname());
      $user->setAddressLine1($data['addressLine1'] ?? $user->getAddressLine1());
      $user->setAddressLine2($data['addressLine2'] ?? $user->getAddressLine2());
      $user->setAddressLine3($data['addressLine3'] ?? $user->getAddressLine3());
      $user->setCity($data['city'] ?? $user->getCity());

      $this->userService->updateUser($user);

      $this->jsonResponse(['message' => 'User updated successfully']);
    } catch (Exception $e) {
      $this->errorResponse($e->getMessage(), 403);
    }
  }

  public function login(): void {
    try {
      $data = $this->getJsonBody();
      $user = $this->userService->login($data['login'], $data['password']);

      if (!$user) {
        $this->errorResponse("Invalid credentials", 401);
        return;
      }

      $this->jsonResponse([
        'user_id' => $user->getId(),
        'message' => 'Login successful'
      ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  public function logout(): void {
    try {
      if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
        $this->errorResponse("User is not logged in", 400);
        return;
      }

      $userId = $_SESSION['user_id'];
      $user = $this->userService->getUserById($userId);

      if (!$user) {
        $this->errorResponse("User not found", 404);
        return;
      }

      if (!$this->userService->logout($user)) {
        $this->errorResponse("Logout failed", 400);
        return;
      }

      $this->jsonResponse([
        'message' => 'Logout successful'
      ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  public function getUserById(int $id): void {
    try {
      $user = $this->userService->getUserById($id);
      if (!$user) throw new Exception("User not found");

      $this->jsonResponse([
        'user' => [
        'id' => $user->getId(),
        'name' => $user->getName(),
        'surname' => $user->getSurname(),
        'addressLine1' => $user->getAddressLine1(),
        'addressLine2' => $user->getAddressLine2(),
        'addressLine3' => $user->getAddressLine3(),
        'city' => $user->getCity(),
        'account_id' => $user->getAccount()->getId()
        ]
      ]);

      } catch (Exception $e) {
        $this->errorResponse($e->getMessage());
      }
  }

  public function deleteUser(int $id): void {
    try {
      if (!$this->userService->deleteUser($id)) throw new Exception("Delete failed");

      $this->jsonResponse(['message' => 'User deleted successfully']);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  private function getJsonBody(): array {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    if (!is_array($data)) throw new Exception("Invalid JSON");
    return $data;
  }

  private function jsonResponse($data, int $status = 200): void {
    if (ob_get_level() > 0) {
        ob_clean();
    }

    http_response_code($status);
    header("Content-Type: application/json");

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}


  private function errorResponse(string $msg, int $status = 400): void {
    $this->jsonResponse([
      "error" => $msg,
      "status" => $status
    ], $status);
  }
}
