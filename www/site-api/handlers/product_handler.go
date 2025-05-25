package handlers

import (
	"site-api/config"
	"site-api/database"
	"site-api/models"
	"site-api/utils"
	"strconv"
	"strings"

	"github.com/gofiber/fiber/v2"
	"gorm.io/gorm"
)

// ProductHandler содержит зависимости для обработчиков товаров
type ProductHandler struct {
	Config *config.Config
}

// NewProductHandler создает новый экземпляр ProductHandler
func NewProductHandler(cfg *config.Config) *ProductHandler {
	return &ProductHandler{
		Config: cfg,
	}
}

// SizeToYearMapping маппинг размеров на годы (из SizeConverter.php)
var SizeToYearMapping = map[int][]int{
	92:  {1, 2},
	98:  {2, 3},
	104: {3, 4},
	110: {4, 5},
	116: {5, 6},
	122: {6, 7},
	128: {7, 8},
	134: {8, 9},
	140: {9, 10},
	146: {10, 11},
	152: {12, 13},
	158: {14, 15},
	164: {16, 17},
}

// getYearFromSize получает годы из размеров (портирование из PHP)
func getYearFromSize(sizes []string) []int {
	var years []int

	for _, sizeStr := range sizes {
		sizePartsList := strings.Split(sizeStr, "-")
		for _, sizePart := range sizePartsList {
			if size, err := strconv.Atoi(strings.TrimSpace(sizePart)); err == nil {
				if yearList, exists := SizeToYearMapping[size]; exists {
					years = append(years, yearList...)
				} else {
					// Логика для промежуточных размеров
					prevSize := 86
					for mapSize, mapYears := range SizeToYearMapping {
						if size < mapSize && size > prevSize {
							if prevYears, exists := SizeToYearMapping[prevSize]; exists && len(prevYears) > 0 {
								years = append(years, mapYears[0], prevYears[len(prevYears)-1])
							} else {
								years = append(years, mapYears[0])
							}
							break
						}
						prevSize = mapSize
					}
				}
			}
		}
	}

	return years
}

// getSizeFromYear получает размеры из годов (портирование из PHP)
func getSizeFromYear(years []string) []int {
	var sizes []int

	for _, yearStr := range years {
		yearPartsList := strings.Split(yearStr, "-")
		for _, yearPart := range yearPartsList {
			if year, err := strconv.Atoi(strings.TrimSpace(yearPart)); err == nil {
				for mapSize, mapYears := range SizeToYearMapping {
					for _, mapYear := range mapYears {
						if year == mapYear {
							sizes = append(sizes, mapSize)
							break
						}
					}
				}
			}
		}
	}

	return sizes
}

// getSizeOptionsByTitles получает размеры по их названиям из базы данных
func getSizeOptionsByTitles(titles []string) ([]uint, error) {
	var options []models.ListOption
	result := database.DB.Where("group_slug = ? AND title IN ?", "size", titles).Find(&options)
	if result.Error != nil {
		return nil, result.Error
	}

	var ids []uint
	for _, option := range options {
		ids = append(ids, option.ID)
	}
	return ids, nil
}

// getSizeYearOptionsByTitles получает размеры по годам по их названиям из базы данных
func getSizeYearOptionsByTitles(titles []string) ([]uint, error) {
	var options []models.ListOption
	result := database.DB.Where("group_slug = ? AND title IN ?", "size_year", titles).Find(&options)
	if result.Error != nil {
		return nil, result.Error
	}

	var ids []uint
	for _, option := range options {
		ids = append(ids, option.ID)
	}
	return ids, nil
}

