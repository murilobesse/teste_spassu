# Desafio Técnico - Cadastro de Livros

Projeto desenvolvido como parte de um teste técnico para a vaga de Desenvolvedor PHP/Symfony TWIG. O sistema contempla o cadastro de Livros, Autores e Assuntos, seguindo rigorosamente o modelo de dados (UML) fornecido, com foco em boas práticas, qualidade de código e arquitetura limpa.

## 🛠️ Stack Tecnológica

- **Backend**: PHP 8.2+ | Symfony 7.4 LTS
- **Banco de Dados**: PostgreSQL 16
- **ORM**: Doctrine ORM + Doctrine Migrations
- **Frontend**: Twig | Bootstrap 5 | Webpack Encore + Sass/SCSS
- **Qualidade de Código**: PHPStan | PHP-CS-Fixer | Symfony Validator
- **Testes**: PHPUnit
- **Relatórios**: mPDF (geração de PDF a partir de views do banco)
- **Ambiente**: Docker + Docker Compose | Composer | Git | Node.js (NPM)

---

## 📋 Pré-requisitos

- Docker e Docker Compose instalados
- PHP 8.2+ e Composer instalados localmente (ou uso total via Docker)
- Node.js (v18 ou superior) e NPM instalados
- Symfony CLI (opcional, mas recomendado)

---

## 🚀 Instalação e Configuração

Siga os passos abaixo para configurar e rodar o projeto localmente:

### 1. Clonar o repositório
```bash
git clone <url-do-repositorio>
cd desafio-livros
```

### 2. Instalar dependências do PHP
```bash
composer install
```

### 3. Instalar dependências do Node.js e buildar os assets
```bash
npm install
npm run dev
```
# Ou use 'npm run watch' para desenvolvimento contínuo

### 4. Configurar o ambiente
Copie o arquivo de exemplo e ajuste as variáveis de ambiente se necessário:
```bash
cp .env
```

Certifique-se de que a DATABASE_URL no .env.local aponta para o container Docker do PostgreSQL:
```env
DATABASE_URL="postgresql://db_livros:!MinhaSenha123@127.0.0.1:5432/app?serverVersion=18&charset=utf8"
```

### 5. Subir o banco de dados via Docker
```bash
docker compose up -d
```

### 6. Criar o banco de dados e aplicar as migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 7. Rodar o servidor local
```bash
symfony server:start -d
# Ou: php -S 127.0.0.1:8000 -t public
```

Acesse a aplicação em: http://127.0.0.1:8000

---

O modelo segue integralmente o UML fornecido, com chaves primárias nomeadas conforme a especificação:
```
    autor: codAu (PK), nome
    assunto: codAs (PK), descricao
    livro: codl (PK), titulo, editora, edicao, anoPublicacao, valor (campo adicional solicitado no desafio)
    Livro_Autor: Tabela de junção N:N (Livro_Codl, Autor_CodAu)
    Livro_Assunto: Tabela de junção N:N (Livro_Codl, Assunto_CodAs)
```
Observação: As colunas de chave primária e estrangeira foram explicitamente mapeadas no Doctrine (#[ORM\Column(name: '...')]) para garantir 100% de aderência ao UML, evitando o padrão automático id.
