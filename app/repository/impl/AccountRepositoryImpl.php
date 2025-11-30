<?php
namespace App\Repository\Impl;

use PDO; 
use DateTime;
use App\Repository\AccountRepository; 
use App\Domain\Entity\Account;         
use App\Domain\Mapper\AccountMapper;  

class AccountRepositoryImpl implements AccountRepository {

  private PDO $pdo;
  private AccountMapper $mapper;

  public function __construct(PDO $pdo, AccountMapper $mapper) {
    $this->pdo=$pdo;
    $this->mapper=$mapper;
  }

  public function create(Account $account): Account {
    $sql = "INSERT INTO accounts (login, password_hash, email, telephone_number, mobile_number, is_verified, created_at, updated_at)
    VALUES(:login, :password_hash, :email, :telephone_number, :mobile_number, :is_verified, :created_at, :updated_at)";

    $stmt = $this->pdo->prepare($sql);

    $now = (new DateTime())->format('Y-m-d H:i:s');

    $data = $this->mapper->toRow($account, false);
    $data['created_at'] = $now;
    $data['updated_at'] = $now;
    $data['is_verified'] = $account->isVerified() ? 1 : 0;

    $stmt->execute($data);

    $account->setId((int)$this->pdo->lastInsertId());
    $account->setCreatedAt(new DateTime($now));
    $account->setUpdatedAt(new DateTime($now));

    return $account;
  }

  public function update(Account $account): Account {
    $sql = "UPDATE accounts
      SET
        login = :login,
        password_hash = :password_hash,
        email = :email,
        telephone_number = :telephone_number,
        mobile_number = :mobile_number,
        is_verified = :is_verified,
        updated_at = :updated_at
    WHERE id = :id";

    $stmt = $this->pdo->prepare($sql);
    
    $params = $this->mapper->toRow($account, true);
    $params['id'] = $account->getId();
    $params['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
    $params['is_verified'] = $account->isVerified() ? 1 : 0;

    $stmt->execute($params);

    $account->setUpdatedAt(new DateTime());

    return $account;
  }
  
  public function delete(int $id): bool {
    $sql = "DELETE FROM accounts WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0;
  }

  public function findAccountById(int $id): ?Account {
    $sql = "SELECT * FROM accounts WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$row) return null;

    $account = $this->mapper->fromRow($row);
    $account->setId((int)$row['id']);
    return $account;
  }

  public function findAccountByLogin(string $login): ?Account {
    $sql = "SELECT * FROM accounts WHERE login = :login";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':login' => $login]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return null;

    return $this->mapper->fromRow($row);
  }

  public function findAccountByEmail(string $email): ?Account {
    $sql = "SELECT * FROM accounts WHERE email = :email";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':email' => $email]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return null;

    return $this->mapper->fromRow($row);
  }
}