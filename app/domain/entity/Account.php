<?php
namespace App\Domain\Entity;

use DateTime;

class Account {
  private ?int $id = null;
  private string $login;
  private string $passwordHash;
  private string $email;
  private int $telephoneNumber;
  private int $mobileNumber;
  private DateTime $createdAt;
  private DateTime $updatedAt;
  private bool $isVerified = false; 
  
  public function __construct(
    ?int $id,
    string $login,
    string $password,
    string $email,
    int $telephoneNumber,
    int $mobileNumber,
    bool $isVerified = false
  ) {
    $this->id = $id;
    $this->login = $login;
    $this->setPassword($password);
    $this->email = $email;
    $this->telephoneNumber = $telephoneNumber;
    $this->mobileNumber = $mobileNumber;
    $this->isVerified = $isVerified;
    $this->createdAt = new DateTime();
    $this->updatedAt = new DateTime();
  }

  public function setPassword(string $password): void {
    $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
  }

  public function verifyPassword(string $password): bool {
    return password_verify($password, $this->passwordHash);
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getLogin(): string {
    return $this->login;
  }

  public function getPasswordHash(): string {
    return $this->passwordHash;
  }

  public function getEmail(): string {
    return $this->email;
  }

  public function getTelephoneNumber(): int {
    return $this->telephoneNumber;
  }

  public function getMobileNumber(): int {
    return $this->mobileNumber;
  }

  public function getCreatedAt(): DateTime {
    return $this->createdAt;
  } 

  public function getUpdatedAt(): DateTime {
    return $this->updatedAt;
  } 

  public function isVerified(): bool {
    return $this->isVerified;
  }

  public function setVerified(bool $verified): void {
    $this->isVerified = $verified;
  }

  public function setPasswordHash(string $hash): void {
    $this->passwordHash = $hash;
  }
  
  public function setId(int $id): void {
    $this->id = $id;
  }

  public function setEmail(string $email): void {
    $this->email = $email;
  }

  public function setCreatedAt(DateTime $date): void {
    $this->createdAt = $date;
  }

  public function setUpdatedAt(DateTime $date): void {
    $this->updatedAt = $date;
  }

}