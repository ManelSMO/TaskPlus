<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationAndLoginTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        parent::setUp();
        // Crie o cliente do Symfony para simular requisições
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->passwordHasher = $kernel->getContainer()->get(UserPasswordHasherInterface::class);

        // Opcional: Limpar usuários de teste antes de cada teste se necessário
        // $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
        // $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Limpa o EntityManager após cada teste para evitar vazamentos de memória
        $this->entityManager->close();
        $this->entityManager = null;
        $this->passwordHasher = null;
    }

    public function testRegistration(): void
    {
        $client = static::createClient(); // Cria um cliente HTTP para simular o navegador
        $crawler = $client->request('GET', '/register'); // Faz uma requisição GET para a página de registro

        $this->assertResponseIsSuccessful(); // Verifica se a requisição foi bem-sucedida (código 200)
        $this->assertSelectorTextContains('h1', 'REGISTRE-SE'); // Verifica se o H1 contém "REGISTRE-SE"

        $form = $crawler->selectButton('Criar Conta')->form([
            'registration_form[email]' => 'test_register_func@example.com',
            'registration_form[plainPassword]' => 'password123',
            'registration_form[agreeTerms]' => true, // Se o campo agreeTerms for obrigatório e existir
        ]);

        $client->submit($form); // Submete o formulário

        $this->assertResponseRedirects('/login'); // Verifica se foi redirecionado para a página de login
        
        // Segue o redirecionamento para verificar a mensagem flash
        $crawler = $client->followRedirect(); 
        $this->assertSelectorTextContains('.flash-success', 'Sua conta foi criada com sucesso!');

        // Verifica se o usuário foi salvo no banco de dados
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'test_register_func@example.com']);
        $this->assertNotNull($user); // O usuário não deve ser nulo
        $this->assertTrue($this->passwordHasher->isPasswordValid($user, 'password123')); // A senha deve ser válida
    }

    public function testLoginSuccessful(): void
    {
        // Garante que existe um usuário para logar
        $user = new User();
        $user->setEmail('test_login_func@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setRoles(['ROLE_USER']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'LOGIN');

        $form = $crawler->selectButton('Entrar')->form([
            'email' => 'test_login_func@example.com',
            'password' => 'password123',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/task'); // Redireciona para a dashboard de tarefas
        
        // Segue o redirecionamento
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Minhas Tarefas'); // Verifica o título da página de tarefas
    }

    public function testLoginFailure(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Entrar')->form([
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login'); // Deve redirecionar de volta para a página de login
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('.auth-alert-error', 'Credenciais inválidas.'); // Verifica a mensagem de erro
    }
}
