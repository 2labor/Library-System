<?php
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

  public function deleteAccount(int $id): void{
    $deleteAccount = $this->repo->delete($id);
    if (!$deleteAccount) {
      throw new Exception("Account deletion false");
    }
  } 
}