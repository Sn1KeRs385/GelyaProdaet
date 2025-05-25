package utils

import (
	"site-api/config"
	"site-api/models"
	"testing"
)

func TestGenerateFileURL(t *testing.T) {
	// Тест 1: Нормальный случай - все настроено
	cfg := &config.Config{
		S3: config.S3Config{
			URL:    "https://s3.amazonaws.com",
			Bucket: "gelya-prodaet",
		},
	}

	file := &models.File{
		Status:   "FINISHED",
		Path:     "uploads/products",
		Filename: "test.jpg",
	}

	result := GenerateFileURL(file, cfg)
	expected := "https://s3.amazonaws.com/gelya-prodaet/uploads/products/test.jpg"

	if result == nil {
		t.Fatal("Expected URL, got nil")
	}

	if *result != expected {
		t.Errorf("Expected %s, got %s", expected, *result)
	}

	// Тест 2: S3_URL не настроен - должен вернуть nil
	cfg.S3.URL = ""
	result = GenerateFileURL(file, cfg)

	if result != nil {
		t.Errorf("Expected nil when S3_URL is empty, got %s", *result)
	}

	// Тест 3: Файл не готов - должен вернуть nil
	cfg.S3.URL = "https://s3.amazonaws.com"
	file.Status = "UPLOADING"
	result = GenerateFileURL(file, cfg)

	if result != nil {
		t.Errorf("Expected nil when file status is not FINISHED, got %s", *result)
	}
}

func TestSetFileURLs(t *testing.T) {
	cfg := &config.Config{
		S3: config.S3Config{
			URL:    "https://s3.amazonaws.com",
			Bucket: "gelya-prodaet",
		},
	}

	files := []models.File{
		{
			ID:       1,
			Status:   "FINISHED",
			Path:     "uploads",
			Filename: "file1.jpg",
		},
		{
			ID:       2,
			Status:   "UPLOADING",
			Path:     "uploads",
			Filename: "file2.jpg",
		},
	}

	SetFileURLs(files, cfg)

	// Первый файл должен иметь URL
	if files[0].URL == nil {
		t.Error("Expected URL for finished file, got nil")
	} else {
		expected := "https://s3.amazonaws.com/gelya-prodaet/uploads/file1.jpg"
		if *files[0].URL != expected {
			t.Errorf("Expected %s, got %s", expected, *files[0].URL)
		}
	}

	// Второй файл не должен иметь URL (не готов)
	if files[1].URL != nil {
		t.Errorf("Expected nil URL for processing file, got %s", *files[1].URL)
	}
}
