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

### Запуск только Site API
```bash
docker-compose -f docker-compose.local.yml up site-api postgres redis
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
| **Direct Go API** | http://localhost:8080/ | Прямой доступ к Go API |
| **Direct Laravel** | http://localhost:8000/ | Прямой доступ к Laravel API |

### Настройка /etc/hosts
Для работы с локальным доменом добавьте в `/etc/hosts`:
```
127.0.0.1 api.gelyaprodaet.local
```

## 🔧 Конфигурация сервисов

### PostgreSQL
- **Image**: postgres:15.2-alpine
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
- **Port**: 80
- **Config**: config/nginx/local.conf

## 🛠 Команды разработки

### Пересборка образов
```bash
# Пересборка всех образов
docker-compose -f docker-compose.local.yml build

# Пересборка конкретного сервиса
docker-compose -f docker-compose.local.yml build site-api
```

### Логи
```bash
# Все логи
docker-compose -f docker-compose.local.yml logs -f

# Логи конкретного сервиса
docker-compose -f docker-compose.local.yml logs -f site-api
```

### Выполнение команд в контейнерах
```bash
# Bash в Go API контейнере
docker-compose -f docker-compose.local.yml exec site-api sh

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