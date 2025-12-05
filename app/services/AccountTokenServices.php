<?php
/*
 * Inputs:
 * - Methods for account token management operations
 *
 * Outputs:
 * - AccountToken entity or boolean status for operations
 *
 * File: app/services/AccountTokenServices.php
 */
namespace App\Services;

use App\Domain\Entity\AccountToken;
use DateTime;

interface AccountTokenServices {
    public function createToken(int $accountId, string $type, DateTime $expiresAt): AccountToken;
    public function verifyEmailCode(int $accountId, string $code): bool;
    public function verifyToken(string $token, string $type): bool;
    public function findToken(string $token, string $type): ?AccountToken;
    public function getTokensByAccountIdAndType(int $accountId, string $type): array;
    public function deleteToken(int $id): bool;
}
 