<?php
/*
 * Inputs:
 * - Methods to map between User entity and UserDto
 * - Methods to map between User entity and associative array (database row)
 *
 * Outputs:
 * - Mapped UserDto or associative array
 *
 * File: app/domain/mapper/impl/UserMapperImpl.php
 */
namespace App\Domain\Mapper\Impl;

use App\Domain\Mapper\UserMapper;
use App\Domain\Entity\User;
use App\Domain\Dto\UserDto;
use App\Domain\Entity\Account;
use App\Domain\Dto\AccountDto;
use App\Repository\AccountRepository;
use App\Domain\Mapper\AccountMapper;
use DateTime;
use Exception;

class UserMapperImpl implements UserMapper {

  private AccountMapper $accountMapper;
  private AccountRepository $accountRepo;

  public function __construct(AccountMapper $accountMapper, AccountRepository $accountRepo) {
    $this->accountMapper = $accountMapper;
    $this->accountRepo = $accountRepo;
  }

  /**
   * Maps a User domain entity to a UserDto.
   *
   * @param User $user The User entity to be mapped.
   * @return UserDto The resulting UserDto containing user details and associated account information.
   */
  public function toDto(User $user): UserDto {
    $account = $this->accountMapper->toDto($user->getAccount());

    return new UserDto(
      $user->getId(),
      $user->getName(),
      $user->getSurname(),
      $user->getAddressLine1(),
      $user->getAddressLine2(),
      $user->getAddressLine3(),
      $user->getCity(),
      $account
    );
  }

  /**
   * Maps data from UserDto and AccountDto objects, along with a plain password,
   * to create a new User domain object.
   *
   * @param UserDto $userDto The data transfer object containing user information.
   * @param AccountDto $accountDto The data transfer object containing account information.
   * @param string $plainPassword The plain text password to be used for account creation.
   * @return User The newly created User domain object.
   */
  public function fromDtoNew(UserDto $userDto, AccountDto $accountDto, string $plainPassword): User {
    $account = $this->accountMapper->fromDto($accountDto, $plainPassword);

    return new User(
      $userDto->getId() ?? null,
      $userDto->getName(),
      $userDto->getSurname(),
      $userDto->getAddressLine1(),
      $userDto->getAddressLine2(),
      $userDto->getAddressLine3(),
      $userDto->getCity(),
      $account
    );
  }

  /**
   * Maps data from a UserDto and an existing Account to a User domain object.
   *
   * @param UserDto $userDto The data transfer object containing user information.
   * @param Account $account The existing account associated with the user.
   * @return User The mapped User domain object.
   */
  public function fromDtoExisting(UserDto $userDto, Account $account): User {
    return new User(
      $userDto->getId(),
      $userDto->getName(),
      $userDto->getSurname(),
      $userDto->getAddressLine1(),
      $userDto->getAddressLine2(),
      $userDto->getAddressLine3(),
      $userDto->getCity(),
      $account
    );
  }

  /**
   * Maps a database row to a User domain object.
   *
   * Converts a flat array representation from the database into a fully initialized
   * User entity with all required properties and timestamps.
   *
   * @param array $row The database row containing user data with keys:
   * - id: User identifier (integer)
   * - name: User's first name (string)
   * - surname: User's last name (string)
   * - address_line1: First line of the user's address (string)
   * - address_line2: Second line of the user's address (string)
   * - address_line3: Third line of the user's address (string, nullable)
   * - city: User's city (string)
   * - account_id: Associated account identifier (integer)
   * - created_at: User creation timestamp (string)
   * - updated_at: User last update timestamp (string)
   *
   * @return User A fully initialized User domain object
   * @throws Exception If the associated account is not found.
   */
  public function fromRow(array $row): User {
    $account = $this->accountRepo->findAccountById((int)$row['account_id']);
    if (!$account) {
      throw new Exception("Account not found with ID {$row['account_id']}");
    }

    $user = new User(
      isset($row['id']) ? (int)$row['id'] : null,
      $row['name'],
      $row['surname'],
      $row['address_line1'],
      $row['address_line2'],
      $row['address_line3'] ?? null,
      $row['city'],
      $account
    );

    $user->setCreatedAt(new DateTime($row['created_at']));
    $user->setUpdatedAt(new DateTime($row['updated_at']));

    return $user;
  }  
  /**
   * Converts a User entity to a database row array.
   *
   * @param User $user The user entity to convert
   * @param bool $forUpdate Whether the row is for an update operation (default: false).
   *                       If true, 'created_at' and 'updated_at' fields are excluded.
   * @return array The associative array representing the user, with keys corresponding to database columns.
   */
  public function toRow(User $user, bool $forUpdate = false): array {
    $row = [
      'name'=> $user->getName(),
      'surname'=> $user->getSurname(),
      'address_line1' => $user->getAddressLine1(),
      'address_line2' => $user->getAddressLine2(),
      'address_line3' => $user->getAddressLine3() ?? null,
      'city'=> $user->getCity(),
      'account_id' => $user->getAccount()->getId(),
      ];

    if (!$forUpdate) {
      $row['created_at'] = $user->getCreatedAt()?->format('Y-m-d H:i:s');
      $row['updated_at'] = $user->getUpdatedAt()?->format('Y-m-d H:i:s');
    }

    return $row;
  }

}