// convertSizeIDsToSizeYearIDs конвертирует ID размеров в ID размеров по годам
func convertSizeIDsToSizeYearIDs(sizeIDs []int) ([]uint, error) {
	// Получаем размеры из базы
	var sizeOptions []models.ListOption
	var uintSizeIDs []uint
	for _, id := range sizeIDs {
		uintSizeIDs = append(uintSizeIDs, uint(id))
	}

	result := database.DB.Where("group_slug = ? AND id IN ?", "size", uintSizeIDs).Find(&sizeOptions)
	if result.Error != nil {
		return nil, result.Error
	}

	// Извлекаем названия размеров
	var sizeTitles []string
	for _, option := range sizeOptions {
		sizeTitles = append(sizeTitles, option.Title)
	}

	// Конвертируем размеры в годы
	years := getYearFromSize(sizeTitles)

	// Формируем названия годов
	var yearTitles []string
	for _, year := range years {
		yearTitles = append(yearTitles, strconv.Itoa(year))
	}

	// Получаем ID опций годов
	return getSizeYearOptionsByTitles(yearTitles)
}

// convertSizeYearIDsToSizeIDs конвертирует ID размеров по годам в ID размеров
func convertSizeYearIDsToSizeIDs(sizeYearIDs []int) ([]uint, error) {
	// Получаем размеры по годам из базы
	var sizeYearOptions []models.ListOption
	var uintSizeYearIDs []uint
	for _, id := range sizeYearIDs {
		uintSizeYearIDs = append(uintSizeYearIDs, uint(id))
	}

	result := database.DB.Where("group_slug = ? AND id IN ?", "size_year", uintSizeYearIDs).Find(&sizeYearOptions)
	if result.Error != nil {
		return nil, result.Error
	}

	// Извлекаем названия годов
	var yearTitles []string
	for _, option := range sizeYearOptions {
		yearTitles = append(yearTitles, option.Title)
	}

	// Конвертируем годы в размеры
	sizes := getSizeFromYear(yearTitles)

	// Формируем названия размеров
	var sizeTitles []string
	for _, size := range sizes {
		sizeTitles = append(sizeTitles, strconv.Itoa(size))
	}

	// Получаем ID опций размеров
	return getSizeOptionsByTitles(sizeTitles)
}

// PaginationParams хранит параметры пагинации
type PaginationParams struct {
	Page    int
	PerPage int
	Offset  int
}

// parsePaginationParams извлекает и валидирует параметры пагинации
func parsePaginationParams(c *fiber.Ctx) PaginationParams {
	perPage, _ := strconv.Atoi(c.Query("per_page", "20"))
	page, _ := strconv.Atoi(c.Query("page", "1"))

	// Валидация параметров пагинации
	if perPage <= 0 || perPage > 100 {
		perPage = 20
	}
	if page <= 0 {
		page = 1
	}

	offset := (page - 1) * perPage

	return PaginationParams{
		Page:    page,
		PerPage: perPage,
		Offset:  offset,
	}
}

