package models

import (
	"time"
)

// ProductItem представляет конкретный экземпляр товара
type ProductItem struct {
	ID         uint       `json:"id" gorm:"primaryKey"`
	ProductID  uint       `json:"product_id" gorm:"not null"`
	SizeID     *uint      `json:"size_id"`
	SizeYearID *uint      `json:"size_year_id"`
	ColorID    *uint      `json:"color_id"`
	PriceBuy   uint       `json:"price_buy" gorm:"not null"`
	Price      uint       `json:"price" gorm:"not null"`
	PriceFinal *uint      `json:"price_final"`
	PriceSell  *uint      `json:"price_sell"`
	Count      uint       `json:"count" gorm:"default:1"`
	IsSold     bool       `json:"is_sold" gorm:"default:false"`
	IsForSale  bool       `json:"is_for_sale" gorm:"default:true"`
	IsReserved bool       `json:"is_reserved" gorm:"default:false"`
	SoldAt     *time.Time `json:"sold_at"`
	CreatedAt  time.Time  `json:"created_at"`
	UpdatedAt  time.Time  `json:"updated_at"`

	// Связи
	Product  Product     `json:"-" gorm:"foreignKey:ProductID;references:ID"`
	Size     *ListOption `json:"size" gorm:"foreignKey:SizeID;references:ID"`
	SizeYear *ListOption `json:"size_year" gorm:"foreignKey:SizeYearID;references:ID"`
	Color    *ListOption `json:"color" gorm:"foreignKey:ColorID;references:ID"`
}

// TableName возвращает имя таблицы для GORM
func (ProductItem) TableName() string {
	return "product_items"
}
