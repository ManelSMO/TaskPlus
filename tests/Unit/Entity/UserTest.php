<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSetEmail(): void
    {
        $user = new User();
        $email = 'test@example.com';
        $user->setEmail($email);
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($email, $user->getUserIdentifier()); // userIdentifier deve ser o email
    }

    public function testSetPassword(): void
    {
        $user = new User();
        $hashedPassword = '$2y$13$XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'; // Exemplo de hash (não use um real aqui)
        $user->setPassword($hashedPassword);
        $this->assertEquals($hashedPassword, $user->getPassword());
    }

    public function testSetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles()); // ROLE_USER é sempre adicionada
        $this->assertCount(2, $user->getRoles()); // Contém ROLE_ADMIN e ROLE_USER
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        $user->setPassword('some_password_hash'); // Simula uma senha definida
        $user->eraseCredentials();
        $this->assertEquals('some_password_hash', $user->getPassword()); // Erase credentials não altera a senha hasheada
    }
}