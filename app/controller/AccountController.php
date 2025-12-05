<?php
/**
 * Purpose: Interface defining account management HTTP endpoints.
 *
 * Responsibilities:
 * - Declare method signatures for account CRUD and auth flows
 * - Define contracts: register, verify email, reset/change password, get/delete
 * - Guide implementations (AccountControllerImpl) on expected I/O
 *
 * Inputs:
 * - Signature defines parameter types and names
 *
 * Outputs:
 * - Methods return void; implementations emit HTTP responses (JSON)
 *
 */

namespace App\Controller;

interface AccountController {
    public function register(): void;
    public function verifyEmail(): void;
    public function resetPassword(): void;
    public function resetPasswordWithToken(): void;
    public function changePassword(): void;
    public function getById(int $id): void;
    public function delete(int $id): void;
}