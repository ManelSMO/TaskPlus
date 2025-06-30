<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;

#[Route('/group')]
#[IsGranted('ROLE_USER')]
class GroupController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(private EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/new', name: 'app_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($group);
                $this->entityManager->flush();

                $this->addFlash('success', 'Grupo criado com sucesso!');
                $this->logger->info('Grupo criado com sucesso: ' . $group->getName());

                return $this->redirectToRoute('app_group_index');

            } catch (\Exception $e) {
                $this->logger->error('Erro ao criar grupo: ' . $e->getMessage(), ['exception' => $e]);
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
        $groups = $this->entityManager->getRepository(Group::class)->findAll();

        return $this->render('group/index.html.twig', [
            'groups' => $groups,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Group $group): Response
    {
        // Não há verificação de usuário para grupos ainda, mas pode ser adicionada se um grupo
        // for associado a um criador ou dono. Por enquanto, qualquer usuário logado pode editar.

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush();

                $this->addFlash('success', 'Grupo atualizado com sucesso!');
                $this->logger->info('Grupo atualizado com sucesso: ' . $group->getName());
                return $this->redirectToRoute('app_group_index');
            } catch (\Exception $e) {
                $this->logger->error('Erro ao atualizar grupo: ' . $e->getMessage(), ['exception' => $e, 'group_id' => $group->getId()]);
                $this->addFlash('error', 'Erro ao atualizar grupo: ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted()) {
             $this->logger->warning('Formulário de edição de grupo inválido.', ['errors' => (string) $form->getErrors(true, false)]);
             $this->addFlash('error', 'Verifique os dados do formulário de edição.');
        }

        return $this->render('group/edit.html.twig', [
            'group' => $group, // Passa o objeto group para o template
            'group_form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_group_delete', methods: ['POST'])]
    public function delete(Request $request, Group $group): Response
    {
        // Se o CSRF estiver ativado globalmente no framework.yaml, descomente a linha abaixo
        // if ($this->isCsrfTokenValid('delete_group_' . $group->getId(), $request->request->get('_token'))) {
            try {
                $this->entityManager->remove($group);
                $this->entityManager->flush();
                $this->addFlash('success', 'Grupo excluído com sucesso!');
                $this->logger->info('Grupo excluído com sucesso: ' . $group->getName());
            } catch (\Exception $e) {
                $this->logger->error('Erro ao excluir grupo: ' . $e->getMessage(), ['exception' => $e, 'group_id' => $group->getId()]);
                $this->addFlash('error', 'Erro ao excluir grupo: ' . $e->getMessage());
            }
        // } else {
        //     $this->addFlash('error', 'Token de segurança inválido.');
        // }

        return $this->redirectToRoute('app_group_index');
    }
}
