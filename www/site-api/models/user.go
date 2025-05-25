package models

import (
	"time"

	"gorm.io/gorm"
)

// User представляет пользователя
type User struct {
	ID            uint           `json:"id" gorm:"primaryKey"`
	Password      *string        `json:"-" gorm:"type:varchar(255)"` // скрываем пароль в JSON
	RememberToken *string        `json:"-" gorm:"type:varchar(100)"` // скрываем токен в JSON
	CreatedAt     time.Time      `json:"created_at"`
	UpdatedAt     time.Time      `json:"updated_at"`
	DeletedAt     gorm.DeletedAt `json:"deleted_at" gorm:"index"`

	// Связи
	Identifiers []UserIdentifier `json:"identifiers" gorm:"foreignKey:UserID"`
}

// TableName возвращает имя таблицы для GORM
func (User) TableName() string {
	return "users"
}
