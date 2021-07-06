## Порядок установки

### Фреймворк Symfony

1. в корне проекта в консоли выполнить команду `docker-compose up -d --build`
2. выполнить установку фреймворка: `docker exec -it symfony-php composer install`
3. выполнить миграцию: `docker exec -it symfony-php php bin/console doctrine:migrations:migrate`

После установки проект доступен по адресу http://localhost