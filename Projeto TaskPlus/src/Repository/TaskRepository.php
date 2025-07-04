<?php

namespace App\Repository; // Certifique-se que o namespace está correto

use App\Entity\Task; // Importa a sua entidade Task
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    // Você pode adicionar métodos personalizados aqui no futuro, como:
    // public function save(Task $entity, bool $flush = false): void
    // {
    //     $this->getEntityManager()->persist($entity);
    //
    //     if ($flush) {
    //         $this->getEntityManager()->flush();
    //     }
    // }

    // public function remove(Task $entity, bool $flush = false): void
    // {
    //     $this->getEntityManager()->remove($entity);
    //
    //     if ($flush) {
    //         $this->getEntityManager()->flush();
    //     }
    // }
}