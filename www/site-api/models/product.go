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
	TypeID         uint      `json:"-" gorm:"not null"`
	GenderID       uint      `json:"-" gorm:"not null"`
	BrandID        *uint     `json:"-"`
	CountryID      *uint     `json:"-"`
	SendToTelegram bool      `json:"-" gorm:"default:false"`
	CreatedAt      time.Time `json:"created_at"`
	UpdatedAt      time.Time `json:"updated_at"`

	// Связи
	Type    ListOption    `json:"type" gorm:"foreignKey:TypeID;references:ID"`
	Gender  ListOption    `json:"gender" gorm:"foreignKey:GenderID;references:ID"`
	Brand   *ListOption   `json:"brand" gorm:"foreignKey:BrandID;references:ID"`
	Country *ListOption   `json:"country" gorm:"foreignKey:CountryID;references:ID"`
	Files   []File        `json:"files" gorm:"polymorphic:Owner;polymorphicValue:Product"`
	Items   []ProductItem `json:"items" gorm:"foreignKey:ProductID"`
}

// TableName возвращает имя таблицы для GORM
func (Product) TableName() string {
	return "products"
}
