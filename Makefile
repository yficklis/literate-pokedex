# Makefile para Laravel Sail
# Facilita o uso de comandos comuns do Laravel Sail

# Definição do comando Sail
SAIL = ./vendor/bin/sail

# Cores para saída no terminal
GREEN = \033[0;32m
NC = \033[0m # No Color

.PHONY: help up down restart build test migrate fresh seed npm-install npm-dev npm-build composer-install composer-update shell pgsql redis logs ps

# Comando padrão ao executar 'make' sem argumentos
help:
	@echo "${GREEN}Comandos disponíveis:${NC}"
	@echo "  make up              - Inicia os contêineres do Docker"
	@echo "  make down            - Para os contêineres do Docker"
	@echo "  make restart         - Reinicia os contêineres do Docker"
	@echo "  make build           - Reconstrói os contêineres do Docker"
	@echo "  make test            - Executa os testes"
	@echo "  make migrate         - Executa as migrações"
	@echo "  make fresh           - Executa as migrações com fresh (apaga e recria as tabelas)"
	@echo "  make seed            - Executa os seeders"
	@echo "  make npm-install     - Instala as dependências do NPM"
	@echo "  make npm-dev         - Compila os assets para desenvolvimento"
	@echo "  make npm-build       - Compila os assets para produção"
	@echo "  make composer-install - Instala as dependências do Composer"
	@echo "  make composer-update  - Atualiza as dependências do Composer"
	@echo "  make shell           - Acessa o shell do contêiner"
	@echo "  make pgsql           - Acessa o PostgreSQL"
	@echo "  make redis           - Acessa o Redis"
	@echo "  make logs            - Exibe os logs dos contêineres"
	@echo "  make ps              - Lista os contêineres em execução"
	@echo "  make fetch-pokemon   - Busca e importa dados de Pokémons (padrão: 100)"

# Comandos do Docker
up:
	@echo "${GREEN}Iniciando os contêineres...${NC}"
	$(SAIL) up -d

down:
	@echo "${GREEN}Parando os contêineres...${NC}"
	$(SAIL) down

restart:
	@echo "${GREEN}Reiniciando os contêineres...${NC}"
	$(SAIL) down && $(SAIL) up -d

build:
	@echo "${GREEN}Reconstruindo os contêineres...${NC}"
	$(SAIL) build --no-cache

# Comandos do Laravel
test:
	@echo "${GREEN}Executando testes...${NC}"
	$(SAIL) test

migrate:
	@echo "${GREEN}Executando migrações...${NC}"
	$(SAIL) artisan migrate

fresh:
	@echo "${GREEN}Executando migrações com fresh...${NC}"
	$(SAIL) artisan migrate:fresh

seed:
	@echo "${GREEN}Executando seeders...${NC}"
	$(SAIL) artisan db:seed

# Comandos do NPM
npm-install:
	@echo "${GREEN}Instalando dependências do NPM...${NC}"
	$(SAIL) npm install

npm-dev:
	@echo "${GREEN}Compilando assets para desenvolvimento...${NC}"
	$(SAIL) npm run dev

npm-build:
	@echo "${GREEN}Compilando assets para produção...${NC}"
	$(SAIL) npm run build

# Comandos do Composer
composer-install:
	@echo "${GREEN}Instalando dependências do Composer...${NC}"
	$(SAIL) composer install

composer-update:
	@echo "${GREEN}Atualizando dependências do Composer...${NC}"
	$(SAIL) composer update

# Acesso aos contêineres
shell:
	@echo "${GREEN}Acessando o shell do contêiner...${NC}"
	$(SAIL) shell

pgsql:
	@echo "${GREEN}Acessando o PostgreSQL...${NC}"
	$(SAIL) pgsql

redis:
	@echo "${GREEN}Acessando o Redis...${NC}"
	$(SAIL) redis

# Comandos de monitoramento
logs:
	@echo "${GREEN}Exibindo logs dos contêineres...${NC}"
	$(SAIL) logs

ps:
	@echo "${GREEN}Listando contêineres em execução...${NC}"
	$(SAIL) ps

# Comandos específicos da aplicação
fetch-pokemon:
	@echo "${GREEN}Buscando e importando dados de Pokémons...${NC}"
	$(SAIL) artisan pokemon:fetch --limit=100 