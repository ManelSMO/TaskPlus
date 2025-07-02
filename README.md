
# TasksPlus: Sistema de Controle de Tarefas e Grupos ✅

## 📝 Descrição do Projeto

**TasksPlus** é uma aplicação web intuitiva e robusta para gerenciamento de tarefas pessoais e organização em grupos, desenvolvida utilizando o poderoso framework **PHP Symfony**. O projeto visa demonstrar a aplicação de boas práticas de desenvolvimento web, arquitetura **MVC**, **ORM (Doctrine)** e princípios de segurança, ao mesmo tempo em que oferece uma ferramenta prática para aumentar a produtividade.

A interface do usuário foi desenhada no **Figma** e replicada com **CSS** para proporcionar uma experiência agradável e responsiva.

🎬 [Vídeo de Apresentação no YouTube](https://youtu.be/xh2o3fVLPao)
---

## ✨ Funcionalidades Principais

### 🔐 Autenticação Completa
- Registro de novos usuários com senha segura (hashing `bcrypt`);
- Login e logout de usuários.

### 🗂️ Gerenciamento de Tarefas (CRUD)
- Criação de tarefas com título, descrição, data de vencimento e prioridade (baixa, média, alta);
- Listagem de tarefas por usuário;
- Edição e exclusão de tarefas;
- Marcação de tarefas como concluídas/pendentes.

### 👥 Gerenciamento de Grupos (CRUD)
- Criação de grupos personalizados para categorizar tarefas;
- Listagem, edição e exclusão de grupos.

### 🔗 Associação
- Associação de tarefas a múltiplos grupos para melhor organização.

---

## 🚀 Tecnologias Utilizadas

### Backend
- PHP 🐘 (v8.2+)
- Symfony Framework 🌐 (v6.x)
- Doctrine ORM 📦
- Composer 🎶

### Banco de Dados
- MySQL 🐬

### Frontend
- HTML5 📄
- CSS3 ✨ (com base em um design Figma)
- Twig 🌱
- Bootstrap 5 ⚛️

### Ferramentas de Desenvolvimento
- Git & GitHub 🐙😺
- PHPUnit 🧪
- Figma 🎨
- PlantUML 🌿

---

## 🛠️ Instalação e Configuração

### ✅ Pré-requisitos
- PHP (v8.2 ou superior)
- Composer
- Servidor web (Apache, Nginx ou Symfony CLI)
- MySQL (v8.0+ recomendado)
- Git

### ⚙️ Passos de Instalação

1. **Clone o Repositório:**
   ```bash
   git clone https://github.com/ManelSMO/TasksPlus.git
   cd TasksPlus/php
   ```

2. **Instale as Dependências:**
   ```bash
   composer install
   ```

3. **Configure o Ambiente:**
   ```bash
   cp .env .env.local
   ```
   Edite o arquivo `.env.local` com sua configuração do MySQL:
   ```
   DATABASE_URL="mysql://root:root@127.0.0.1:3306/tasksplus"
   ```

4. **Criação do Banco de Dados:**
   ```bash
   php bin/console doctrine:database:create
   ```

5. **Execução das Migrações:**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

6. **Limpeza e Aquecimento do Cache:**
   ```bash
   php bin/console cache:clear --env=dev
   php bin/console cache:warmup
   ```

7. **Inicie o Servidor de Desenvolvimento:**
   ```bash
   symfony serve
   ```
   Ou, alternativamente:
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

---

## 🖥️ Como Usar

1. **Acesse a Aplicação:**  
   Abra o navegador em [http://127.0.0.1:8000](http://127.0.0.1:8000)

2. **Cadastre-se:**  
   Vá até `/register` e crie sua conta.

3. **Faça Login:**  
   Após o registro, vá para `/login`.

4. **Gerencie suas Tarefas:**  
   - Acesse `/task` para visualizar suas tarefas.
   - Use `/task/new` para criar novas tarefas.
   - Edite, conclua ou exclua tarefas com os botões apropriados.

5. **Gerencie seus Grupos:**  
   - Acesse `/group` para visualizar seus grupos.
   - Use `/group/new` para adicionar novos grupos.

---

## 🤝 Contribuição

Contribuições são bem-vindas! Para colaborar:

1. Faça um **fork** do projeto;
2. Crie uma branch:  
   `git checkout -b feature/nova-feature`
3. Realize suas alterações e adicione testes;
4. Commit:  
   `git commit -m 'feat: nova feature'`
5. Push:  
   `git push origin feature/nova-feature`
6. Abra um **Pull Request** e descreva suas mudanças.

---

## 👥 Autores

- Emanuel Previatti  
- João Gabriel Zangalli  
- Vinicius Scholtze Cunha
