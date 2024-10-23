#!/bin/bash

# Color constants
Green='\033[1;32m'
Blue='\033[1;34m'
Yellow='\033[1;33m'
Purple='\033[1;35m'
NC='\033[0m' # No Color

# Navigate to the project directory
cd /www/web-scrapper


echo -e "${Blue}Create a local .env file...${NC}"
if [ -f .env ]; then
    echo -e "${Purple}File already exists. Skipped.${NC}"
else
    cp .env.dev .env
fi
echo -e "${Green}Done.${NC}\n"


echo -e "\n${Blue}Change the rights to the folders required by PHP...${NC}"
chmod -R o+w /www/web-scrapper/storage
echo -e "${Green}Done.${NC}\n"


echo -e "\n${Blue}Install composer dependencies...${NC}"
composer install
echo -e "${Green}Done.${NC}"


echo -e "\n${Blue}Create the DB structure...${NC}"
php artisan migrate
echo -e "${Green}Done.${NC}"


echo -e "\n${Yellow}Run the php-fpm service (port 9000) and Laravel queues with Horizon...${NC}"
php-fpm & php artisan queue:work & php artisan horizon
