package database

import (
	"fmt"
	"log"
	"site-api/config"
	"site-api/models"

	"gorm.io/driver/postgres"
	"gorm.io/gorm"
	"gorm.io/gorm/logger"
)

// DB глобальная переменная для доступа к базе данных
var DB *gorm.DB

// Connect подключается к базе данных
func Connect(cfg *config.Config) error {
	dsn := cfg.Database.GetDSN()

	db, err := gorm.Open(postgres.Open(dsn), &gorm.Config{
		Logger: logger.Default.LogMode(logger.Info),
	})

	if err != nil {
		return fmt.Errorf("не удалось подключиться к базе данных: %v", err)
	}

	DB = db
	log.Println("Успешно подключились к базе данных")

	return nil
}

// Migrate выполняет миграции моделей
func Migrate() error {
	err := DB.AutoMigrate(
		&models.ListOption{},
		&models.Product{},
		&models.ProductItem{},
		&models.File{},
		&models.User{},
		&models.UserIdentifier{},
	)

	if err != nil {
		return fmt.Errorf("ошибка миграции: %v", err)
	}

	log.Println("Миграции выполнены успешно")
	return nil
}
