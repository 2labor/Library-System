<?php

namespace App\Controller\Impl;

use App\Controller\AccountController;
use App\Services\AccountServices;
use Exception;

class AccountControllerImpl implements AccountController {
  private AccountServices $services;

  public function __construct(AccountServices $services) {
      $this->services = $services;
  }

  public function register(): void {
    try {
      $data = $this->getJsonBody();
      $account = $this->services->registerAccount(
      $data['login'] ?? '',
      $data['email'] ?? '',
      $data['password'] ?? '',
      $data['confirm_password'] ?? '',
      (int)($data['mobile_number'] ?? 0),
      (int)($data['telephone_number'] ?? 0));

      $this->jsonResponse([
        'message' => 'Account registered successfully. Verification email sent.'
      ]);

    } catch (Exception $e) {
      $this->jsonResponse([
        'error' => $e->getMessage()
      ], 400);
    }
  }

  public function verifyEmail(): void {
    try {
      $data = $this->getJsonBody();
      $result = $this->services->verifyEmail(
        $data['email'],
        $data['code']
      );

      $this->jsonResponse([
        'verified' => $result,
        'message' => 'Email verified successfully'
      ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  public function resetPassword(): void {
    try {
      $data = $this->getJsonBody();
      $this->services->resetPassword($data['email']);

      $this->jsonResponse([
        'message' => 'Password reset email sent.'
      ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  public function resetPasswordWithToken(): void {
    try {
      $data = $this->getJsonBody();
      $token = $data['token'] ?? null;
      $newPassword = $data['new_password'] ?? null;

      if (!$token || !$newPassword) {
        $this->jsonResponse([
          'error' => 'Token and new password are required'
        ], 400);
        return;
      }

      $this->services->resetPasswordWithToken($token, $newPassword);

      $this->jsonResponse([
          'message' => 'Password has been reset successfully'
      ]);
    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }


  public function changePassword(): void {
    try {
      $data = $this->getJsonBody();
      $account = $this->services->getAccountById($data['account_id']);
      $this->services->changePassword($account, $data['old_password'], $data['new_password']);

      $this->jsonResponse([
        'message' => 'Password changed successfully.'
      ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  public function getById(int $id): void {
    try {
    
      $account = $this->services->getAccountById($id);
      if (!$account) {
        throw new Exception("Account not found");
      }

      $this->jsonResponse([
        'account' => [
        'id' => $account->getId(),
        'login' => $account->getLogin(),
        'email' => $account->getEmail(),
        'verified' => $account->isVerified(),
        'mobile_number' => $account->getMobileNumber(),
        'telephone_number' => $account->getTelephoneNumber()
        ]
      ]);

    } catch (Exception $e) {
      $this->errorResponse($e->getMessage());
    }
  }

  public function delete(int $id): void {
    try {
      $this->services->deleteAccount($id);

      $this->jsonResponse([
        'message' => 'Account deleted successfully',
        'deleted' => true
      ]);

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
