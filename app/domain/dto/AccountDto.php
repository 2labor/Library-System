<?php
/**
 * Purpose: Data Transfer Object for Account entity.
 *
 * Responsibilities:
 * - Encapsulate account data for transfer between layers
 * - Provide getters for account properties
 *
 * Inputs:
 * - Constructor parameters for account attributes
 *
 * Outputs:
 * - Getter methods return individual account attributes
 *
 * File: app/domain/dto/AccountDto.php
 */
namespace App\Domain\Dto;

class AccountDto {
  private int $id;
  private string $login;
  private string $email;
  private bool $isVerified;
  private int $telephoneNumber;
  private int $mobileNumber;

  public function __construct(
    int $id,
    string $login,
    string $email,
    bool $isVerified,
    int $telephoneNumber,
    int $mobileNumber
  ) {
    $this->id = $id;
    $this->login = $login;
    $this->email = $email;
    $this->isVerified = $isVerified;
    $this->telephoneNumber = $telephoneNumber;
    $this->mobileNumber = $mobileNumber;  
  }

  public function getId(): int {
    return $this->id;
  }

  public function getLogin(): string {
    return $this->login;
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

  public function isVerified(): bool {
    return $this->isVerified;
  }
}