// applyFilters применяет все фильтры к переданному запросу
func applyFilters(query *gorm.DB, c *fiber.Ctx) *gorm.DB {
	// Базовая фильтрация: показываем только products у которых есть хотя бы один items с is_for_sale=true
	query = query.Where("EXISTS (SELECT 1 FROM product_items WHERE product_items.product_id = products.id AND product_items.is_for_sale = true)")

	// Фильтр is_active
	isActiveParam := c.Query("is_active")
	if isActiveParam != "" {
		isActive, err := strconv.ParseBool(isActiveParam)
		if err == nil {
			if isActive {
				// true - показать products у которых есть items с is_sold=false
				query = query.Where("EXISTS (SELECT 1 FROM product_items WHERE product_items.product_id = products.id AND product_items.is_for_sale = true AND product_items.is_sold = false)")
			} else {
				// false - показать products у которых есть items с is_sold=true
				query = query.Where("EXISTS (SELECT 1 FROM product_items WHERE product_items.product_id = products.id AND product_items.is_for_sale = true AND product_items.is_sold = true)")
			}
		}
	}

	// Фильтры по полям products
	if typeIDs := c.Query("type_id"); typeIDs != "" {
		ids := parseIntArray(typeIDs)
		if len(ids) > 0 {
			query = query.Where("type_id IN ?", ids)
		}
	}

	if genderIDs := c.Query("gender_id"); genderIDs != "" {
		ids := parseIntArray(genderIDs)
		if len(ids) > 0 {
			query = query.Where("gender_id IN ?", ids)
		}
	}

	if brandIDs := c.Query("brand_id"); brandIDs != "" {
		ids := parseIntArray(brandIDs)
		if len(ids) > 0 {
			query = query.Where("brand_id IN ?", ids)
		}
	}

	if countryIDs := c.Query("country_id"); countryIDs != "" {
		ids := parseIntArray(countryIDs)
		if len(ids) > 0 {
			query = query.Where("country_id IN ?", ids)
		}
	}

	// Фильтры по полям product_items
	itemsConditions := []string{}
	itemsParams := []interface{}{}

	// Улучшенная фильтрация по размерам с cross-конвертацией
	if sizeIDs := c.Query("size_id"); sizeIDs != "" {
		ids := parseIntArray(sizeIDs)
		if len(ids) > 0 {
			// Конвертируем размеры в годы для поиска по size_year_id
			sizeYearIDs, err := convertSizeIDsToSizeYearIDs(ids)
			if err == nil && len(sizeYearIDs) > 0 {
				// Ищем по размерам ИЛИ по соответствующим годам
				itemsConditions = append(itemsConditions, "(product_items.size_id IN ? OR product_items.size_year_id IN ?)")
				itemsParams = append(itemsParams, ids, sizeYearIDs)
			} else {
				// Если конвертация не удалась, ищем только по размерам
				itemsConditions = append(itemsConditions, "product_items.size_id IN ?")
				itemsParams = append(itemsParams, ids)
			}
		}
	}

	if colorIDs := c.Query("color_id"); colorIDs != "" {
		ids := parseIntArray(colorIDs)
		if len(ids) > 0 {
			itemsConditions = append(itemsConditions, "product_items.color_id IN ?")
			itemsParams = append(itemsParams, ids)
		}
	}

	// Улучшенная фильтрация по годам с cross-конвертацией
	if sizeYearIDs := c.Query("size_year_id"); sizeYearIDs != "" {
		ids := parseIntArray(sizeYearIDs)
		if len(ids) > 0 {
			// Конвертируем годы в размеры для поиска по size_id
			sizeIDs, err := convertSizeYearIDsToSizeIDs(ids)
			if err == nil && len(sizeIDs) > 0 {
				// Ищем по годам ИЛИ по соответствующим размерам
				itemsConditions = append(itemsConditions, "(product_items.size_year_id IN ? OR product_items.size_id IN ?)")
				itemsParams = append(itemsParams, ids, sizeIDs)
			} else {
				// Если конвертация не удалась, ищем только по годам
				itemsConditions = append(itemsConditions, "product_items.size_year_id IN ?")
				itemsParams = append(itemsParams, ids)
			}
		}
	}

	// Если есть фильтры по items, добавляем их
	if len(itemsConditions) > 0 {
		whereClause := "EXISTS (SELECT 1 FROM product_items WHERE product_items.product_id = products.id AND product_items.is_for_sale = true AND " + strings.Join(itemsConditions, " AND ") + ")"
		query = query.Where(whereClause, itemsParams...)
	}

	return query
}

// applySorting применяет сортировку к запросу
func applySorting(query *gorm.DB, c *fiber.Ctx) *gorm.DB {
	sortBy := c.Query("sort_by", "created_at")
	sortDirection := c.Query("sort_direction", "desc")

	// Валидация направления сортировки
	if sortDirection != "asc" && sortDirection != "desc" {
		sortDirection = "desc"
	}

	switch sortBy {
	case "created_at":
		query = query.Order("products.created_at " + sortDirection)
	case "price":
		// Сортировка по минимальной цене среди items (с учетом price_final)
		priceSubquery := `(
			SELECT MIN(
				CASE 
					WHEN product_items.price_final IS NOT NULL AND product_items.price_final > 0 
					THEN product_items.price_final 
					ELSE product_items.price 
				END
			) 
			FROM product_items 
			WHERE product_items.product_id = products.id 
			AND product_items.is_for_sale = true
		)`
		query = query.Order(priceSubquery + " " + sortDirection)
	default:
		// По умолчанию сортируем по дате создания
		query = query.Order("products.created_at " + sortDirection)
	}

	return query
}

