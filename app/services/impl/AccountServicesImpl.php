<?php
/*
 * Inputs:
 * - Methods for account registration, password reset, email verification, and account retrieval
 *
 * Outputs:
 * - Account entities created, updated, or retrieved from the database
 *
 * File: app/services/impl/AccountServicesImpl.php
 */
namespace App\Services\Impl;

use App\Services\AccountServices;
use App\Repository\AccountRepository;
use App\Repository\UserRepository;
use App\Services\AccountTokenServices;
use App\Services\EmailServices;
use App\Domain\Entity\Account;
use Exception;
use DateTime;

require_once __DIR__ . '/../AccountServices.php';
class AccountServicesImpl implements AccountServices {

  private AccountRepository $repo;
  private UserRepository $userRepo;
  private AccountTokenServices $tokenService;
  private EmailServices $emailService;

  public function __construct(AccountRepository $repo, AccountTokenServices $tokenService, EmailServices $emailService, UserRepository $userRepo) {
    $this->repo = $repo;
    $this->userRepo = $userRepo;
    $this->tokenService = $tokenService;
    $this->emailService = $emailService;
  }

  /**
   * Registers a new account after validating input data.
   *
   * @param string $login The desired login for the account.
   * @param string $email The email address for the account.
   * @param string $rowPassword The raw password for the account.
   * @param string $confirmPassword The confirmation of the raw password.
   * @param int $mobileNumber The mobile number for the account.
   * @param int $telephoneNumber The telephone number for the account.
   * @return Account The newly created Account entity.
   * @throws Exception If validation fails or account creation encounters an error.
   */
  public function registerAccount(string $login, string $email, string $rowPassword,
      string $confirmPassword, int $mobileNumber, int $telephoneNumber): Account  {
    if ($this->repo->findAccountByLogin($login)) {
      throw new Exception("Login is taken!"); 
    }

    if ($this->repo->findAccountByEmail($email)) {
      throw new Exception("Email is taken!");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new Exception("Email address not valid!");
    }

    if(strlen($rowPassword) != 6) {
      throw new Exception("Password must be 6 character long!");
    } 

    if ($rowPassword !== $confirmPassword) {
      throw new Exception("Password don't match with confirm password");
    }

    if (!preg_match('/^\d{10}$/', (string)$telephoneNumber)) {
      throw new Exception("Telephone number must be 10 digits!");
    }

    $account = new Account(
      null,
      $login,
      '',
      $email,
      $telephoneNumber,
      $mobileNumber,
      false
    );

    $account->setPassword($rowPassword);
    $this->repo->create($account);

    $expires = new DateTime('+15 minutes');
    $token = $this->tokenService->createToken($account->getId(), 'verify_email', $expires);

    $this->emailService->sendVerificationEmail(
      $account->getEmail(),
      $account->getLogin(),
      $token->getCode(),
      15
    );

    return $account;
  }  

  /**
   * Initiates the password reset process for an account.
   *
   * @param string $email The email address associated with the account.
   * @return bool True if the reset process was initiated successfully.
   * @throws Exception If the email is invalid or the account is not found.
   */
  public function resetPassword(string $email): bool {

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception("Invalid email");
    
    $account = $this->repo->findAccountByEmail($email);
    if (!$account) throw new Exception("Account not found");

    $expires = new DateTime('+1 hour');
    $token = $this->tokenService->createToken($account->getId(), 'reset_password', $expires);

    $this->emailService->sendResetPasswordEmail(
        $account->getEmail(),
        $account->getLogin(),
        $token->getToken(),
        60
    );

    return true;
  }

  /**
   * Resets the password for an account using a valid token.
   * @return bool True if the password was reset successfully.
   * @throws Exception If the token is invalid, account not found, or password validation fails.
   * @param string $token The password reset token.
   * @param string $newPassword The new password to set for the account.
   */
   public function resetPasswordWithToken(string $token, string $newPassword): bool {
    $accountToken = $this->tokenService->findToken($token, 'reset_password');
    if (!$accountToken) {
      throw new Exception("Invalid or expired token");
    }

    $account = $this->repo->findAccountById($accountToken->getAccountId());
    if (!$account) {
      throw new Exception("Account not found");
    }

    if (strlen($newPassword) != 6) {
      throw new Exception("Password must be 6 characters long");
    }

    if (password_verify($newPassword, $account->getPasswordHash())) {
      throw new Exception("New password should be different from old one!");
    }

    $account->setPassword($newPassword);
    $this->updateAccount($account);

    $this->tokenService->deleteToken($accountToken->getId());

    return true;
  }

  /**
   * Verifies the email of an account using a verification code.
   *
   * @param string $email The email address associated with the account.
   * @param string $code The verification code sent to the email.
   * @return bool True if the email was verified successfully.
   * @throws Exception If the account is not found, token is invalid, or code does not match.
   */
  public function verifyEmail(string $email, string $code): bool {
    $account = $this->getAccountByEmail($email);
    if (!$account) throw new Exception("Account not found!");

    $tokens = $this->tokenService->getTokensByAccountIdAndType($account->getId(), 'verify_email');
    if (empty($tokens)) {
        throw new Exception("Verification token not found");
    }

    $token = $tokens[0];
    if ($token->getCode() !== $code) {
        throw new Exception("Invalid verification code");
    }

    $account->setVerified(true);
    $this->updateAccount($account);
    $this->tokenService->deleteToken($token->getId());

    return true;
  }

  /**
   * Retrieves an account by its ID.
   *
   * @param int $id The ID of the account to retrieve.
   * @return Account|null The found Account entity or null if not found.
   */
  public function getAccountById(int $id): ?Account {
    return $this->repo->findAccountById($id);
  }

  public function getAccountByEmail(string $email): ?Account {
    return $this->repo->findAccountByEmail($email);
  }

  public function getAccountByLogin(string $login): ?Account {
    return $this->repo->findAccountByLogin($login);
  }

  public function updateAccount(Account $account): Account {
    $updateAccount = $this->repo->update($account);

    if ($updateAccount == null) {
      throw new Exception("Account not found");
    }

    return $updateAccount;
  }

  /**
   * Deletes an account by its ID.
   *
   * @param int $id The ID of the account to delete.
   * @throws Exception If the account deletion fails.
   */
  public function deleteAccount(int $id): void{
    $deleteAccount = $this->repo->delete($id);
    if (!$deleteAccount) {
      throw new Exception("Account deletion false");
    }
  } 
}