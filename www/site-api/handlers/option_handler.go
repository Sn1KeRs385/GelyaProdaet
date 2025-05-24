package handlers

import (
	"site-api/database"
	"site-api/models"

	"github.com/gofiber/fiber/v2"
)

// GetOptions возвращает все опции отсортированные по group_slug, weight, title
func GetOptions(c *fiber.Ctx) error {
	var options []models.ListOption

	// Загружаем все опции с сортировкой
	result := database.DB.
		Order("group_slug ASC, weight ASC, title ASC").
		Find(&options)

	if result.Error != nil {
		return c.Status(500).JSON(fiber.Map{
			"error": "Ошибка при получении опций",
		})
	}

	return c.JSON(options)
}
