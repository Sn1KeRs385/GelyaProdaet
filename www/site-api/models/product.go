package models

import (
	"time"
)

// Product представляет товар
type Product struct {
	ID             uint      `json:"id" gorm:"primaryKey"`
	Title          string    `json:"title" gorm:"type:varchar(255);not null"`
	Slug           string    `json:"slug" gorm:"type:varchar(255);not null;unique"`
	Description    *string   `json:"description" gorm:"type:text"`
	TypeID         uint      `json:"type_id" gorm:"not null"`
	GenderID       uint      `json:"gender_id" gorm:"not null"`
	BrandID        *uint     `json:"brand_id"`
	CountryID      *uint     `json:"country_id"`
	SendToTelegram bool      `json:"send_to_telegram" gorm:"default:false"`
	CreatedAt      time.Time `json:"created_at"`
	UpdatedAt      time.Time `json:"updated_at"`

	// Связи
	Type    ListOption    `json:"type" gorm:"foreignKey:TypeID;references:ID"`
	Gender  ListOption    `json:"gender" gorm:"foreignKey:GenderID;references:ID"`
	Brand   *ListOption   `json:"brand" gorm:"foreignKey:BrandID;references:ID"`
	Country *ListOption   `json:"country" gorm:"foreignKey:CountryID;references:ID"`
	Items   []ProductItem `json:"items" gorm:"foreignKey:ProductID"`
}

// TableName возвращает имя таблицы для GORM
func (Product) TableName() string {
	return "products"
}
