<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testSetTitle(): void
    {
        $task = new Task();
        $title = 'Minha Nova Tarefa';
        $task->setTitle($title);
        $this->assertEquals($title, $task->getTitle());
    }

    public function testSetIsCompleted(): void
    {
        $task = new Task();
        $this->assertFalse($task->isIsCompleted()); // Valor padrão

        $task->setIsCompleted(true);
        $this->assertTrue($task->isIsCompleted());

        $task->setIsCompleted(false);
        $this->assertFalse($task->isIsCompleted());
    }

    public function testSetDueDate(): void
    {
        $task = new Task();
        $dueDate = new \DateTimeImmutable('2025-12-31');
        $task->setDueDate($dueDate);
        $this->assertEquals($dueDate, $task->getDueDate());
    }

    public function testSetUser(): void
    {
        $task = new Task();
        $user = new User(); // Cria uma instância de User (simulada)
        $user->setEmail('test@user.com'); // Necessário para UserIdentifier
        
        $task->setUser($user);
        $this->assertEquals($user, $task->getUser());
    }

    public function testCreatedAtIsSetAutomatically(): void
    {
        $task = new Task();
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getCreatedAt());
    }
}