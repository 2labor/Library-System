<?php
namespace App\Domain\Entity;

use DateTime;

class AccountToken {
   private ?int $id = null;
    private int $accountId;
    private ?string $code;
    private ?string $token;
    private string $type;
    private DateTime $expiresAt;
    private DateTime $createdAt;
 
    public function __construct(
        int $accountId,
        ?string $code,
        ?string $token,
        string $type,
        DateTime $expiresAt
    ) {
        $this->accountId = $accountId;
        $this->code = $code;
        $this->token = $token;
        $this->type = $type;
        $this->expiresAt = $expiresAt;
        $this->createdAt = new DateTime();
    }

  public function getId(): ?int { 
    return $this->id; 
  }

  public function getAccountId(): int { 
    return $this->accountId; 
  }

  public function getCode(): ?string { 
    return $this->code; 
  }

  public function getToken(): ?string { 
    return $this->token; 
  }

  public function getType(): string { 
    return $this->type; 
  }

  public function getExpiresAt(): \DateTime { 
    return $this->expiresAt; 
  }

  public function getCreatedAt(): \DateTime { 
    return $this->createdAt; 
  }

  public function setId(int $id): void { 
    $this->id = $id; 
  }

  public function setCode(string $code): void { 
    $this->code = $code; 
  }

  public function setToken(string $token): void { 
    $this->token = $token; 
  }

  public function setExpiresAt(\DateTime $expiresAt): void { 
    $this->expiresAt = $expiresAt; 
  }
}