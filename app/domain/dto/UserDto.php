<?php
namespace App\Domain\Dto;

class UserDto {
  private int $id;
  private string $name;
  private string $surname;
  private string $addressLine1;
  private string $addressLine2;
  private ?string $addressLine3 = null;
  private string $city;
  private AccountDto $accountDto;

  public function __construct(
    int $id,
    string $name,
    string $surname,
    string $addressLine1,
    string $addressLine2,
    ?string $addressLine3,
    string $city,
    AccountDto $accountDto
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->surname = $surname;
    $this->addressLine1 = $addressLine1;
    $this->addressLine2 = $addressLine2;
    $this->addressLine3 = $addressLine3;
    $this->city = $city;
    $this->accountDto = $accountDto;
  }

  public function getId(): int {
    return $this->id;
  }

  public function getName(): string {
    return $this->name;
  }

  public function getSurname(): string {
    return $this->surname;
  }

  public function getAddressLine1(): string {
    return $this->addressLine1;
  }

  public function getAddressLine2(): string {
    return $this->addressLine2;
  }

  public function getAddressLine3(): ?string {
    return $this->addressLine3;
  }

  public function getCity(): string {
    return $this->city;
  }

  public function getAccountDto(): AccountDto {
    return $this->accountDto;
  }
}

