<?php
namespace App\Domain\Mapper\Impl;

use App\Domain\Mapper\AccountMapper;
use App\Domain\Entity\Account;
use App\Domain\Dto\AccountDto;
use DateTime;

class AccountMapperImpl implements AccountMapper {
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