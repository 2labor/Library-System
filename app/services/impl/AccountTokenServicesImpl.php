<?php
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

    public function verifyToken(string $token, string $type): bool {
        $accountToken = $this->repo->findByToken($token, $type);
        if (!$accountToken) throw new Exception("Token not found");
        if ($accountToken->getExpiresAt() < new DateTime()) throw new Exception("Token expired");

        $this->repo->delete($accountToken->getId());
        return true;
    }

    public function findToken(string $token, string $type): ?AccountToken {
        return $this->repo->findByToken($token, $type);
    }

    public function getTokensByAccountIdAndType(int $accountId, string $type): array {
        $token = $this->repo->findByAccountIdAndType($accountId, $type);
        return $token ? [$token] : [];
    }


    public function deleteToken(int $id): bool {
        return $this->repo->delete($id);
    }
}