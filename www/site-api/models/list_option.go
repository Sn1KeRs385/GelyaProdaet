package models

import (
	"time"
)

// ListOption представляет опцию списка (размеры, цвета, бренды и т.д.)
type ListOption struct {
	ID                      uint      `json:"id" gorm:"primaryKey"`
	GroupSlug               string    `json:"group_slug" gorm:"type:varchar(255);not null"`
	Title                   string    `json:"title" gorm:"type:varchar(255);not null"`
	Weight                  int       `json:"-" gorm:"default:0"`
	IsHiddenFromUserFilters bool      `json:"is_hidden_from_user_filters" gorm:"default:false"`
	CreatedAt               time.Time `json:"created_at"`
	UpdatedAt               time.Time `json:"updated_at"`
}

// TableName возвращает имя таблицы для GORM
func (ListOption) TableName() string {
	return "list_options"
}
