## Установка

### 1. Клонирование репозитория
Склонируйте репозиторий и перейдите в папку проекта:

```bash
git clone <repository-url>
cd pizza
```

### 2. Настройка окружения для Laravel
Скопируйте файл `.env.example` в `.env` и настройте параметры подключения к базе данных:

```bash
cp .env.example .env
```

Откройте файл `.env` и убедитесь, что параметры базы данных соответствуют следующим значениям:

```env
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=db
DB_USERNAME=user
DB_PASSWORD=password
```

### 3. Проверка конфигурации Docker
Создайте файл docker_s/.env

```env
PROJECT_NAME=pizza
PROJECT_DIR=..
NGINX_PORT=92

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5427
DB_DATABASE=db
DB_USERNAME=user
DB_PASSWORD=password

UID=1000
GID=1000
```

**Примечание**: Порт `DB_PORT=5427` указывает на хостовый порт для проброса PostgreSQL. Внутри контейнеров PostgreSQL использует стандартный порт `5432`.

### 4. Сборка и запуск контейнеров
Соберите и запустите Docker-контейнеры:

```bash
docker compose up -d --build
```

### 5. Установка зависимостей приложения
Установите зависимости PHP через Composer внутри контейнера:

```bash
docker exec -it pizza-php-fpm composer install
```

### 6. Генерация ключа приложения
Сгенерируйте ключ для Laravel-приложения:

```bash
docker exec -it pizza-php-fpm php artisan key:generate
```

### 7. (Опционально) Генерация секрета для JWT
Если в приложении используется JWT-аутентификация, выполните:

```bash
docker exec -it pizza-php-fpm php artisan jwt:secret
```

### 8. (Опционально) Заполнение базы данных
Для заполнения базы данных тестовыми данными выполните:

```bash
docker exec -it pizza-php-fpm php artisan db:seed
```

### 9. Запуск тестов
Для выполнения тестов приложения используйте команду Artisan:

```bash
docker exec -it pizza-php-fpm php artisan test
```

## Запуск приложения
После выполнения всех шагов откройте приложение в браузере по адресу:

```
http://localhost:92
```
