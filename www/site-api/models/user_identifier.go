package models

import (
	"encoding/json"
	"time"

	"gorm.io/gorm"
)

// UserIdentifier представляет идентификатор пользователя (телефон, email и т.д.)
type UserIdentifier struct {
	ID        uint            `json:"id" gorm:"primaryKey"`
	UserID    uint            `json:"user_id" gorm:"not null"`
	Type      string          `json:"type" gorm:"type:varchar(255);not null"`
	Value     string          `json:"value" gorm:"type:varchar(255);not null"`
	ExtraData json.RawMessage `json:"extra_data" gorm:"type:jsonb"`
	CreatedAt time.Time       `json:"created_at"`
	UpdatedAt time.Time       `json:"updated_at"`
	DeletedAt gorm.DeletedAt  `json:"deleted_at" gorm:"index"`

	// Связи
	User User `json:"user" gorm:"foreignKey:UserID;references:ID"`
}

// TableName возвращает имя таблицы для GORM
func (UserIdentifier) TableName() string {
	return "user_identifiers"
}
