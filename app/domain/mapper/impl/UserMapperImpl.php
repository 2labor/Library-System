<?php
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