// GetProducts возвращает все товары с полными связями, фильтрацией, пагинацией и сортировкой
func (h *ProductHandler) GetProducts(c *fiber.Ctx) error {
	var products []models.Product

	// Парсим параметры пагинации
	pagination := parsePaginationParams(c)

	// Строим основной запрос с фильтрами
	query := database.DB.Model(&models.Product{})
	query = applyFilters(query, c)

	// Подсчитываем общее количество товаров (до пагинации и сортировки)
	var total int64
	countQuery := database.DB.Model(&models.Product{})
	countQuery = applyFilters(countQuery, c)
	countQuery.Count(&total)

	// Применяем сортировку и пагинацию
	query = applySorting(query, c)
	query = query.Limit(pagination.PerPage).Offset(pagination.Offset)

	// Выполняем запрос с загрузкой связей
	result := query.
		Preload("Type").
		Preload("Gender").
		Preload("Brand").
		Preload("Country").
		Preload("Files", func(db *gorm.DB) *gorm.DB {
			// Загружаем только файлы со статусом FINISHED и не удаленные
			return db.Where("status = ? AND deleted_at IS NULL", "FINISHED")
		}).
		Preload("Items", func(db *gorm.DB) *gorm.DB {
			// Загружаем только items с is_for_sale=true
			return db.Where("is_for_sale = true")
		}).
		Preload("Items.Size").
		Preload("Items.SizeYear").
		Preload("Items.Color").
		Find(&products)

	if result.Error != nil {
		return c.Status(500).JSON(fiber.Map{
			"error": "Ошибка при получении товаров",
		})
	}

	// Устанавливаем URL для файлов
	for i := range products {
		utils.SetFileURLs(products[i].Files, h.Config)
	}

	// Рассчитываем метаинформацию
	totalPages := int((total + int64(pagination.PerPage) - 1) / int64(pagination.PerPage))

	return c.JSON(fiber.Map{
		"data": products,
		"meta": fiber.Map{
			"page":        pagination.Page,
			"per_page":    pagination.PerPage,
			"total":       total,
			"total_pages": totalPages,
			"has_next":    pagination.Page < totalPages,
			"has_prev":    pagination.Page > 1,
		},
	})
}

// parseIntArray парсит строку с ID разделенными запятыми в массив int
func parseIntArray(str string) []int {
	var result []int
	parts := strings.Split(str, ",")
	for _, part := range parts {
		part = strings.TrimSpace(part)
		if id, err := strconv.Atoi(part); err == nil {
			result = append(result, id)
		}
	}
	return result
}

// GetProduct возвращает товар по slug с полными связями
func (h *ProductHandler) GetProduct(c *fiber.Ctx) error {
	slug := c.Params("slug")
	if slug == "" {
		return c.Status(400).JSON(fiber.Map{
			"error": "Slug товара не указан",
		})
	}

	var product models.Product
	result := database.DB.
		Where("slug = ?", slug).
		Preload("Type").
		Preload("Gender").
		Preload("Brand").
		Preload("Country").
		Preload("Files", func(db *gorm.DB) *gorm.DB {
			// Загружаем только файлы со статусом FINISHED и не удаленные
			return db.Where("status = ? AND deleted_at IS NULL", "FINISHED")
		}).
		Preload("Items", func(db *gorm.DB) *gorm.DB {
			// Загружаем только items с is_for_sale=true
			return db.Where("is_for_sale = true")
		}).
		Preload("Items.Size").
		Preload("Items.SizeYear").
		Preload("Items.Color").
		First(&product)

	if result.Error != nil {
		return c.Status(404).JSON(fiber.Map{
			"error": "Товар не найден",
		})
	}

	// Устанавливаем URL для файлов
	utils.SetFileURLs(product.Files, h.Config)

	return c.JSON(product)
}
