<?php
/*
 * Inputs:
 * - Constructor parameters for user attributes
 *
 * Outputs:
 * - Getter and setter methods for individual user attributes
 *
 * File: app/domain/entity/User.php
 */
namespace App\Domain\Entity;

use DateTime;

class User {
  private ?int $id = null;
  private string $name;
  private string $surname;
  private string $addressLine1;
  private string $addressLine2;
  private ?string $addressLine3 = null;
  private string $city;
  private Account $account;
  private DateTime $createdAt;
  private DateTime $updatedAt;

  public function __construct(
    ?int $id,
    string $name,
    string $surname,
    string $addressLine1,
    string $addressLine2,
    ?string $addressLine3,
    string $city,
    Account $account
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->surname = $surname;
    $this->addressLine1 = $addressLine1;
    $this->addressLine2 = $addressLine2;
    $this->addressLine3 = $addressLine3;
    $this->city = $city;
    $this->account = $account;
    $this->createdAt = new DateTime();
    $this->updatedAt = new DateTime();
  } 

  public function getId(): ?int {
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

  public function getAccount(): Account {
    return $this->account;
  }

  public function getCreatedAt(): DateTime {
   return $this->createdAt; 
  }

  public function getUpdatedAt(): DateTime {
   return $this->updatedAt; 
  }

  public function setId(int $id): void {
    $this->id = $id;
  }

  public function setName(string $name): void {
    $this->name = $name;
    $this->touch();
  }

  public function setSurname(string $surname): void {
    $this->surname = $surname;
    $this->touch();
  }

  public function setAddressLine1(string $addressLine1): void {
    $this->addressLine1 = $addressLine1;
    $this->touch();
  }

  public function setAddressLine2(string $addressLine2): void {
    $this->addressLine2 = $addressLine2;
    $this->touch();
  }

  public function setAddressLine3(?string $addressLine3): void {
    $this->addressLine3 = $addressLine3;
    $this->touch();
  }

  public function setCity(string $city): void {
    $this->city = $city;
    $this->touch();
  }

  public function setAccount(Account $account): void {
    $this->account = $account;
    $this->touch();
  }
  
    public function setCreatedAt(DateTime $date): void {
    $this->createdAt = $date;
  }

  public function setUpdatedAt(DateTime $date): void {
    $this->updatedAt = $date;
  }

  private function touch(): void {
      $this->updatedAt = new DateTime();
  }
}