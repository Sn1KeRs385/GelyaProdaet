# Docker Development Environment

Полная разработческая среда с Laravel API, Go API, PostgreSQL, Redis и Nginx.

## 🏗 Архитектура

```
┌─────────────────────────────────────┐
│   Nginx :80                         │
│   api.gelyaprodaet.local           │
└─────────────────┬───────────────────┘
                  │
    ┌─────────────┴─────────────┐
    │                           │
    ▼ /site/*                   ▼ /* (все остальное)
┌──────────────────┐     ┌──────────────────┐
│  Go API          │     │  Laravel API     │
│  :8080           │     │  :8000           │
│  (внешние запросы│     │  (админка,       │
│   для сайта)     │     │   внутренняя     │
└──────────────────┘     │   логика)        │
         │               └──────────────────┘
         │                        │
         └────────┬─────────────────┘
                  │
         ┌──────────────────┐
         │  PostgreSQL      │
         │  :5432           │
         └──────────────────┘
                  │
         ┌──────────────────┐
         │  Redis           │
         │  :6379           │
         └──────────────────┘
```

## 🚀 Быстрый старт

### Запуск всех сервисов
```bash
# Из корня проекта
docker-compose -f docker-compose.local.yml up

# В фоновом режиме
docker-compose -f docker-compose.local.yml up -d
```

### Запуск только Go API
```bash
docker-compose -f docker-compose.local.yml up go-api postgres redis
```

### Остановка
```bash
docker-compose -f docker-compose.local.yml down

# С удалением volumes
docker-compose -f docker-compose.local.yml down -v
```

## 📡 Доступные endpoints

| Сервис | URL | Описание |
|--------|-----|----------|
| **Go API** | http://api.gelyaprodaet.local/site/ | Высокопроизводительное API для внешних запросов сайта |
| **Laravel API** | http://api.gelyaprodaet.local/ | Существующее API для админки и внутренней логики |
| **Health Check** | http://api.gelyaprodaet.local/site/health | Проверка состояния Go API |
| **Direct Go API** | http://localhost:8080/api/v1/ | Прямой доступ к Go API |
| **Direct Laravel** | http://localhost:8000/api/ | Прямой доступ к Laravel API |

### Настройка /etc/hosts
Для работы с локальным доменом добавьте в `/etc/hosts`:
```
127.0.0.1 api.gelyaprodaet.local
```

## 🔧 Конфигурация сервисов

### PostgreSQL
- **Image**: postgres:16-alpine
- **Port**: 5432
- **Database**: gelya_prodaet
- **User**: postgres
- **Password**: secret123

### Redis
- **Image**: redis:7-alpine
- **Port**: 6379

### Go API
- **Build**: config/site-api/Dockerfile
- **Go Version**: 1.24.3
- **Port**: 8080
- **Hot Reload**: ✅ (volume mount + go run)

### Laravel API
- **Build**: .docker/api/Dockerfile
- **Port**: 8000
- **Hot Reload**: ✅ (volume mount)

### Nginx
- **Image**: nginx:alpine
- **Port**: 80, 443
- **Config**: config/nginx/local.conf

## 🛠 Команды разработки

### Пересборка образов
```bash
# Пересборка всех образов
docker-compose -f docker-compose.local.yml build

# Пересборка конкретного сервиса
docker-compose -f docker-compose.local.yml build go-api
```

### Логи
```bash
# Все логи
docker-compose -f docker-compose.local.yml logs -f

# Логи конкретного сервиса
docker-compose -f docker-compose.local.yml logs -f go-api
```

### Выполнение команд в контейнерах
```bash
# Bash в Go API контейнере
docker-compose -f docker-compose.local.yml exec go-api sh

# Миграции Laravel
docker-compose -f docker-compose.local.yml exec laravel-api php artisan migrate

# PostgreSQL CLI
docker-compose -f docker-compose.local.yml exec postgres psql -U postgres -d gelya_prodaet
```

## 📁 Файловая структура

```
GelyaProdaet/
├── config/
│   ├── site-api/
│   │   └── Dockerfile          # Go API Docker образ
│   └── nginx/
│       └── api.conf            # Nginx конфигурация
├── www/
│   ├── site-api/              # Go API исходники (volume mount)
│   │   ├── main.go            # Точка входа
│   │   ├── config/            # Конфигурация
│   │   ├── database/          # База данных
│   │   ├── handlers/          # HTTP обработчики
│   │   ├── models/            # Модели данных
│   │   └── utils/             # Утилиты
│   └── api/                   # Laravel API исходники (volume mount)
└── docker-compose.local.yml   # Dev конфигурация
```

## 🔄 Hot Reload

### Go API
- Использует `go run main.go` для hot reload
- Исходники монтируются как volume: `./www/site-api:/app`
- При изменении файлов автоматически перезапускается
- **Простая структура**: без internal/ и cmd/ папок

### Laravel API
- Стандартный PHP-FPM setup
- Исходники монтируются как volume: `./www/api:/var/www/html`
- Изменения применяются мгновенно

## 🐛 Отладка

### Проверка состояния
```bash
# Статус всех контейнеров
docker-compose -f docker-compose.local.yml ps

# Проверка сети
docker network ls | grep gelya
```

### Проверка API
```bash
# Go API health check
curl http://api.gelyaprodaet.local/site/health

# Получить все товары с полными связями
curl http://api.gelyaprodaet.local/site/products

# Получить конкретный товар
curl http://api.gelyaprodaet.local/site/products/1

# Получить все опции
curl http://api.gelyaprodaet.local/site/options
```

### Типичные проблемы

1. **Порт занят**
   ```bash
   # Найти процесс на порту
   lsof -i :8080
   ```

2. **База данных недоступна**
   ```bash
   # Проверить PostgreSQL логи
   docker-compose -f docker-compose.local.yml logs postgres
   ```

3. **Nginx не стартует**
   ```bash
   # Проверить конфигурацию
   docker-compose -f docker-compose.local.yml exec nginx nginx -t
   ```

## 🚀 Production готовность

Для production используйте:
```bash
# Оптимизированная сборка Go
docker build -f config/site-api/Dockerfile -t site-api:prod .

# Multi-stage build уменьшает размер образа до ~20MB
```

### Переменные окружения для production
```env
DB_HOST=production-db-host
DB_PASSWORD=secure-password
SERVER_PORT=8080
DB_SSLMODE=require
```

## 📊 Мониторинг

### Метрики контейнеров
```bash
# Использование ресурсов
docker stats

# Только Go API
docker stats gelya_go_api_local
```

### Логи в реальном времени
```bash
# Все сервисы
docker-compose -f docker-compose.local.yml logs -f --tail=100

# Фильтр по уровню
docker-compose -f docker-compose.local.yml logs -f go-api | grep ERROR
```

Эта конфигурация обеспечивает полноценную среду разработки с hot reload, централизованным логированием и простым масштабированием! 