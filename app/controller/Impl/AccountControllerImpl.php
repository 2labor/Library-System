<?php

namespace App\Controller\Impl;

use App\Controller\AccountController;
use App\Services\AccountServices;
use Exception;

/**
 * AccountControllerImpl
 *
 * Controller implementation for account-related HTTP endpoints.
 */
class AccountControllerImpl implements AccountController {
  private AccountServices $services;

  /**
   * Constructor.
   *
   * @param AccountServices $services Service layer for account operations.
   */
  public function __construct(AccountServices $services) {
      $this->services = $services;
  }

  /**
   * Register a new account.
   *
   * Reads JSON body with keys: login, email, password, confirm_password,
   * mobile_number, telephone_number. Calls the service to register the
   * account and returns a JSON success message. On error returns a JSON
   * error response with status 400.
   *
   * @throws Exception When input is invalid or service fails.
   * @return void Outputs JSON and exits.
   */
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

  /**
   * Verify an account email.
   *
   * Expects JSON body with 'email' and 'code'. Calls service to verify the
   * email and returns verification status and message.
   *
   * @throws Exception On missing input or verification failure.
   * @return void Outputs JSON and exits.
   */
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

  /**
   * Request password reset.
   *
   * Expects JSON body with 'email'. Triggers a reset email via the service
   * and returns a success message.
   *
   * @throws Exception On invalid input or service failure.
   * @return void Outputs JSON and exits.
   */
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

  /**
   * Complete password reset using a token.
   *
   * Expects JSON body with 'token' and 'new_password'. Validates presence of
   * both values, calls the service to apply the new password, and returns a
   * confirmation message.
   *
   * @throws Exception When token/new password missing or service fails.
   * @return void Outputs JSON and exits.
   */
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


  /**
   * Change an existing account password.
   *
   * Expects JSON body with 'account_id', 'old_password', 'new_password'.
   * Retrieves the account by id and delegates password change to the service.
   *
   * @throws Exception When account not found or password change fails.
   * @return void Outputs JSON and exits.
   */
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

  /**
   * Get account details by id.
   *
   * @param int $id Account identifier.
   * @throws Exception If account is not found.
   * @return void Outputs JSON with account data and exits.
   */
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

  /**
   * Delete an account by id.
   *
   * @param int $id Account identifier to delete.
   * @throws Exception On deletion failure.
   * @return void Outputs JSON confirmation and exits.
   */
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


  /**
   * Read and decode JSON request body.
   *
   * @throws Exception If request body is not valid JSON or not an array.
   * @return array Decoded JSON as associative array.
   */
  private function getJsonBody(): array {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    if (!is_array($data)) throw new Exception("Invalid JSON");
    return $data;
  }

  /**
   * Send JSON response and terminate execution.
   *
   * Sets HTTP status code and Content-Type header, outputs encoded JSON and
   * calls exit.
   *
   * @param mixed $data Data to encode as JSON.
   * @param int $status HTTP status code (default 200).
   * @return void This method exits after sending output.
   */
  private function jsonResponse($data, int $status = 200): void {
    if (ob_get_level() > 0) {
      ob_clean();
    }
    
    http_response_code($status);
    header("Content-Type: application/json");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
  }

  /**
   * Send standardized error JSON response.
   *
   * @param string $msg Error message.
   * @param int $status HTTP status code (default 400).
   * @return void This method exits after sending output.
   */
  private function errorResponse(string $msg, int $status = 400): void {
    $this->jsonResponse([
      "error" => $msg,
      "status" => $status
    ], $status);
  }
}
