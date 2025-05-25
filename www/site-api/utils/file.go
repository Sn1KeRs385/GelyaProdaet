package utils

import (
	"site-api/config"
	"site-api/models"
)

// GenerateFileURL формирует URL для файла на основе конфигурации S3
// Формат: https://{S3_URL}/{S3_BUCKET}/{file.path}/{file.filename}
// Если S3_URL не настроен, возвращает nil
func GenerateFileURL(file *models.File, cfg *config.Config) *string {
	// Если нет S3_URL в конфигурации, возвращаем null
	if cfg.S3.URL == "" {
		return nil
	}

	// Если файл не готов, возвращаем null
	if file.Status != "FINISHED" {
		return nil
	}

	// Формируем URL: https://{S3_URL}/{S3_BUCKET}/{file.path}/{file.filename}
	url := cfg.S3.URL + "/" + cfg.S3.Bucket + "/" + file.Path + "/" + file.Filename
	return &url
}

// SetFileURLs устанавливает URL для массива файлов
func SetFileURLs(files []models.File, cfg *config.Config) {
	for i := range files {
		files[i].URL = GenerateFileURL(&files[i], cfg)
	}
}
