# Многоэтапная сборка для оптимизации размера образа
FROM golang:1.24.3-alpine AS builder

# Устанавливаем необходимые пакеты
RUN apk add --no-cache git ca-certificates tzdata

# Создаем рабочую директорию
WORKDIR /app

# Копируем go mod файлы
COPY www/site-api/go.mod www/site-api/go.sum ./

# Скачиваем зависимости
RUN go mod download

# Копируем исходный код
COPY www/site-api/ ./

# Собираем приложение
RUN CGO_ENABLED=0 GOOS=linux go build -a -installsuffix cgo -o site-api main.go

# Финальный образ
FROM alpine:latest

# Устанавливаем ca-certificates для HTTPS запросов
RUN apk --no-cache add ca-certificates

# Создаем пользователя для безопасности
RUN adduser -D -s /bin/sh appuser

# Создаем рабочую директорию
WORKDIR /app

# Копируем бинарный файл из builder этапа
COPY --from=builder /app/site-api .

# Меняем владельца файлов
RUN chown -R appuser:appuser /app

# Переключаемся на пользователя appuser
USER appuser

# Открываем порт
EXPOSE 8080

# Команда запуска
CMD ["./site-api"] 