package main

import (
	"log"
	"site-api/config"
	"site-api/database"
	"site-api/handlers"

	"github.com/gofiber/fiber/v2"
	"github.com/gofiber/fiber/v2/middleware/cors"
	"github.com/gofiber/fiber/v2/middleware/logger"
	"github.com/joho/godotenv"
)

func main() {
	// Загружаем переменные окружения
	if err := godotenv.Load(); err != nil {
		log.Println("Файл .env не найден, используем переменные окружения системы")
	}

	// Загружаем конфигурацию
	cfg := config.Load()

	// Подключаемся к базе данных
	if err := database.Connect(cfg); err != nil {
		log.Fatal("Ошибка подключения к базе данных:", err)
	}

	// Выполняем миграции
	if err := database.Migrate(); err != nil {
		log.Fatal("Ошибка миграции:", err)
	}

	// Создаем Fiber приложение
	app := fiber.New(fiber.Config{
		ErrorHandler: func(c *fiber.Ctx, err error) error {
			code := fiber.StatusInternalServerError
			if e, ok := err.(*fiber.Error); ok {
				code = e.Code
			}
			return c.Status(code).JSON(fiber.Map{
				"error": err.Error(),
			})
		},
	})

	// Middleware
	app.Use(logger.New())
	app.Use(cors.New(cors.Config{
		AllowOrigins: "*",
		AllowMethods: "GET,POST,PUT,DELETE,OPTIONS",
		AllowHeaders: "Origin,Content-Type,Accept,Authorization",
	}))

	// API маршруты
	api := app.Group("/api/v1")

	// Маршруты для товаров (только чтение для внешнего API)
	api.Get("/products", handlers.GetProducts)
	api.Get("/products/:id", handlers.GetProduct)

	// Маршрут для опций (только чтение для внешнего API)
	api.Get("/options", handlers.GetOptions)

	// Проверка здоровья
	app.Get("/health", func(c *fiber.Ctx) error {
		return c.JSON(fiber.Map{
			"status":  "ok",
			"message": "API работает",
		})
	})

	// Запускаем сервер
	log.Printf("🚀 Сервер запущен на порту %s", cfg.Server.Port)
	log.Fatal(app.Listen(":" + cfg.Server.Port))
}
