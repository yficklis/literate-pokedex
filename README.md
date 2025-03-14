# Pokémon API e Frontend

Este projeto consiste em uma API backend desenvolvida em Laravel 12 e um frontend em Vue.js 3 para listar e detalhar informações sobre Pokémons. A API consome dados da [PokeAPI](https://pokeapi.co/) e os armazena em um banco de dados PostgreSQL. O frontend exibe os dados de forma paginada e permite filtrar Pokémons por tipo ou nome.

---

## **Tecnologias Utilizadas**
- **Backend**: Laravel 12, PostgreSQL, Redis.
- **Frontend**: Vue.js 3, Axios, Vuex.
- **Ferramentas**: Docker, PHPUnit, Jest/Vitest.
- **Boas Práticas**: RESTful, SOLID, DRY, Object Calisthenics.

---

## **Funcionalidades**

### **Backend**
- **Consumo da PokeAPI**:
    - Comando Artisan para buscar e salvar dados de Pokémons no banco local.
    - Conversão de altura (decímetros para centímetros) e peso (hectogramas para quilogramas).
- **Endpoints**:
    - `GET /api/pokemons`: Lista Pokémons com filtros por tipo ou nome.
    - `GET /api/pokemons/{id}`: Retorna detalhes de um Pokémon (nome, tipo, altura, peso).

### **Frontend**
- **Listagem de Pokémons**:
    - Exibe Pokémons com paginação.
    - Filtros por tipo ou nome.
- **Detalhes do Pokémon**:
    - Exibe informações detalhadas: nome, tipo, altura (em cm), peso (em kg).

---

## **Como Executar o Projeto**

### **Pré-requisitos**
- Docker e Docker Compose instalados.
- Git instalado.

### **Passos para Execução**

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/yficklis/pokemon-api-frontend.git
   cd pokemon-api-frontend
   ```

2. **Configure o ambiente com Docker**:
    - Crie um arquivo `.env` para o Laravel baseado no `.env.example`:
      ```bash
      cp backend/.env.example backend/.env
      ```
    - Crie um arquivo `.env` para o Vue.js baseado no `.env.example`:
      ```bash
      cp frontend/.env.example frontend/.env
      ```

3. **Suba os containers**:
   ```bash
   docker-compose up -d
   ```

4. **Instale as dependências do Laravel**:
   ```bash
   docker-compose exec backend composer install
   ```

5. **Execute as migrations e seeders**:
   ```bash
   docker-compose exec backend php artisan migrate --seed
   ```

6. **Instale as dependências do Vue.js**:
   ```bash
   docker-compose exec frontend npm install
   ```

7. **Inicie o servidor de desenvolvimento do Vue.js**:
   ```bash
   docker-compose exec frontend npm run dev
   ```

8. **Acesse a aplicação**:
    - Frontend: `http://localhost:8080`
    - API: `http://localhost:8000/api/pokemons`

---

## **Estrutura do Projeto**

### **Backend**
- **Comando Artisan**: `app/Console/Commands/FetchPokemonData.php`
- **Controllers**: `app/Http/Controllers/PokemonController.php`
- **Models**: `app/Models/Pokemon.php`
- **Migrations**: `database/migrations/`
- **Testes**: `tests/Feature/`

### **Frontend**
- **Componentes**:
    - `src/components/PokemonList.vue`: Listagem de Pokémons.
    - `src/components/PokemonCard.vue`: Card de cada Pokémon.
    - `src/components/PokemonDetail.vue`: Detalhes do Pokémon.
- **Views**:
    - `src/views/HomeView.vue`: Página inicial com listagem.
    - `src/views/PokemonDetailView.vue`: Página de detalhes.
- **Store** (se usar Vuex/Pinia): `src/store/`

---

## **Testes**

### **Backend**
- Execute os testes unitários com PHPUnit:
  ```bash
  docker-compose exec backend php artisan test
  ```

### **Frontend**
- Execute os testes unitários com Jest/Vitest:
  ```bash
  docker-compose exec frontend npm run test
  ```

---

## **Boas Práticas Aplicadas**
- **RESTful**: API seguindo princípios REST.
- **SOLID e DRY**: Código organizado e reutilizável.
- **Object Calisthenics**: Métodos pequenos e poucos níveis de indentação.
- **Design Patterns**: Repository Pattern e Service Layer no backend.

---

## **Possíveis Melhorias**
- Adicionar autenticação e autorização (ex: JWT).
- Implementar cache para melhorar performance.
- Adicionar mais filtros e ordenação na listagem.
- Implementar CI/CD para testes e deploy automatizados.

---

## **Contribuição**
Contribuições são bem-vindas! Siga os passos abaixo:
1. Faça um fork do projeto.
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`).
3. Commit suas mudanças (`git commit -m 'Adiciona nova feature'`).
4. Push para a branch (`git push origin feature/nova-feature`).
5. Abra um Pull Request.
---

## **Contato**
- **Nome**: Yficklis Santos
- **Email**: yficklis.santos@gmail.com
- **GitHub**: [https://github.com/yficklis](https://github.com/yficklis)

