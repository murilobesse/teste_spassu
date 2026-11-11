# 📚 Sistema de Gestão de Livros

> Sistema de gestão de livros desenvolvido como desafio técnico.

![Symfony](https://img.shields.io/badge/Symfony-7.4-000000?style=for-the-badge&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-18-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap)
![Tests](https://img.shields.io/badge/Tests-PHPUnit-3C7EBB?style=for-the-badge&logo=php)

## Sobre o Projeto

Sistema web para gerenciamento de cadastros de livros, permitindo o cadastro de **autores**, **assuntos** e **livros**, com relacionamentos ManyToMany entre as entidades. Inclui um módulo de **relatórios** com exportação em PDF e paginação.

### Funcionalidades Principais

- CRUD completo de Autores, Assuntos e Livros
- Relacionamentos ManyToMany com seleção múltipla
- Paginação com KnpPaginatorBundle
- Feedback visual com SweetAlert2 (toasts e confirmações)
- Validação em frontend (HTML5) e backend (Symfony Validator)
- DataTransformer para formatação monetária brasileira (R$)
- Relatório de livros por autor via View SQL + DBAL
- Exportação do relatório em PDF (mPDF)
- Fixtures com Faker para popular dados de teste
- Testes automatizados (PHPUnit)

---

## Pré-requisitos

- **Docker** e **Docker Compose** instalados
- **Git** instalado


## Como Rodar o Projeto

**1. Clonar o repositório**
```bash
git clone https://github.com/murilobesse/teste_spassu.git
cd teste_spassu
```

**2. Configurar o ambiente**
```bash
cp .env.example .env
cp .env.example .env.local
cp .env.example .env.test
```
Certifique-se de que a `DATABASE_URL` nos arquivos `.env.*` apontam para o container:
```env
DATABASE_URL="postgresql://app:!MinhaSenha123@127.0.0.1:5432/app?serverVersion=18&charset=utf8"
```


## Opção 1: Ambiente Híbrido (Usando o Docker apenas para isolar o banco de dados)

**1. Instalar dependências**
```bash
composer install
npm install
npm run dev # Ou 'npm run watch' para desenvolvimento contínuo
```

**2. Subir apenas o banco de dados via Docker**
```bash
docker compose up -d
```

**3. Criar o banco e aplicar migrations**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

**4. Popular com dados de teste**
```bash
php bin/console doctrine:fixtures:load
```

**5. Rodar o servidor local**
```bash
php -S 127.0.0.1:8000 -t public
```

Acesse com email: **admin@teste.com**
senha: **123456**

**6. Testes Automatizados**
```bash
# 1. Criar o banco de teste (apenas na primeira vez)
docker compose exec database psql -U app -c "CREATE DATABASE app_test;"

# 2. Aplicar migrations no ambiente de teste
php bin/console doctrine:migrations:migrate --env=test

# 3. Rodar os testes
vendor/bin/phpunit
```

---

## Opção 2: Ambiente 100% Dockerizado

**1. Subir todos os containers (usando o arquivo completo)**

```bash
docker compose -f compose.full.yaml up -d
```

Pronto! O `entrypoint.sh` do container PHP vai automaticamente:
- Instalar dependências PHP (`composer install`)
- Instalar dependências JavaScript (`npm install`)
- Criar o banco de dados
- Aplicar todas as migrations
- Popular com dados de teste (fixtures)
- Compilar os assets (`npm run dev`)


**2. Verifique os logs**

```base
docker compose -f compose.full.yaml logs -f php
```

**Aguarde a mensagem final:**
```bash
Projeto pronto!
Acesse: http://localhost:8080
```


**3. Acessar o Sistema**

Abra no navegador: **http://localhost:8080**

Acesse com email: **admin@teste.com**
senha: **123456**

**3. Testes Automatizados**

```bash
# 1. Criar o banco de teste (apenas na primeira vez)
docker compose -f compose.full.yaml exec database psql -U app -c "CREATE DATABASE app_test;"

# 2. Aplicar migrations no ambiente de teste
docker compose -f compose.full.yaml exec php bin/console doctrine:migrations:migrate --env=test

# 3. Rodar os testes (dentro do container PHP)
docker compose -f compose.full.yaml exec php vendor/bin/phpunit
```

---

## Qualidade de Código

```bash
composer test       # testes (PHPUnit)
composer analyse    # análise estática (PHPStan, nível 6)
composer cs-check   # verifica o padrão de código, sem alterar arquivos
composer cs-fix     # aplica as correções do PHP-CS-Fixer
composer lint       # roda tudo: análise + padrão + testes
```

Em Docker, prefixe com `docker compose -f compose.full.yaml exec php`.

---

## 📝 Comandos Úteis

```bash
# Acessar o container PHP
docker compose exec php bash

# Ver logs do container
docker compose logs -f php

# Rodar testes
docker compose exec php vendor/bin/phpunit
#ou
php vendor/bin/phpunit

# Limpar cache
docker compose exec php bin/console cache:clear
#ou
php bin/console cache:clear
```

---
