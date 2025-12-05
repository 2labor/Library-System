<?php
/*
 * Inputs:
 * - Account entity
 * - AccountDto
 * - Database row array
 *
 * Outputs:
 * - AccountDto
 * - Account entity
 * - Database row array
 *
 * File: app/domain/mapper/impl/AccountMapperImpl.php
 */
namespace App\Domain\Mapper\Impl;

use App\Domain\Mapper\AccountMapper;
use App\Domain\Entity\Account;
use App\Domain\Dto\AccountDto;
use DateTime;

class AccountMapperImpl implements AccountMapper {
  /**
   * Converts an Account entity to an AccountDto data transfer object.
   *
   * @param Account $account The Account entity to be converted
   * @return AccountDto The converted data transfer object containing account information
   *including id, login, email, verification status, telephone number,
   *and mobile number
   */
  public function toDto(Account $account): AccountDto {
    return new AccountDto(
      $account->getId(),
      $account->getLogin(),
      $account->getEmail(),
      $account->isVerified(),
      $account->getTelephoneNumber(),
      $account->getMobileNumber()
    );
  }

  /**
   * Converts an AccountDto to an Account entity.
   *
   * @param AccountDto $dto The data transfer object containing account information
   * @param string $plainPassword The plain text password to be set on the account
   * @return Account The newly created Account entity with password configured
   */
  public function fromDto(AccountDto $dto, string $plainPassword): Account {
    $account = new Account (
      $dto->getId() ?? null,
      $dto->getLogin(),
      '',
      $dto->getEmail(),
      $dto->getTelephoneNumber(),
      $dto->getMobileNumber(),
      $dto->isVerified()
    );

    $account->setPassword($plainPassword);

    return $account;
  }

  /**
   * Maps a database row to an Account domain object.
   *
   * Converts a flat array representation from the database into a fully initialized
   * Account entity with all required properties and timestamps.
   *
   * @param array $row The database row containing account data with keys:
   * - id: Account identifier (integer)
   * - login: User login string
   * - email: User email address
   * - telephone_number: Telephone number (integer)
   * - mobile_number: Mobile number (integer)
   * - is_verified: Account verification status (boolean)
   * - password_hash: Hashed password string
   * - created_at: Account creation timestamp (string)
   * - updated_at: Account last update timestamp (string)
   *
   * @return Account A fully initialized Account domain object
   */
  public function fromRow(array $row): Account {
    $account = new Account(
      (int)$row['id'],              
      $row['login'],                
      '',
      $row['email'],                
      (int)$row['telephone_number'],
      (int)$row['mobile_number'],
      (bool)$row['is_verified'],
    );

    $account->setPasswordHash($row['password_hash']);

    $account->setCreatedAt(new DateTime($row['created_at']));
    $account->setUpdatedAt(new DateTime($row['updated_at']));

    return $account;
  }

  /**
   * Converts an Account entity to a database row array.
   *
   * @param Account $account The account entity to convert
   * @param bool $forUpdate Whether this conversion is for an update operation (default: false)
   * @return array An associative array representing the account data ready for database insertion or update,
   * including login, password_hash, email, is_verified, telephone_number, mobile_number, and timestamp fields (created_at/updated_at)
   */
  public function toRow(Account $account, bool $forUpdate = false): array
  {
    $row = [
        'login' => $account->getLogin(),
        'password_hash' => $account->getPasswordHash(),
        'email' => $account->getEmail(),
        'is_verified' => $account->isVerified(),
        'telephone_number' => $account->getTelephoneNumber(),
        'mobile_number' => $account->getMobileNumber(),
    ];

     if ($forUpdate) {
        $row['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
    } else {
        $row['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
        $row['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
    }

    return $row;
  }   
}