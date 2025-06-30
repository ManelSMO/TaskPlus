<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\AppAuthenticator;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private LoggerInterface $logger)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);

            try {
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', 'Sua conta foi criada com sucesso! Por favor, faça login com seu e-mail e senha.');
                $this->logger->info('Usuário registrado com sucesso: ' . $user->getEmail() . ' - Hash da Senha: ' . $hashedPassword); // Loga o hash!

                // --- REMOVIDO TEMPORARIAMENTE PARA DEPURAR ---
                // return $userAuthenticator->authenticateUser(
                //     $user,
                //     $authenticator,
                //     $request
                // );
                // --- Em vez de login automático, redirecione para o login ---
                return $this->redirectToRoute('app_login'); 
                // -----------------------------------------------------------

            } catch (\Exception $e) {
                $this->logger->error('Erro ao registrar usuário: ' . $e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', 'Ocorreu um erro ao criar sua conta. Por favor, tente novamente. Detalhes: ' . $e->getMessage());
            }
        } else {
            if ($form->isSubmitted()) {
                foreach ($form->getErrors(true) as $error) {
                    $this->logger->warning('Erro de validação no formulário de registro: ' . $error->getMessage());
                }
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}