.PHONY: build run dev test clean docker-build docker-run

# Переменные
APP_NAME=site-api
BUILD_DIR=build
DOCKER_IMAGE=site-api:latest
DOCKERFILE_PATH=../../config/site-api/Dockerfile

# Сборка приложения
build:
	@echo "🔨 Сборка приложения..."
	@mkdir -p $(BUILD_DIR)
	@go build -o $(BUILD_DIR)/$(APP_NAME) main.go
	@echo "✅ Сборка завершена: $(BUILD_DIR)/$(APP_NAME)"

# Запуск приложения
run: build
	@echo "🚀 Запуск приложения..."
	@./$(BUILD_DIR)/$(APP_NAME)

# Разработка (с автоперезагрузкой)
dev:
	@echo "🔄 Запуск в режиме разработки..."
	@go run main.go

# Установка зависимостей
deps:
	@echo "📦 Установка зависимостей..."
	@go mod tidy
	@go mod download

# Тестирование
test:
	@echo "🧪 Запуск тестов..."
	@go test -v ./...

# Очистка
clean:
	@echo "🧹 Очистка..."
	@rm -rf $(BUILD_DIR)
	@go clean

# Docker сборка
docker-build:
	@echo "🐳 Сборка Docker образа..."
	@cd ../.. && docker build -f $(DOCKERFILE_PATH) -t $(DOCKER_IMAGE) .

# Docker запуск
docker-run:
	@echo "🐳 Запуск Docker контейнера..."
	@docker run -p 8080:8080 --env-file .env $(DOCKER_IMAGE)

# Форматирование кода
fmt:
	@echo "🎨 Форматирование кода..."
	@go fmt ./...

# Линтинг
lint:
	@echo "🔍 Проверка кода..."
	@golangci-lint run

# Помощь
help:
	@echo "Доступные команды:"
	@echo "  build       - Сборка приложения"
	@echo "  run         - Запуск приложения"
	@echo "  dev         - Запуск в режиме разработки"
	@echo "  deps        - Установка зависимостей"
	@echo "  test        - Запуск тестов"
	@echo "  clean       - Очистка"
	@echo "  docker-build - Сборка Docker образа"
	@echo "  docker-run  - Запуск Docker контейнера"
	@echo "  fmt         - Форматирование кода"
	@echo "  lint        - Проверка кода"
	@echo "  help        - Показать эту справку" 