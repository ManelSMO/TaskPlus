<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User; // Certifique-se de importar a entidade User
use App\Form\GroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository; // Novo: Importar o UserRepository

#[Route('/group')]
#[IsGranted('ROLE_USER')]
class GroupController extends AbstractController
{
    private LoggerInterface $logger;
    private UserRepository $userRepository; // Novo: Declarar a propriedade

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, UserRepository $userRepository) // Novo: Injetar UserRepository
    {
        $this->entityManager = $entityManager; // Certifique-se que entityManager também está declarado se você usa private $entityManager
        $this->logger = $logger;
        $this->userRepository = $userRepository; // Novo: Atribuir UserRepository
    }

    #[Route('/new', name: 'app_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser();
            if (!$currentUser instanceof User) {
                // Isso não deve acontecer com #[IsGranted('ROLE_USER')], mas é uma salvaguarda
                $this->addFlash('error', 'Usuário não autenticado.');
                return $this->redirectToRoute('app_login');
            }

            // --- Adicionar o usuário logado ao grupo ---
            $group->addUser($currentUser);

            // --- Processar e-mails de colaboradores ---
            $collaboratorEmailsString = $form->get('collaboratorEmails')->getData();
            $collaboratorEmails = [];
            if ($collaboratorEmailsString) {
                // Divide a string por vírgula ou nova linha e remove espaços em branco
                $collaboratorEmails = array_map('trim', preg_split('/[,;\n\r]+/', $collaboratorEmailsString, -1, PREG_SPLIT_NO_EMPTY));
                $collaboratorEmails = array_unique($collaboratorEmails); // Evita e-mails duplicados
            }

            $notFoundEmails = [];
            foreach ($collaboratorEmails as $email) {
                // Evitar adicionar o próprio usuário logado novamente
                if ($email === $currentUser->getEmail()) {
                    continue;
                }

                $collaboratorUser = $this->userRepository->findOneBy(['email' => $email]);
                if ($collaboratorUser) {
                    $group->addUser($collaboratorUser);
                } else {
                    $notFoundEmails[] = $email;
                }
            }

            try {
                $this->entityManager->persist($group);
                $this->entityManager->flush();

                $successMessage = 'Grupo "' . $group->getName() . '" criado com sucesso!';
                if (!empty($notFoundEmails)) {
                    $successMessage .= ' Os seguintes e-mails não foram encontrados e não foram adicionados: ' . implode(', ', $notFoundEmails) . '.';
                    $this->addFlash('warning', $successMessage); // Use 'warning' para feedback parcial
                } else {
                    $this->addFlash('success', $successMessage);
                }
                
                $this->logger->info('Grupo criado com sucesso: ' . $group->getName() . ' por ' . $currentUser->getEmail());
                if (!empty($notFoundEmails)) {
                    $this->logger->warning('E-mails de colaboradores não encontrados para o grupo ' . $group->getName() . ': ' . implode(', ', $notFoundEmails));
                }

                return $this->redirectToRoute('app_group_index');

            } catch (\Exception $e) {
                $this->logger->error('Erro ao criar grupo: ' . $e->getMessage(), ['exception' => $e, 'user_id' => $currentUser->getId()]);
                $this->addFlash('error', 'Erro ao criar grupo: ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted()) {
            $this->logger->warning('Formulário de criação de grupo inválido.', ['errors' => (string) $form->getErrors(true, false)]);
            $this->addFlash('error', 'Verifique os dados do formulário.');
        }

        return $this->render('group/new.html.twig', [
            'group_form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'app_group_index', methods: ['GET'])]
    public function index(): Response
    {
        // Para listar apenas os grupos do usuário logado (se for o caso), ajuste a query:
        // $user = $this->getUser();
        // if (!$user instanceof User) {
        //     throw $this->createAccessDeniedException('Você precisa estar logado para ver os grupos.');
        // }
        // $groups = $this->entityManager->getRepository(Group::class)->findByUser($user); // Você precisaria criar este método no GroupRepository

        // Por enquanto, lista todos os grupos que o usuário logado participa
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw $this->createAccessDeniedException('Você precisa estar logado para ver os grupos.');
        }
        $groups = $currentUser->getUserGroups(); // Pega os grupos aos quais o usuário logado está associado

        return $this->render('group/index.html.twig', [
            'groups' => $groups,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Group $group): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User || !$group->getUsers()->contains($currentUser)) {
            throw $this->createAccessDeniedException('Você não tem permissão para editar este grupo.');
        }

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // --- Processar e-mails de colaboradores na edição ---
            $collaboratorEmailsString = $form->get('collaboratorEmails')->getData();
            $collaboratorEmails = [];
            if ($collaboratorEmailsString) {
                $collaboratorEmails = array_map('trim', preg_split('/[,;\n\r]+/', $collaboratorEmailsString, -1, PREG_SPLIT_NO_EMPTY));
                $collaboratorEmails = array_unique($collaboratorEmails);
            }

            $notFoundEmails = [];
            $currentGroupUsers = $group->getUsers()->map(fn($user) => $user->getEmail())->toArray();
            $usersToAdd = []; // Armazenar usuários a serem adicionados

            foreach ($collaboratorEmails as $email) {
                // Se o e-mail já estiver no grupo, pule
                if (in_array($email, $currentGroupUsers)) {
                    continue;
                }
                // Se for o próprio criador (já adicionado), pule
                if ($email === $currentUser->getEmail()) {
                    continue;
                }

                $collaboratorUser = $this->userRepository->findOneBy(['email' => $email]);
                if ($collaboratorUser) {
                    $usersToAdd[] = $collaboratorUser; // Adiciona para processamento posterior
                } else {
                    $notFoundEmails[] = $email;
                }
            }

            // Remover usuários que não estão mais na lista (opcional, dependendo da UX desejada)
            // Para simplificar, estamos apenas adicionando novos. Para remover, seria mais complexo.
            // Para remover, você percorreria os usuários atuais do grupo e removeria aqueles
            // que não estão na lista `collaboratorEmails` ATUALIZADA (e não são o criador).
            // No entanto, isso pode gerar problemas se alguém removeu acidentalmente um email.
            // A abordagem mais simples e segura é apenas ADICIONAR novos.

            try {
                // Adicionar os novos usuários encontrados ao grupo
                foreach ($usersToAdd as $userToAdd) {
                    $group->addUser($userToAdd);
                }

                $this->entityManager->flush();

                $successMessage = 'Grupo "' . $group->getName() . '" atualizado com sucesso!';
                if (!empty($notFoundEmails)) {
                    $successMessage .= ' Os seguintes e-mails não foram encontrados e não foram adicionados: ' . implode(', ', $notFoundEmails) . '.';
                    $this->addFlash('warning', $successMessage);
                } else {
                    $this->addFlash('success', $successMessage);
                }
                
                $this->logger->info('Grupo atualizado com sucesso: ' . $group->getName() . ' por ' . $currentUser->getEmail());
                if (!empty($notFoundEmails)) {
                    $this->logger->warning('E-mails de colaboradores não encontrados ao atualizar o grupo ' . $group->getName() . ': ' . implode(', ', $notFoundEmails));
                }

                return $this->redirectToRoute('app_group_index');
            } catch (\Exception $e) {
                $this->logger->error('Erro ao atualizar grupo: ' . $e->getMessage(), ['exception' => $e, 'group_id' => $group->getId()]);
                $this->addFlash('error', 'Erro ao atualizar grupo: ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted()) {
             $this->logger->warning('Formulário de edição de grupo inválido.', ['errors' => (string) $form->getErrors(true, false)]);
             $this->addFlash('error', 'Verifique os dados do formulário de edição.');
        }

        // --- Preencher o campo collaboratorEmails com os e-mails dos usuários atuais do grupo ---
        // Isso é crucial para que o campo não venha vazio na edição
        $currentCollaboratorEmails = $group->getUsers()->map(fn($user) => $user->getEmail())->toArray();
        // Filtrar o e-mail do próprio usuário logado se ele for sempre adicionado automaticamente
        $currentCollaboratorEmails = array_filter($currentCollaboratorEmails, fn($email) => $email !== $currentUser->getEmail());
        $form->get('collaboratorEmails')->setData(implode(', ', $currentCollaboratorEmails));
        // -----------------------------------------------------------------------------------------


        return $this->render('group/edit.html.twig', [
            'group' => $group,
            'group_form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_group_delete', methods: ['POST'])]
    public function delete(Request $request, Group $group): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User || !$group->getUsers()->contains($currentUser)) {
            throw $this->createAccessDeniedException('Você não tem permissão para excluir este grupo.');
        }

        try {
            $this->entityManager->remove($group);
            $this->entityManager->flush();
            $this->addFlash('success', 'Grupo excluído com sucesso!');
            $this->logger->info('Grupo excluído com sucesso: ' . $group->getName() . ' por ' . $currentUser->getEmail());
        } catch (\Exception $e) {
            $this->logger->error('Erro ao excluir grupo: ' . $e->getMessage(), ['exception' => $e, 'group_id' => $group->getId()]);
            $this->addFlash('error', 'Erro ao excluir grupo: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_group_index');
    }
}