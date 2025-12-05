<?php
/*
 * Inputs:
 * - Constructor parameters for category attributes
 *
 * Outputs:
 * - Getter methods return individual category attributes
 *
 * File: app/domain/entity/Category.php
 */
namespace App\Domain\Entity;

class Category {
  private ?int $id = null;
  private string $name;

  public function __construct(
    string $name,
    ?int $id = null
  ) {
    $this->id = $id;
    $this->name = $name;
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function getName(): string {
    return $this->name;
  }
}