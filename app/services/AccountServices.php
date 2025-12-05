<?php
/*
 * Inputs:
 * - Methods for account management operations
 *
 * Outputs:
 * - Account entity or boolean status for operations
 *
 * File: app/services/AccountServices.php
 */
namespace App\Services;

use App\Domain\Entity\Account;

interface AccountServices {
  public function registerAccount(string $login, string $email, string $rowPassword, string $confirmPassword, int $mobileNumber, int $telephoneNumber): Account;
  public function verifyEmail(string $email, string $code): bool;
  public function resetPassword(string $email): bool; 
  public function resetPasswordWithToken(string $token, string $newPassword): bool;
  public function getAccountById(int $id): ?Account;
  public function getAccountByEmail(string $email): ?Account;
  public function getAccountByLogin(string $login): ?Account;
  public function updateAccount(Account $account): ?Account;
  public function deleteAccount(int $id): void; 
}