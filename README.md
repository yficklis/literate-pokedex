# Literate Pokédex

Este projeto consiste em uma aplicação web desenvolvida com Laravel 12, Inertia.js e Vue.js 3 para listar e detalhar informações sobre Pokémons. A aplicação consome dados da [PokeAPI](https://pokeapi.co/) e os armazena em um banco de dados local. O frontend exibe os dados de forma paginada e permite filtrar Pokémons por tipo ou nome.

## **Tecnologias Utilizadas**
- **Backend**: Laravel 12, SQLite/PostgreSQL
- **Frontend**: Vue.js 3, Inertia.js, Tailwind CSS
- **Ferramentas**: PHPUnit, Laravel Sail
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

### **Frontend**
- **Listagem de Pokémons**:
    - Exibe Pokémons com paginação.
    - Filtros por tipo ou nome.
    - Interface responsiva com Tailwind CSS.
    - Busca inteligente que consulta a API pública quando um Pokémon não é encontrado localmente.
- **Detalhes do Pokémon**:
    - Exibe informações detalhadas: nome, tipo, altura (em cm), peso (em kg).
    - Imagem do Pokémon.
- **Página de Tipos**:
    - Exibe todos os tipos de Pokémon disponíveis.
    - Permite filtrar Pokémons por tipo com um clique.

## **Como Executar o Projeto**

### **Pré-requisitos**
- PHP 8.2 ou superior
- Composer
- Node.js e npm
- SQLite ou PostgreSQL

### **Passos para Execução**

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/yficklis/literate-pokedex.git
   cd literate-pokedex
   ```

2. **Instale as dependências do PHP**:
   ```bash
   composer install
   ```

3. **Configure o ambiente**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure o banco de dados**:
   - Para SQLite (desenvolvimento):
     ```bash
     touch database/database.sqlite
     ```
     Edite o arquivo `.env` e configure:
     ```
     DB_CONNECTION=sqlite
     ```
   - Para PostgreSQL (produção):
     Edite o arquivo `.env` e configure suas credenciais do PostgreSQL.

5. **Execute as migrações**:
   ```bash
   php artisan migrate
   ```

6. **Importe dados dos Pokémons**:
   ```bash
   php artisan pokemon:fetch --limit=100
   ```

7. **Instale as dependências do frontend**:
   ```bash
   npm install
   ```

8. **Compile os assets**:
   ```bash
   npm run dev
   ```

9. **Inicie o servidor**:
   ```bash
   php artisan serve
   ```

10. **Acesse a aplicação**:
    - Frontend: `http://localhost:8000`
    - API: `http://localhost:8000/api/pokemons`

## **Estrutura do Projeto**

### **Backend**
- **Comando Artisan**: `app/Console/Commands/FetchPokemonData.php`
- **Controllers**:
  - Web: `app/Http/Controllers/PokemonController.php`
  - API: `app/Http/Controllers/Api/PokemonController.php`
  - Tipos: `app/Http/Controllers/PokemonTypeController.php`
- **Models**: `app/Models/Pokemon.php`
- **Services**: `app/Services/PokemonApiService.php`
- **Migrations**: `database/migrations/2025_03_17_011330_create_pokemon_table.php`

### **Frontend**
- **Componentes**:
  - `resources/js/Components/Pokemon/PokemonCard.vue`: Card de cada Pokémon
  - `resources/js/Components/Pagination.vue`: Componente de paginação
- **Páginas**:
  - `resources/js/Pages/Pokemon/Index.vue`: Listagem de Pokémons
  - `resources/js/Pages/Pokemon/Show.vue`: Detalhes do Pokémon
  - `resources/js/Pages/Pokemon/Types.vue`: Tipos de Pokémon
- **Layouts**:
  - `resources/js/Layouts/MainLayout.vue`: Layout principal da aplicação

## **Testes**

### **Executando os Testes**
```bash
php artisan test
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

