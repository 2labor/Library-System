<?php
namespace App\Repository\Impl;

use PDO; 
use App\Repository\AccountTokenRepository; 
use App\Domain\Entity\AccountToken;        


class AccountTokenRepositoryImpl implements AccountTokenRepository {
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(AccountToken $token): AccountToken {
        if ($token->getId() !== null) {
            $stmt = $this->pdo->prepare(
                "UPDATE account_tokens SET code = :code, token = :token, type = :type, expires_at = :expires_at WHERE id = :id"
            );
            $stmt->execute([
                ':code' => $token->getCode(),
                ':token' => $token->getToken(),
                ':type' => $token->getType(),
                ':expires_at' => $token->getExpiresAt()->format('Y-m-d H:i:s'),
                ':id' => $token->getId()
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                "INSERT INTO account_tokens (account_id, code, token, type, created_at, expires_at) VALUES (:account_id, :code, :token, :type, :created_at, :expires_at)"
            );
            $stmt->execute([
                ':account_id' => $token->getAccountId(),
                ':code' => $token->getCode(),
                ':token' => $token->getToken(),
                ':type' => $token->getType(),
                ':created_at' => $token->getCreatedAt()->format('Y-m-d H:i:s'),
                ':expires_at' => $token->getExpiresAt()->format('Y-m-d H:i:s')
            ]);
            $token->setId((int)$this->pdo->lastInsertId());
        }
        return $token;
    }

    public function findByToken(string $token, string $type): ?AccountToken {
        $stmt = $this->pdo->prepare("SELECT * FROM account_tokens WHERE token = :token AND type = :type LIMIT 1");
        $stmt->execute([':token' => $token, ':type' => $type]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->fromRow($row) : null;
    }

    public function findByCode(string $code, int $accountId, string $type): ?AccountToken {
        $stmt = $this->pdo->prepare("SELECT * FROM account_tokens WHERE code = :code AND account_id = :account_id AND type = :type LIMIT 1");
        $stmt->execute([':code' => $code, ':account_id' => $accountId, ':type' => $type]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->fromRow($row) : null;
    }

    public function findByAccountIdAndType(int $accountId, string $type): ?AccountToken {
        $stmt = $this->pdo->prepare("SELECT * FROM account_tokens WHERE account_id = :account_id AND type = :type LIMIT 1");
        $stmt->execute([':account_id' => $accountId, ':type' => $type]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->fromRow($row) : null;
    }


    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM account_tokens WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function deleteExpired(): int {
        $stmt = $this->pdo->prepare("DELETE FROM account_tokens WHERE expires_at < NOW()");
        $stmt->execute();
        return $stmt->rowCount();
    }

    private function fromRow(array $row): AccountToken {
        $token = new AccountToken(
            (int)$row['account_id'],
            $row['code'],
            $row['token'],
            $row['type'],
            new \DateTime($row['expires_at'])
        );
        $token->setId((int)$row['id']);
        return $token;
    }
}