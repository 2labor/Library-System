<?php
/*
 * Inputs:
 * - Methods for creating, verifying, retrieving, and deleting account tokens
 *
 * Outputs:
 * - AccountToken entities created, verified, retrieved, or deleted from the database
 *
 * File: app/services/impl/AccountTokenServicesImpl.php
 */
namespace App\Services\Impl;

use App\Repository\AccountTokenRepository;
use App\Repository\AccountRepository;
use App\Services\AccountTokenServices;
use App\Domain\Entity\AccountToken;
use DateTime;
use Exception;

class AccountTokenServicesImpl implements AccountTokenServices {
    private AccountTokenRepository $repo;
    private AccountRepository $accountRepo;

    public function __construct(AccountTokenRepository $repo, AccountRepository $accountRepo) {
        $this->repo = $repo;
        $this->accountRepo = $accountRepo;
    }
    
    /**
     * Creates a new account token with the specified type and expiration time.
     *
     * @param int $accountId The ID of the account for which the token is being created
     * @param string $type The type of token to create. Supported types:
     *                     - 'verify_email': Generates a 6-digit numeric code for email verification
     *                     - 'reset_password': Generates a 32-character hexadecimal token for password reset
     * @param DateTime $expiresAt The date and time when the token should expire
     *
     * @return AccountToken The created and persisted account token object
     *
     * @throws Exception If the token type is not recognized or supported
     */
    public function createToken(int $accountId, string $type, DateTime $expiresAt): AccountToken {
        if ($type === 'verify_email') {
            $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = new AccountToken($accountId, $code, null, $type, $expiresAt);
        } elseif ($type === 'reset_password') {
            $tokenStr = bin2hex(random_bytes(16));
            $token = new AccountToken($accountId, null, $tokenStr, $type, $expiresAt);
        } else {
            throw new Exception("Unknown token type");
        }
        return $this->repo->save($token);
    }

    /**
     * Verifies an email address by validating a verification code token.
     *
     * @param int $accountId The ID of the account to verify
     * @param string $code The verification code token
     * @return bool Returns true if email verification was successful
     * @throws Exception If the token is not found
     * @throws Exception If the token has expired
     * @throws Exception If the account is not found
     */
    public function verifyEmailCode(int $accountId, string $code): bool {
        $accountToken = $this->repo->findByCode($code, $accountId, 'verify_email');
        if (!$accountToken) throw new Exception("Token not found");
        if ($accountToken->getExpiresAt() < new DateTime()) throw new Exception("Token expired");

        $account = $this->accountRepo->findAccountById($accountToken->getAccountId());
        if (!$account) throw new Exception("Account not found");

        $account->setVerified(true);
        $this->accountRepo->update($account);
        $this->repo->delete($accountToken->getId());

        return true;
    }

    /**
     * Verifies the validity of a token and deletes it upon successful verification.
     *
     * @param string $token The token string to verify
     * @param string $type The type/category of the token
     * @return bool Returns true if the token is valid and successfully deleted
     * @throws Exception If token is not found in the repository
     * @throws Exception If token has expired based on its expiration datetime
     */
    public function verifyToken(string $token, string $type): bool {
        $accountToken = $this->repo->findByToken($token, $type);
        if (!$accountToken) throw new Exception("Token not found");
        if ($accountToken->getExpiresAt() < new DateTime()) throw new Exception("Token expired");

        $this->repo->delete($accountToken->getId());
        return true;
    }

    /**
     * Finds an account token by its token string and type.
     *
     * @param string $token The token string to search for
     * @param string $type The type of token to search for
     * @return AccountToken|null The AccountToken object if found, null otherwise
     */
    public function findToken(string $token, string $type): ?AccountToken {
        return $this->repo->findByToken($token, $type);
    }

    /**
     * Retrieves tokens for a specific account by account ID and token type.
     *
     * @param int $accountId The ID of the account to retrieve tokens for
     * @param string $type The type of token to retrieve
     * @return array An array containing the token if found, or an empty array if not found
     */
    public function getTokensByAccountIdAndType(int $accountId, string $type): array {
        $token = $this->repo->findByAccountIdAndType($accountId, $type);
        return $token ? [$token] : [];
    }


    /**
     * Deletes a token by its ID.
     *
     * @param int $id The ID of the token to delete
     * @return bool True if the token was successfully deleted, false otherwise
     */
    public function deleteToken(int $id): bool {
        return $this->repo->delete($id);
    }
}