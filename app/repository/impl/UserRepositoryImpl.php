<?php 
namespace App\Repository\Impl;

use PDO; 
use DateTime;
use App\Repository\UserRepository;
use App\Domain\Mapper\UserMapper;
use App\Domain\Entity\User;

class UserRepositoryImpl implements UserRepository {

  private UserMapper $mapper;
  private PDO $pdo;

  public function __construct(
    UserMapper $mapper,
    PDO $pdo
  ) {
    $this->mapper = $mapper;
    $this->pdo = $pdo;
  }

  public function create(User $user): User {
    $sql = "INSERT INTO users(
      name, surname, address_line1, address_line2, address_line3, city, account_id, created_at, updated_at
      ) 
      VALUES (:name, :surname, :address_line1, :address_line2, :address_line3, :city, :account_id, :created_at, :updated_at)";

    $stmt = $this->pdo->prepare($sql);

    $now = (new DateTime())->format('Y-m-d H:i:s');

    $data = $this->mapper->toRow($user, false);
    $data['created_at'] = $now;
    $data['updated_at'] = $now;

    $stmt->execute($data);

    $user->setId((int)$this->pdo->lastInsertId());
    $user->setCreatedAt(new DateTime($now));
    $user->setUpdatedAt(new DateTime($now));

    return $user;
  }

  public function update(User $user): User {
    $sql = "UPDATE users
      SET 
        name = :name, 
        surname = :surname, 
        address_line1 = :address_line1, 
        address_line2 = :address_line2, 
        address_line3 = :address_line3, 
        city = :city, 
        account_id = :account_id, 
        updated_at = :updated_at
      WHERE id = :id
      ";

    $stmt = $this->pdo->prepare($sql);

    $params = $this->mapper->toRow($user, true);
    $params['id'] = $user->getId();
    $params['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');

    $stmt->execute($params);

    $user->setUpdatedAt(new DateTime());

    return $user;
  }

  public function delete(int $id): bool {
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0;
  }

  public function findUserById(int $id): ?User {
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return null;
    
    return $this->mapper->fromRow($row); 
  }


  public function findUserByAccountId(int $accountId): ?User {
    $sql = "SELECT * FROM users WHERE account_id = :account_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':account_id' => $accountId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return null;

    return $this->mapper->fromRow($row);
  }
}