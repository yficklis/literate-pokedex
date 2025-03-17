# Literate Pokédex

Este projeto consiste em uma aplicação web desenvolvida com Laravel 12, Inertia.js e Vue.js 3 para listar e detalhar informações sobre Pokémons. A aplicação consome dados da [PokeAPI](https://pokeapi.co/) e os armazena em um banco de dados local. O frontend exibe os dados de forma paginada e permite filtrar Pokémons por tipo ou nome.

## **Tecnologias Utilizadas**
- **Backend**: Laravel 12, PostgreSQL, Redis
- **Frontend**: Vue.js 3, Inertia.js, Tailwind CSS
- **Ferramentas**: PHPUnit, Laravel Sail (Docker)
- **Boas Práticas**: RESTful, SOLID, DRY, Repository Pattern, Service Layer

## **Funcionalidades**

### **Backend**
- **Consumo da PokeAPI**:
    - Comando Artisan `pokemon:fetch` para buscar e salvar dados de Pokémons no banco local.
    - Conversão de altura (decímetros para centímetros) e peso (hectogramas para quilogramas).
    - Busca automática na API pública quando um Pokémon não é encontrado no banco local.
- **API REST**:
    - `GET /api/pokemons`: Lista Pokémons com filtros por tipo ou nome.
    - `GET /api/pokemons/{id}`: Retorna detalhes de um Pokémon (nome, tipo, altura, peso).
    - `GET /api/pokemon-types`: Lista todos os tipos de Pokémon disponíveis.
    - `GET /api/pokemons-autocomplete`: Retorna sugestões de nomes de Pokémon para autocomplete.
    - `GET /api/pokemon-types-autocomplete`: Retorna sugestões de tipos de Pokémon para autocomplete.

### **Frontend**
- **Listagem de Pokémons**:
    - Exibe Pokémons com paginação.
    - Filtros por tipo ou nome com autocomplete.
    - Interface responsiva com Tailwind CSS.
    - Busca inteligente que consulta a API pública quando um Pokémon não é encontrado localmente.
- **Busca Avançada**:
    - Campos de busca com autocomplete para nome e tipo.
    - Sugestões em tempo real enquanto o usuário digita.
    - Busca case-insensitive, aceitando qualquer variação de maiúsculas e minúsculas.
    - Atualização automática da base de dados quando um Pokémon ou tipo não é encontrado localmente.
- **Detalhes do Pokémon**:
    - Exibe informações detalhadas: nome, tipo, altura (em cm), peso (em kg).
    - Imagem do Pokémon.
- **Página de Tipos**:
    - Exibe todos os tipos de Pokémon disponíveis.
    - Permite filtrar Pokémons por tipo com um clique.

## **Como Executar o Projeto**

### **Pré-requisitos**
- Docker e Docker Compose
- Git

### **Configuração Inicial (Primeira Vez)**

Se você está configurando o projeto pela primeira vez, siga estes passos:

#### **Método 1: Usando o Script de Instalação Automatizado**

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/yficklis/literate-pokedex.git
   cd literate-pokedex
   ```

2. **Execute o script de instalação**:
   ```bash
   ./setup.sh
   ```
   
   Este script irá:
   - Verificar se o Docker e o Docker Compose estão instalados
   - Criar o arquivo .env a partir do .env.example
   - Instalar as dependências do Composer
   - Iniciar os contêineres do Laravel Sail
   - Gerar a chave da aplicação
   - Executar as migrações
   - Instalar as dependências do NPM
   - Compilar os assets
   - Importar dados dos Pokémons
   - Oferecer a opção de configurar o alias 'sail'

3. **Acesse a aplicação**:
   - Frontend: `http://localhost`
   - API: `http://localhost/api/pokemons`

#### **Método 2: Configuração Manual**

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/yficklis/literate-pokedex.git
   cd literate-pokedex
   ```

2. **Configure o ambiente**:
   ```bash
   cp .env.example .env
   ```

3. **Instale as dependências do Composer usando Docker**:
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php82-composer:latest \
       composer install --ignore-platform-reqs
   ```

4. **Inicie o ambiente Docker com Laravel Sail**:
   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Configure o alias do Sail** (opcional, mas recomendado):
   ```bash
   alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
   ```
   
   Para tornar o alias permanente, adicione-o ao seu arquivo de perfil do shell:
   ```bash
   echo "alias sail='sh \$([ -f sail ] && echo sail || echo vendor/bin/sail)'" >> ~/.bashrc
   # ou para ZSH
   echo "alias sail='sh \$([ -f sail ] && echo sail || echo vendor/bin/sail)'" >> ~/.zshrc
   ```

6. **Gere a chave da aplicação e execute as migrações**:
   ```bash
   sail artisan key:generate
   sail artisan migrate
   ```

7. **Importe dados dos Pokémons**:
   ```bash
   sail artisan pokemon:fetch --limit=100
   ```

8. **Instale as dependências do frontend e compile os assets**:
   ```bash
   sail npm install
   sail npm run dev
   ```

9. **Acesse a aplicação**:
    - Frontend: `http://localhost`
    - API: `http://localhost/api/pokemons`

### **Uso Diário (Após Configuração Inicial)**

Depois que o ambiente estiver configurado, você pode usar estes comandos para o desenvolvimento diário:

1. **Iniciar o ambiente**:
   ```bash
   sail up -d
   ```
   ou
   ```bash
   make up
   ```

2. **Parar o ambiente**:
   ```bash
   sail down
   ```
   ou
   ```bash
   make down
   ```

3. **Executar o servidor de desenvolvimento do frontend**:
   ```bash
   sail npm run dev
   ```
   ou
   ```bash
   make npm-dev
   ```

### **Comandos úteis do Sail**

- **Iniciar os contêineres**: `sail up -d`
- **Parar os contêineres**: `sail down`
- **Executar comandos Artisan**: `sail artisan [comando]`
- **Executar comandos Composer**: `sail composer [comando]`
- **Executar comandos NPM**: `sail npm [comando]`
- **Executar testes**: `sail test`
- **Acessar o shell do contêiner**: `sail shell`
- **Acessar o MySQL/PostgreSQL**: `sail pgsql`
- **Acessar o Redis**: `sail redis`

### **Usando o Makefile**

O projeto inclui um Makefile para facilitar ainda mais o uso do Laravel Sail. Para ver todos os comandos disponíveis:

```bash
make help
```

Exemplos de comandos:
- **Iniciar os contêineres**: `make up`
- **Parar os contêineres**: `make down`
- **Executar testes**: `make test`
- **Executar migrações**: `make migrate`
- **Instalar dependências do NPM**: `make npm-install`
- **Compilar assets**: `make npm-dev`
- **Buscar dados de Pokémons**: `make fetch-pokemon`

## **Solução de Problemas Comuns**

### **Problemas com permissões**

Se você encontrar problemas de permissão ao executar comandos do Sail, tente:

```bash
# Corrigir permissões de arquivos
chmod -R 777 storage bootstrap/cache
```

### **Portas em uso**

Se as portas 80 (web) ou 5432 (PostgreSQL) já estiverem em uso no seu sistema, você pode alterá-las no arquivo `.env`:

```
APP_PORT=8000  # Altera a porta web de 80 para 8000
FORWARD_DB_PORT=54320  # Altera a porta do PostgreSQL de 5432 para 54320
```

### **Problemas com o Docker**

Se você encontrar problemas com o Docker, tente:

```bash
# Parar todos os contêineres
docker stop $(docker ps -aq)

# Remover todos os contêineres
docker rm $(docker ps -aq)

# Reconstruir os contêineres do Sail
sail build --no-cache
```

### **Problemas com o banco de dados**

Se você encontrar problemas com o banco de dados, tente:

```bash
# Recriar o banco de dados
sail artisan migrate:fresh

# Verificar a conexão com o banco de dados
sail artisan db:monitor
```

## **Estrutura do Projeto**

### **Backend**
- **Comando Artisan**: `app/Console/Commands/FetchPokemonData.php`
- **Controllers**:
  - Web: `app/Http/Controllers/PokemonController.php`
  - API: `app/Http/Controllers/Api/PokemonController.php`
  - Tipos: `app/Http/Controllers/PokemonTypeController.php`
- **Serviços**:
  - API Pokémon: `app/Services/PokemonApiService.php`
- **Recursos**:
  - Pokémon: `app/Http/Resources/PokemonResource.php`

### **Frontend**
- **Páginas**:
  - Listagem: `resources/js/Pages/Pokemon/Index.vue`
  - Detalhes: `resources/js/Pages/Pokemon/Show.vue`
  - Tipos: `resources/js/Pages/Pokemon/Types.vue`
- **Componentes**:
  - Card: `resources/js/Components/Pokemon/PokemonCard.vue`
  - Autocomplete: `resources/js/Components/Autocomplete.vue`
  - Paginação: `resources/js/Components/Pagination.vue`

## **Novas Funcionalidades (v2.0)**

### **Busca com Autocomplete**
- Campos de busca por nome e tipo agora oferecem sugestões em tempo real.
- Integração com a API pública para buscar Pokémon e tipos não encontrados localmente.
- Interface de usuário melhorada com feedback visual durante a busca.

### **Melhorias na API**
- Novos endpoints para autocomplete de nomes e tipos.
- Busca case-insensitive para melhor experiência do usuário.
- Atualização automática da base de dados com novos Pokémon e tipos.

## **Testes**

### **Executando os Testes**
```bash
sail test
```

### **Tipos de Testes**
- **Testes Unitários**:
  - `tests/Unit/Models/PokemonTest.php`: Testes para o modelo Pokemon
- **Testes de Feature**:
  - `tests/Feature/Api/PokemonControllerTest.php`: Testes para a API REST
  - `tests/Feature/ExampleTest.php`: Teste básico de redirecionamento

## **Boas Práticas Aplicadas**
- **RESTful API**: Endpoints seguindo princípios REST.
- **SOLID**: Separação de responsabilidades entre controllers, models e services.
- **DRY**: Código reutilizável e bem organizado.
- **Repository Pattern**: Abstração do acesso aos dados.
- **Service Layer**: Lógica de negócio encapsulada em serviços.
- **Testes Automatizados**: Cobertura de testes para garantir a qualidade do código.

## **Funcionalidade de Busca Inteligente**
A aplicação implementa uma busca inteligente que:
1. Primeiro procura o Pokémon no banco de dados local.
2. Se não encontrar, consulta automaticamente a API pública do Pokémon.
3. Se o Pokémon for encontrado na API, seus dados são armazenados no banco local antes de serem retornados.
4. Se não for encontrado em nenhum lugar, exibe a mensagem "Nenhum Pokémon encontrado".

Esta abordagem otimiza o desempenho, reduzindo chamadas desnecessárias à API externa e melhorando a experiência do usuário.

## **Possíveis Melhorias**
- Implementar cache para reduzir chamadas à API externa.
- Adicionar mais filtros e ordenação na listagem.
- Implementar busca em tempo real com debounce.
- Adicionar mais informações sobre os Pokémons (habilidades, estatísticas, etc.).
- Implementar favoritos e histórico de visualização.
- Adicionar modo escuro à interface.

## **Contribuição**
Contribuições são bem-vindas! Siga os passos abaixo:
1. Faça um fork do projeto.
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`).
3. Commit suas mudanças (`git commit -m 'Adiciona nova feature'`).
4. Push para a branch (`git push origin feature/nova-feature`).
5. Abra um Pull Request.

## **Contato**
- **Nome**: Yficklis Santos
- **Email**: yficklis.santos@gmail.com
- **GitHub**: [https://github.com/yficklis](https://github.com/yficklis)

