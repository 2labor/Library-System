<?php
namespace App\Controller;

interface AccountController {
    public function register(): void;
    public function verifyEmail(): void;
    public function resetPassword(): void;
    public function resetPasswordWithToken(): void;
    public function changePassword(): void;
    public function getById(int $id): void;
    public function delete(int $id ): void;
}