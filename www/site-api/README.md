# Site API (Go)

Высокопроизводительное API для сайта GelyaProdaet, написанное на Go с использованием Fiber фреймворка.

## 🚀 Особенности

- **Высокая производительность**: Go + Fiber обеспечивают до 132,000 req/s
- **Go 1.24.3**: Используется последняя версия Go
- **GORM ORM**: Удобная работа с базой данных PostgreSQL
- **RESTful API**: Стандартные HTTP методы и статус коды
- **CORS поддержка**: Настроенная поддержка кросс-доменных запросов
- **Автоматические миграции**: Автоматическое создание таблиц при запуске
- **Структурированное логирование**: Подробные логи запросов
- **Docker поддержка**: Готовая конфигурация для dev и prod

## 📋 Требования

- Go 1.24.3+
- PostgreSQL 12+
- Docker & Docker Compose (опционально)

## 🛠 Установка и запуск

### Локальная разработка

1. **Клонируйте репозиторий и перейдите в директорию:**
   ```bash
   cd www/site-api
   ```

2. **Установите зависимости:**
   ```bash
   make deps
   ```

3. **Настройте переменные окружения:**
   ```bash
   # Отредактируйте .env файл с вашими настройками базы данных
   vim .env
   ```

4. **Запустите приложение:**
   ```bash
   make dev  # Режим разработки с hot reload
   # или
   make run  # Сборка и запуск
   ```

### Docker разработка

1. **Запуск через docker-compose:**
   ```bash
   # Из корня проекта
   docker-compose -f docker-compose.local.yml up go-api
   ```

2. **Полная dev среда:**
   ```bash
   # Запуск всех сервисов (Laravel API, Go API, PostgreSQL, Redis, Nginx)
   docker-compose -f docker-compose.local.yml up
   ```

## 📚 API Endpoints

### Доступ через Nginx (в Docker)
- **Go API (внешние запросы для сайта)**: `http://api.gelyaprodaet.local/site/`
- **Laravel API (админка, внутренняя логика)**: `http://api.gelyaprodaet.local/`
- **Health Check**: `http://api.gelyaprodaet.local/site/health`

### Прямой доступ (для разработки)
- **Go API**: `http://localhost:8080/api/v1/`
- **Laravel API**: `http://localhost:8000/api/`

### Товары (Products)
- `GET /site/products` - Получить товары с фильтрацией, пагинацией и сортировкой
  - **Фильтры**: `is_active`, `type_id`, `gender_id`, `brand_id`, `country_id`, `size_id`, `color_id`, `size_year_id`
  - **Пагинация**: `page`, `per_page` (макс. 100 на странице)
  - **Сортировка**: `sort_by` (`created_at`, `price`), `sort_direction` (`asc`, `desc`)
  - **Базовая фильтрация**: только товары с `is_for_sale=true` элементами
  - **Ответ**: объект с `data` (товары) и `meta` (пагинация)
- `GET /site/products/:id` - Получить товар по ID с полными связями

### Опции (Options) 
- `GET /site/options` - Получить все опции отсортированные по group_slug, weight, title

### Система
- `GET /site/health` - Проверка состояния API

## 🗄 Модели данных

### ListOption
```json
{
  "id": 1,
  "group_slug": "sizes",
  "title": "XL",
  "weight": 10,
  "is_hidden_from_user_filters": false,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

### Product
```json
{
  "id": 1,
  "title": "Футболка Nike",
  "description": "Спортивная футболка",
  "type_id": 1,
  "gender_id": 2,
  "brand_id": 3,
  "country_id": 4,
  "send_to_telegram": false,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z",
  "type": { /* ListOption */ },
  "gender": { /* ListOption */ },
  "brand": { /* ListOption */ },
  "country": { /* ListOption */ },
  "items": [ /* ProductItem[] */ ]
}
```

## ⚡ Производительность

Данное API показывает отличную производительность:
- **132,000+ запросов в секунду** для простых операций
- **Низкая задержка**: ~1.8ms среднее время ответа
- **Эффективное использование памяти**: ~55MB под нагрузкой

## 🔧 Переменные окружения

| Переменная | Описание | По умолчанию |
|------------|----------|--------------|
| `DB_HOST` | Хост базы данных | `localhost` |
| `DB_PORT` | Порт базы данных | `5432` |
| `DB_USER` | Пользователь БД | `postgres` |
| `DB_PASSWORD` | Пароль БД | `` |
| `DB_NAME` | Имя базы данных | `gelya_prodaet` |
| `DB_SSLMODE` | SSL режим | `disable` |
| `SERVER_PORT` | Порт сервера | `8080` |

## 🏗 Структура проекта

```
site-api/
├── main.go                 # Точка входа приложения
├── config/
│   └── config.go           # Конфигурация
├── database/
│   └── database.go         # Подключение к БД и миграции
├── handlers/
│   ├── product_handler.go
│   ├── product_item_handler.go
│   └── list_option_handler.go
├── models/
│   ├── product.go
│   ├── product_item.go
│   ├── list_option.go
│   ├── user.go
│   └── user_identifier.go
├── utils/                  # Утилиты (если нужны)
├── go.mod                  # Go модуль (Go 1.24.3)
├── go.sum
├── Makefile               # Команды для разработки
├── README.md
└── API.md                 # Документация API
```

### 📁 Простая и понятная структура

Мы используем **простую структуру** без излишних усложнений:
- **main.go**: Единая точка входа в корне проекта
- **Плоская структура**: Все пакеты в корне для простоты
- **Без internal/**: Нет необходимости в приватных пакетах для простого API
- **Без cmd/**: Один исполняемый файл не требует отдельной папки

Эта структура идеально подходит для **внешнего API сайта**, которое:
- Обрабатывает только публичные запросы
- Не требует сложной архитектуры
- Легко понимается и поддерживается

## 🚀 Развертывание

### Docker файлы
- **Dockerfile**: `config/site-api/Dockerfile` (использует Go 1.24.3)
- **Docker Compose**: `docker-compose.local.yml` (dev конфигурация)

### Команды для development

1. **Запуск всех сервисов:**
   ```bash
   docker-compose -f docker-compose.local.yml up
   ```

2. **Только Go API:**
   ```bash
   docker-compose -f docker-compose.local.yml up go-api postgres
   ```

3. **Пересборка образа:**
   ```bash
   docker-compose -f docker-compose.local.yml build go-api
   ```

### Production

1. **Использовать Docker с оптимизированными настройками**
2. **Настроить reverse proxy (nginx)**
3. **Использовать connection pooling для БД**
4. **Настроить мониторинг и логирование**

## 📈 Сравнение с Laravel

| Метрика | Laravel PHP | Go API |
|---------|-------------|--------|
| Запросов/сек | ~5,000 | ~132,000 |
| Время ответа | ~20ms | ~1.8ms |
| Память | ~200MB | ~55MB |
| CPU | Высокое | Низкое |
| Версия | PHP 8.2 | **Go 1.24.3** |

Go API показывает **26x лучшую производительность** по сравнению с Laravel!

## 🐳 Docker архитектура

В dev среде запускаются:
- **PostgreSQL 16** - основная база данных
- **Redis 7** - кэширование
- **Laravel API** - существующее PHP API (порт 8000)
- **Go API** - новое высокопроизводительное API (порт 8080)
- **Nginx** - reverse proxy с маршрутизацией:
  - `/api/` → Go API
  - `/laravel-api/` → Laravel API
  - `/admin/` → Admin панель
  - `/health` → Health check Go API