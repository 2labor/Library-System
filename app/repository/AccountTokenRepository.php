<?php
namespace App\Repository;

use App\Domain\Entity\AccountToken;

interface AccountTokenRepository {
    public function save(AccountToken $token): AccountToken;
    public function findByToken(string $token, string $type): ?AccountToken; 
    public function findByCode(string $code, int $accountId, string $type): ?AccountToken; 
     public function findByAccountIdAndType(int $accountId, string $type): ?AccountToken;
    public function delete(int $id): bool;
    public function deleteExpired(): int;
}