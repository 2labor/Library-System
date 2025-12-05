<?php
/*
 * Inputs:
 * - Methods to perform CRUD operations on AccountToken entities
 *
 * Outputs:
 * - AccountToken entities retrieved or modified in the database
 *
 * File: app/repository/impl/AccountTokenRepositoryImpl.php
 */
namespace App\Repository\Impl;

use PDO; 
use App\Repository\AccountTokenRepository; 
use App\Domain\Entity\AccountToken;        


class AccountTokenRepositoryImpl implements AccountTokenRepository {
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Saves an AccountToken to the database. If the token has an ID, it updates the existing record; otherwise, it creates a new one.
     *
     * @param AccountToken $token The AccountToken entity to save.
     * @return AccountToken The saved AccountToken entity with updated ID if it was newly created.
     */
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

    /**
     * Finds an AccountToken by its token string and type.
     *
     * @param string $token The token string to search for.
     * @param string $type The type of the token.
     * @return AccountToken|null The found AccountToken entity or null if not found.
     */
    public function findByToken(string $token, string $type): ?AccountToken {
        $stmt = $this->pdo->prepare("SELECT * FROM account_tokens WHERE token = :token AND type = :type LIMIT 1");
        $stmt->execute([':token' => $token, ':type' => $type]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->fromRow($row) : null;
    }

    /**
     * Finds an AccountToken by its code, account ID, and type.
     *
     * @param string $code The code string to search for.
     * @param int $accountId The ID of the associated account.
     * @param string $type The type of the token.
     * @return AccountToken|null The found AccountToken entity or null if not found.
     */
    public function findByCode(string $code, int $accountId, string $type): ?AccountToken {
        $stmt = $this->pdo->prepare("SELECT * FROM account_tokens WHERE code = :code AND account_id = :account_id AND type = :type LIMIT 1");
        $stmt->execute([':code' => $code, ':account_id' => $accountId, ':type' => $type]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->fromRow($row) : null;
    }

    /**
     * Finds an AccountToken by account ID and type.
     *
     * @param int $accountId The ID of the associated account.
     * @param string $type The type of the token.
     * @return AccountToken|null The found AccountToken entity or null if not found.
     */
    public function findByAccountIdAndType(int $accountId, string $type): ?AccountToken {
        $stmt = $this->pdo->prepare("SELECT * FROM account_tokens WHERE account_id = :account_id AND type = :type LIMIT 1");
        $stmt->execute([':account_id' => $accountId, ':type' => $type]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->fromRow($row) : null;
    }

    /**
     * Deletes a token by its ID.
     *
     * @param int $id The ID of the token to delete
     * @return bool True if the deletion was successful, false otherwise
     */
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM account_tokens WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Deletes all expired tokens from the database.
     *
     * @return int The number of tokens deleted.
     */
    public function deleteExpired(): int {
        $stmt = $this->pdo->prepare("DELETE FROM account_tokens WHERE expires_at < NOW()");
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Maps a database row to an AccountToken domain object.
     *
     * @param array $row The database row containing account token data.
     * @return AccountToken The mapped AccountToken entity.
     */
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