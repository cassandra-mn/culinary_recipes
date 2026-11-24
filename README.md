# Receitas Culinárias

Aplicação para cadastro e consulta de receitas culinárias.

O projeto é dividido em duas partes:

- **API:** PHP puro (sem framework), com MySQL e autenticação via JWT - fica na raiz do repositório.
- **Front-end:** Vue 3 + TypeScript + Vite - na pasta `frontend`.

Há duas formas de rodar o projeto: [com Docker](#rodando-com-docker) (sobe MySQL, API e front-end com um comando) ou [manualmente](#rodando-manualmente).

## Rodando com Docker

### Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) com o Docker Compose

### Passo a passo

1. Clone o repositório

```sh
git clone git@github.com:cassandra-mn/culinary_recipes.git
cd culinary_recipes
```

2. Crie o arquivo de ambiente

```sh
cp .env.example .env
```

3. Preencha o `.env`

```
DB_DATABASE=culinary_recipes
DB_USERNAME=root
DB_PASSWORD=root

JWT_SECRET=uma_chave_com_no_minimo_32_caracteres
JWT_TTL=3600
```

- `DB_USERNAME` e `DB_PASSWORD` precisam ser `root` e `root`, que são as credenciais do MySQL criado pelo `docker-compose.yaml`.
- `DB_HOST` e `DB_PORT` podem ficar como estão: dentro do Docker, o `DB_HOST` é sobrescrito para o container `mysql`.
- `JWT_SECRET` precisa ter pelo menos 32 caracteres. Para gerar uma:

```sh
openssl rand -hex 32
```

4. Suba os containers

```sh
docker compose up -d --build
```

Isso sobe três containers:

| Serviço | Endereço | Descrição |
| ------- | -------- | --------- |
| `frontend` | `http://localhost:5173` | Front-end (Vite) |
| `api` | `http://localhost:8000` | API em PHP + Apache |
| `mysql` | `localhost:3306` | MySQL 8.4 (usuário `root`, senha `root`) |

O banco, as tabelas e as categorias iniciais são criados automaticamente quando o container da API inicia, então não é preciso rodar o seed.

Na primeira vez, o container do front-end instala as dependências (`npm install`) antes de subir e pode demorar alguns instantes. Para acompanhar:

```sh
docker compose logs -f frontend
```

Como não existe usuário padrão, acesse `http://localhost:5173/register`, crie sua conta e depois faça login.

### Comandos úteis

```sh
docker compose logs -f api   # logs da API
docker compose down          # para e remove os containers (os dados do banco são mantidos)
docker compose down -v       # também apaga o volume com os dados do MySQL
```

## Rodando manualmente

### Pré-requisitos

- PHP 8.0 ou superior
- [Composer](https://getcomposer.org/)
- MySQL 8 rodando
- Node.js `^22.18.0` ou `>=24.12.0` e npm

### Instalação

1. Clone o repositório

```sh
git clone git@github.com:cassandra-mn/culinary_recipes.git
cd culinary_recipes
```

2. Instale as dependências da API

```sh
composer install
```

3. Crie o arquivo de ambiente

```sh
cp .env.example .env
```

4. Preencha o `.env` com as credenciais do seu MySQL e com a chave do JWT

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=culinary_recipes
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

JWT_SECRET=uma_chave_com_no_minimo_32_caracteres
JWT_TTL=3600
```

- `JWT_SECRET` precisa ter pelo menos 32 caracteres. Para gerar uma:

```sh
php -r "echo bin2hex(random_bytes(32));"
```

- `JWT_TTL` é o tempo de validade do token, em segundos.

5. Crie o banco, as tabelas e as categorias iniciais

```sh
php src/Database/Seed.php
```

O script cria o banco informado em `DB_DATABASE` se ele ainda não existir, mas o usuário do `.env` precisa ter permissão de `CREATE` no MySQL.

6. Suba a API

```sh
php -S localhost:8000 -t public public/index.php
```

A API fica disponível em `http://localhost:8000`.

### Configuração do front-end

1. Em outro terminal, entre na pasta do front-end

```sh
cd frontend
```

2. Instale as dependências

```sh
npm install
```

3. Crie o arquivo `frontend/.env` apontando para a API

```
API_PROXY_TARGET=http://localhost:8000
```

   Se o arquivo não existir, o padrão já é `http://localhost:8000`. Em desenvolvimento, o Vite repassa as chamadas de `/api` para esse endereço.

4. Rode o projeto

```sh
npm run dev
```

O front-end abre em `http://localhost:5173`. Como não existe usuário padrão, crie sua conta em `/register` e depois faça login.

Para gerar a versão de produção:

```sh
npm run build
```

## Testes

Os testes de integração sobem a API (`php -S` em uma porta livre), fazem requisições HTTP reais e conferem o banco.

Precisam de PHP, das dependências de desenvolvimento (`composer install`) e de um MySQL acessível, com as mesmas variáveis `DB_HOST`, `DB_PORT`, `DB_USERNAME` e `DB_PASSWORD` do `.env`.

```sh
vendor/bin/phpunit --testsuite Integration
```

- Os testes usam um banco separado, `culinary_recipes_test`, criado automaticamente na primeira execução (tabelas e categorias do seed).
- Para usar outro nome de banco, defina `DB_TEST_DATABASE`. O nome precisa terminar em `_test`, senão os testes se recusam a limpar o banco.
- As tabelas `recipes`, `users` e `revoked_tokens` são limpas antes de cada teste.

## Rotas da API

Todas as respostas seguem o formato:

```json
{
  "success": true,
  "data": {},
  "message": "Mensagem descritiva"
}
```

As rotas marcadas com 🔒 exigem o header `Authorization: Bearer <token>`, com o token retornado no login.

| Método | Rota | Auth | Descrição |
| ------ | ---- | :--: | --------- |
| POST | `/login` | | Faz login e retorna o token e o usuário |
| POST | `/register` | | Cadastra um novo usuário |
| POST | `/logout` | 🔒 | Encerra a sessão e invalida o token |
| GET | `/categories` | 🔒 | Lista as categorias |
| GET | `/recipes` | 🔒 | Lista as receitas do usuário logado |
| POST | `/recipes` | 🔒 | Cadastra uma receita |
| GET | `/recipes/{id}` | 🔒 | Busca uma receita pelo id |
| PATCH | `/recipes/{id}` | 🔒 | Atualiza uma receita (campos parciais) |
| DELETE | `/recipes/{id}` | 🔒 | Exclui uma receita |

Cada usuário só enxerga e altera as próprias receitas.

### Exemplos de body

**POST `/register`**

```json
{
  "name": "Maria Silva",
  "login": "maria",
  "password": "senha123"
}
```

`name` é opcional. `login` e `password` são obrigatórios.

**POST `/login`**

```json
{
  "login": "maria",
  "password": "senha123"
}
```

**POST `/recipes`** e **PATCH `/recipes/{id}`**

```json
{
  "category_id": 9,
  "name": "Brigadeiro",
  "preparation_time": 20,
  "preparation_method": "Misture tudo na panela e mexa até soltar do fundo.",
  "portions": 20,
  "ingredients": "1 lata de leite condensado, 1 colher de manteiga, 4 colheres de chocolate em pó"
}
```

Somente `preparation_method` é obrigatório na criação.
