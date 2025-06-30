TasksPlus: Sistema de Controle de Tarefas e Grupos

📝 Descrição do Projeto
TasksPlus é uma aplicação web intuitiva e robusta para gerenciamento de tarefas pessoais e organização em grupos, desenvolvida utilizando o poderoso framework PHP Symfony. O projeto visa demonstrar a aplicação de boas práticas de desenvolvimento web, arquitetura MVC, ORM (Doctrine) e princípios de segurança, ao mesmo tempo em que oferece uma ferramenta prática para aumentar a produtividade. A interface do usuário foi desenhada no Figma e replicada com CSS para proporcionar uma experiência agradável e responsiva.

✨ Funcionalidades Principais
Autenticação Completa:

Registro de novos usuários com senha segura (hashing bcrypt).

Login e Logout de usuários.

Gerenciamento de Tarefas (CRUD):

Criação de tarefas com título, descrição, data de vencimento e prioridade (baixa, média, alta).

Listagem de tarefas por usuário.

Edição de detalhes de tarefas existentes.

Exclusão de tarefas.

Marcação de tarefas como concluídas/pendentes.

Gerenciamento de Grupos (CRUD):

Criação de grupos personalizados para categorizar tarefas.

Listagem de grupos.

Edição e exclusão de grupos.

Associação:

Associação de tarefas a múltiplos grupos para melhor organização.

🚀 Tecnologias Utilizadas
Este projeto foi construído com as seguintes tecnologias e ferramentas:

Backend:

PHP 🐘 (v8.2+)

Symfony Framework 🌐 (v6.x)

Doctrine ORM 📦

Composer (Gerenciador de Dependências) 🎶

Banco de Dados:

MySQL 🐬

Frontend:

HTML5  Markup 📄

CSS3 Styling ✨ (com base em um design Figma)

Twig (Motor de Templates) 🌱

Bootstrap 5 (via CDN para grid e componentes básicos) ⚛️

Ferramentas de Desenvolvimento:

Git (Controle de Versão) 🐙

GitHub (Hospedagem de Repositório) 😺

PHPUnit (Testes Unitários e Funcionais) 🧪

Figma (Design da Interface) 🎨

PlantUML (Geração de Diagramas UML) 🌿

🛠️ Instalação e Configuração
Siga os passos abaixo para colocar o projeto TasksPlus em funcionamento em sua máquina local.

Pré-requisitos
Certifique-se de ter instalado em seu ambiente:

PHP (v8.2 ou superior)

Composer

Um servidor web (Apache, Nginx, ou o servidor web embutido do Symfony CLI)

Um servidor de banco de dados MySQL (v8.0+ recomendado)

Git

Passos de Instalação
Clone o Repositório:

git clone https://github.com/ManelSMO/TasksPlus.git
cd TasksPlus/php # Entre no diretório raiz do projeto Symfony


Instale as Dependências do Composer:

composer install


Configuração do Ambiente (.env):
Copie o arquivo de exemplo do ambiente e configure suas credenciais de banco de dados.

cp .env .env.local


Edite o .env.local e ajuste a variável DATABASE_URL para sua configuração MySQL:

# .env.local
DATABASE_URL="mysql://root:root@127.0.0.1:3306/tasksplus" # Altere 'root:root' e 'tasksplus' conforme seu setup


Configuração do Banco de Dados:

Exclua o Banco de Dados Existente (se houver e para um ambiente limpo):
Você pode fazer isso via phpMyAdmin ou MySQL CLI.

DROP DATABASE IF EXISTS tasksplus;


(Ou utilize php bin/console doctrine:database:drop --force --if-exists se estiver certo de que ele funciona para você).

Crie o Banco de Dados:

php bin/console doctrine:database:create


Execute as Migrações (para criar as tabelas):

php bin/console make:migration # Isso gerará um novo arquivo de migração se houver alterações no schema
php bin/console doctrine:migrations:migrate


Quando perguntado, confirme com yes.

Limpe e Aqueça o Cache do Symfony:

php bin/console cache:clear --env=dev
php bin/console cache:warmup


Inicie o Servidor de Desenvolvimento:

symfony serve
# Ou se não tiver o Symfony CLI instalado, pode usar:
# php -S 127.0.0.1:8000 -t public


A aplicação estará disponível em http://127.0.0.1:8000.

🖥️ Como Usar
Acesse a Aplicação: Abra seu navegador e vá para http://127.0.0.1:8000.

Cadastre-se: Navegue para /register e crie sua conta.

Faça Login: Após o registro, vá para /login e acesse com suas credenciais.

Gerencie suas Tarefas:

Na dashboard (/task), você pode visualizar suas tarefas.

Clique em "Adicionar Nova Tarefa" (/task/new) para criar uma nova.

Use os botões de "Editar", "Concluir/Reabrir" e "Excluir" na lista para gerenciar suas tarefas.

Gerencie seus Grupos:

Navegue para /group para ver seus grupos.

Clique em "Adicionar Novo Grupo" (/group/new) para criar um novo.

Use os botões de "Editar" e "Excluir" para gerenciar seus grupos.

🤝 Contribuição
Contribuições são bem-vindas! Se você deseja contribuir para este projeto, por favor:

Faça um fork do repositório.

Crie uma nova branch para sua funcionalidade (git checkout -b feature/minha-nova-feature).

Faça suas alterações e adicione testes apropriados.

Commit suas mudanças (git commit -m 'feat: Minha nova feature').

Envie suas mudanças para o seu fork (git push origin feature/minha-nova-feature).

Abra um Pull Request descrevendo suas alterações.

👥 Autores
[Emanuel Previatti] 

[João Gabriel Zangalli] 

[Vinicius Cunha]