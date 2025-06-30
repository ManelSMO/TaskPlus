<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;

#[Route('/task')]
#[IsGranted('ROLE_USER')]
class TaskController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
        $this->logger = $logger;
    }

    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Você precisa estar logado para ver as tarefas.');
        }

        $tasks = $this->taskRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Você precisa estar logado para criar tarefas.');
        }

        $task = new Task();
        $task->setUser($user);
        // createdAt é definido no construtor da entidade Task

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($task);
                $this->entityManager->flush();

                $this->addFlash('success', 'Tarefa criada com sucesso!');
                $this->logger->info('Tarefa criada com sucesso por ' . $user->getEmail() . ': ' . $task->getTitle());
                return $this->redirectToRoute('app_task_index');

            } catch (\Exception $e) {
                $this->logger->error('Erro ao criar tarefa: ' . $e->getMessage(), ['exception' => $e, 'user_id' => $user->getId()]);
                $this->addFlash('error', 'Erro ao criar tarefa: ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted()) {
            $this->logger->warning('Formulário de criação de tarefa inválido.', ['errors' => (string) $form->getErrors(true, false)]);
            $this->addFlash('error', 'Verifique os dados do formulário.');
        }

        return $this->render('task/new.html.twig', [
            'task_form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task): Response
    {
        $user = $this->getUser();
        // Verifica se a tarefa pertence ao usuário logado
        if (!$user instanceof \App\Entity\User || $task->getUser() !== $user) {
            throw $this->createAccessDeniedException('Você não tem permissão para editar esta tarefa.');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush(); // O objeto já está gerenciado pelo Doctrine

                $this->addFlash('success', 'Tarefa atualizada com sucesso!');
                $this->logger->info('Tarefa atualizada com sucesso por ' . $user->getEmail() . ': ' . $task->getTitle());
                return $this->redirectToRoute('app_task_index');
            } catch (\Exception $e) {
                $this->logger->error('Erro ao atualizar tarefa: ' . $e->getMessage(), ['exception' => $e, 'task_id' => $task->getId()]);
                $this->addFlash('error', 'Erro ao atualizar tarefa: ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted()) {
             $this->logger->warning('Formulário de edição de tarefa inválido.', ['errors' => (string) $form->getErrors(true, false)]);
             $this->addFlash('error', 'Verifique os dados do formulário de edição.');
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'task_form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task): Response
    {
        $user = $this->getUser();
        // Verifica se a tarefa pertence ao usuário logado
        if (!$user instanceof \App\Entity\User || $task->getUser() !== $user) {
            throw $this->createAccessDeniedException('Você não tem permissão para excluir esta tarefa.');
        }

        // Token CSRF está desativado para o login, mas é bom manter para outras ações
        // Se o CSRF estiver ativado globalmente no framework.yaml, descomente a linha abaixo
        // if ($this->isCsrfTokenValid('delete_task_' . $task->getId(), $request->request->get('_token'))) {
            try {
                $this->entityManager->remove($task);
                $this->entityManager->flush();
                $this->addFlash('success', 'Tarefa excluída com sucesso!');
                $this->logger->info('Tarefa excluída com sucesso por ' . $user->getEmail() . ': ' . $task->getTitle());
            } catch (\Exception $e) {
                $this->logger->error('Erro ao excluir tarefa: ' . $e->getMessage(), ['exception' => $e, 'task_id' => $task->getId()]);
                $this->addFlash('error', 'Erro ao excluir tarefa: ' . $e->getMessage());
            }
        // } else {
        //     $this->addFlash('error', 'Token de segurança inválido.');
        // }

        return $this->redirectToRoute('app_task_index');
    }

    #[Route('/{id}/toggle', name: 'app_task_toggle_complete', methods: ['POST'])]
    public function toggleComplete(Request $request, Task $task): Response
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User || $task->getUser() !== $user) {
            throw $this->createAccessDeniedException('Você não tem permissão para alterar esta tarefa.');
        }

        // Token CSRF está desativado para o login, mas é bom manter para outras ações
        // if ($this->isCsrfTokenValid('toggle_task_' . $task->getId(), $request->request->get('_token'))) {
            try { // Adicionar try-catch para toggle também
                $task->setIsCompleted(!$task->isIsCompleted());
                $this->entityManager->flush();
                $this->addFlash('success', 'Status da tarefa atualizado!');
                $this->logger->info('Status da tarefa "' . $task->getTitle() . '" alterado por ' . $user->getEmail());
            } catch (\Exception $e) {
                $this->logger->error('Erro ao alterar status da tarefa: ' . $e->getMessage(), ['exception' => $e, 'task_id' => $task->getId()]);
                $this->addFlash('error', 'Erro ao alterar status da tarefa: ' . $e->getMessage());
            }
        // } else {
        //     $this->addFlash('error', 'Token de segurança inválido.');
        // }

        return $this->redirectToRoute('app_task_index');
    }
}