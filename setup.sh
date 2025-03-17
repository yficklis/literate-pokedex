#!/bin/bash

# Cores para saída no terminal
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}Iniciando a configuração do projeto Literate Pokédex...${NC}"

# Verificar se o Docker está instalado
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Docker não encontrado. Por favor, instale o Docker antes de continuar.${NC}"
    exit 1
fi

# Verificar se o Docker Compose está instalado
if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}Docker Compose não encontrado. Por favor, instale o Docker Compose antes de continuar.${NC}"
    exit 1
fi

# Criar arquivo .env se não existir
if [ ! -f .env ]; then
    echo -e "${YELLOW}Criando arquivo .env...${NC}"
    cp .env.example .env
    echo -e "${GREEN}Arquivo .env criado com sucesso!${NC}"
else
    echo -e "${YELLOW}Arquivo .env já existe. Pulando...${NC}"
fi

# Instalar dependências do Composer
echo -e "${YELLOW}Instalando dependências do Composer...${NC}"
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
echo -e "${GREEN}Dependências do Composer instaladas com sucesso!${NC}"

# Iniciar os contêineres do Laravel Sail
echo -e "${YELLOW}Iniciando os contêineres do Laravel Sail...${NC}"
./vendor/bin/sail up -d
echo -e "${GREEN}Contêineres do Laravel Sail iniciados com sucesso!${NC}"

# Gerar chave da aplicação
echo -e "${YELLOW}Gerando chave da aplicação...${NC}"
./vendor/bin/sail artisan key:generate
echo -e "${GREEN}Chave da aplicação gerada com sucesso!${NC}"

# Executar migrações
echo -e "${YELLOW}Executando migrações...${NC}"
./vendor/bin/sail artisan migrate
echo -e "${GREEN}Migrações executadas com sucesso!${NC}"

# Instalar dependências do NPM
echo -e "${YELLOW}Instalando dependências do NPM...${NC}"
./vendor/bin/sail npm install
echo -e "${GREEN}Dependências do NPM instaladas com sucesso!${NC}"

# Compilar assets
echo -e "${YELLOW}Compilando assets...${NC}"
./vendor/bin/sail npm run dev &
echo -e "${GREEN}Compilação de assets iniciada em segundo plano!${NC}"

# Importar dados dos Pokémons
echo -e "${YELLOW}Importando dados dos Pokémons...${NC}"
./vendor/bin/sail artisan pokemon:fetch --limit=100
echo -e "${GREEN}Dados dos Pokémons importados com sucesso!${NC}"

# Configurar alias do Sail
echo -e "${YELLOW}Deseja configurar o alias 'sail' para facilitar o uso do Laravel Sail? (s/n)${NC}"
read -r configure_alias

if [ "$configure_alias" = "s" ] || [ "$configure_alias" = "S" ]; then
    shell_file=""
    
    if [ -f "$HOME/.bashrc" ]; then
        shell_file="$HOME/.bashrc"
    elif [ -f "$HOME/.zshrc" ]; then
        shell_file="$HOME/.zshrc"
    fi
    
    if [ -n "$shell_file" ]; then
        echo -e "${YELLOW}Configurando alias 'sail' no arquivo $shell_file...${NC}"
        echo "alias sail='sh \$([ -f sail ] && echo sail || echo vendor/bin/sail)'" >> "$shell_file"
        echo -e "${GREEN}Alias 'sail' configurado com sucesso! Execute 'source $shell_file' para ativar o alias.${NC}"
    else
        echo -e "${RED}Não foi possível encontrar o arquivo de configuração do shell. Configure o alias manualmente.${NC}"
    fi
else
    echo -e "${YELLOW}Você pode configurar o alias manualmente adicionando a seguinte linha ao seu arquivo de perfil do shell:${NC}"
    echo "alias sail='sh \$([ -f sail ] && echo sail || echo vendor/bin/sail)'"
fi

echo -e "${GREEN}Configuração concluída com sucesso!${NC}"
echo -e "${GREEN}Acesse a aplicação em: http://localhost${NC}"
echo -e "${YELLOW}Para parar os contêineres, execute: ./vendor/bin/sail down${NC}" 