package models

import (
	"time"
)

// File представляет файл в системе
type File struct {
	ID                uint       `json:"id" gorm:"primaryKey"`
	Status            string     `json:"status" gorm:"type:varchar(255);not null"`
	Disk              string     `json:"-" gorm:"type:varchar(255);not null"`
	OwnerType         string     `json:"-" gorm:"type:varchar(255);not null"`
	OwnerID           uint       `json:"-" gorm:"not null"`
	Collection        string     `json:"collection" gorm:"type:varchar(255);not null"`
	Path              string     `json:"-" gorm:"type:varchar(255);not null"`
	Filename          string     `json:"-" gorm:"type:varchar(255);not null"`
	Type              *string    `json:"type" gorm:"type:varchar(255)"`
	Size              *int64     `json:"-" gorm:"type:bigint"`
	OriginalFilename  string     `json:"original_filename" gorm:"type:varchar(255);not null"`
	OriginalCreatedAt *time.Time `json:"-" gorm:"type:timestamp"`
	UUID              string     `json:"-" gorm:"type:varchar(255);not null"`
	Meta              *string    `json:"-" gorm:"type:json"`
	CreatedAt         time.Time  `json:"created_at"`
	UpdatedAt         time.Time  `json:"updated_at"`
	DeletedAt         *time.Time `json:"-" gorm:"type:timestamp"`

	// Computed field для URL (будет null если S3_URL не настроен)
	URL *string `json:"url" gorm:"-"`
}

// TableName возвращает имя таблицы для GORM
func (File) TableName() string {
	return "files"
}
