# API Documentation

Документация для Site API (Go) - высокопроизводительного API для внешних запросов сайта GelyaProdaet.

## 🎯 Базовый URL

**Production**: `http://api.gelyaprodaet.local/site/`
**Development**: `http://localhost:8080/api/v1/`

## 📚 Endpoints

### 📦 Товары (Products)

#### GET /products
Возвращает все товары с полными связями (product-items и опции) с поддержкой фильтрации, пагинации и сортировки.

**Базовая фильтрация:**
- Всегда показываются только товары, у которых есть хотя бы один `product_item` с `is_for_sale=true`
- В связях `items` загружаются только элементы с `is_for_sale=true`

**Запрос:**
```http
GET /products?is_active=true&type_id=1,2&size_id=5,6&page=1&per_page=10&sort_by=price&sort_direction=asc
```

**Параметры запроса:**

| Параметр | Тип | Описание |
|----------|-----|----------|
| **Фильтрация** |
| `is_active` | boolean | `true` - товары с непроданными items (`is_sold=false`)<br>`false` - товары с проданными items (`is_sold=true`) |
| `type_id` | string | Список ID типов через запятую, например: `1,2,3` |
| `gender_id` | string | Список ID гендеров через запятую, например: `1,2` |
| `brand_id` | string | Список ID брендов через запятую, например: `3,4,5` |
| `country_id` | string | Список ID стран через запятую, например: `1,2` |
| `size_id` | string | Список ID размеров через запятую, например: `5,6,7` |
| `color_id` | string | Список ID цветов через запятую, например: `1,2,3` |
| `size_year_id` | string | Список ID размеров по годам через запятую, например: `1,2` |
| **Пагинация** |
| `page` | integer | Номер страницы (по умолчанию: `1`) |
| `per_page` | integer | Количество товаров на странице (по умолчанию: `20`, максимум: `100`) |
| **Сортировка** |
| `sort_by` | string | Поле для сортировки: `created_at`, `price` (по умолчанию: `created_at`) |
| `sort_direction` | string | Направление сортировки: `asc`, `desc` (по умолчанию: `desc`) |

**Логика фильтрации:**
- **Фильтры товаров** (`type_id`, `gender_id`, `brand_id`, `country_id`) - фильтруют по полям самого товара
- **Фильтры элементов** (`size_id`, `color_id`, `size_year_id`) - показывают товары, у которых есть хотя бы один элемент с указанными характеристиками
- **Cross-конвертация размеров**: При фильтрации по `size_id` автоматически ищутся товары с соответствующими `size_year_id` и наоборот. Например, при поиске по размеру "104" будут найдены товары с размером "3-4 года"

**Логика сортировки:**
- **`created_at`** - сортировка по дате создания товара
- **`price`** - сортировка по минимальной цене среди всех элементов товара (с учетом скидочной цены `price_final`)

**Ответ:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Футболка Nike",
      "slug": "futbolka-nike",
      "description": "Спортивная футболка из хлопка",
      "type_id": 1,
      "gender_id": 2,
      "brand_id": 3,
      "country_id": 4,
      "send_to_telegram": false,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z",
      "type": {
        "id": 1,
        "group_slug": "types",
        "title": "Футболка",
        "weight": 10
      },
      "gender": {
        "id": 2,
        "group_slug": "genders",
        "title": "Мужской",
        "weight": 20
      },
      "brand": {
        "id": 3,
        "group_slug": "brands",
        "title": "Nike",
        "weight": 30
      },
      "country": {
        "id": 4,
        "group_slug": "countries",
        "title": "США",
        "weight": 40
      },
      "files": [
        {
          "id": 1,
          "status": "FINISHED",
          "collection": "default",
          "original_filename": "nike-shirt-1.jpg",
          "type": "image/jpeg",
          "url": "https://s3.amazonaws.com/gelya-prodaet/uploads/products/nike-shirt-1.jpg"
        },
        {
          "id": 2,
          "status": "FINISHED",
          "collection": "default",
          "original_filename": "nike-shirt-2.jpg",
          "type": "image/jpeg",
          "url": "https://s3.amazonaws.com/gelya-prodaet/uploads/products/nike-shirt-2.jpg"
        }
      ],
      "items": [
        {
          "id": 1,
          "product_id": 1,
          "size_id": 5,
          "color_id": 6,
          "price_buy": 2000,
          "price": 3500,
          "price_final": 3200,
          "count": 1,
          "is_sold": false,
          "is_for_sale": true,
          "is_reserved": false,
          "size": {
            "id": 5,
            "group_slug": "sizes",
            "title": "L",
            "weight": 50
          },
          "color": {
            "id": 6,
            "group_slug": "colors",
            "title": "Черный",
            "weight": 60
          }
        }
      ]
    }
  ],
  "meta": {
    "page": 1,
    "per_page": 20,
    "total": 150,
    "total_pages": 8,
    "has_next": true,
    "has_prev": false
  }
}
```

**Примеры запросов:**
```http
# Первая страница с 10 товарами
GET /products?page=1&per_page=10

# Сортировка по цене (от дешевых к дорогим)
GET /products?sort_by=price&sort_direction=asc

# Сортировка по дате (новые сначала)
GET /products?sort_by=created_at&sort_direction=desc

# Только активные товары с пагинацией
GET /products?is_active=true&page=2&per_page=15

# Футболки и кроссовки от Nike, отсортированные по цене
GET /products?type_id=1,2&brand_id=3&sort_by=price&sort_direction=asc

# Товары размера L или XL, вторая страница
GET /products?size_id=5,6&page=2&per_page=20

# Комбинированный запрос с фильтрацией, пагинацией и сортировкой
GET /products?is_active=true&type_id=1&brand_id=3,4&size_id=5,6,7&page=1&per_page=12&sort_by=price&sort_direction=asc

# Cross-конвертация размеров: поиск по размеру роста
GET /products?size_id=10  # Найдет товары с размером 104см И с возрастом 3-4 года

# Cross-конвертация размеров: поиск по возрасту
GET /products?size_year_id=15  # Найдет товары с возрастом 5 лет И с размером 116см

# Комбинированный поиск размеров и возрастов
GET /products?size_id=10,11&size_year_id=15,16  # Максимально широкий поиск по размерам
```

#### GET /products/:slug
Возвращает один товар по slug с полными связями.

**Особенности:**
- В связях `items` загружаются только элементы с `is_for_sale=true`

**Запрос:**
```http
GET /products/nike-air-max-90
```

**Ответ:**
```json
{
  "id": 1,
  "title": "Футболка Nike",
  "slug": "futbolka-nike",
  "description": "Спортивная футболка из хлопка",
  "type_id": 1,
  "gender_id": 2,
  "brand_id": 3,
  "country_id": 4,
  "send_to_telegram": false,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z",
  "type": { /* ListOption */ },
  "gender": { /* ListOption */ },
  "brand": { /* ListOption */ },
  "country": { /* ListOption */ },
  "files": [ /* File[] */ ],
  "items": [ /* Только ProductItem[] с is_for_sale=true */ ]
}
```

**Ошибки:**
- `400` - Slug товара не указан
- `404` - Товар не найден
- `500` - Внутренняя ошибка сервера

### 📋 Опции (Options)

#### GET /options
Возвращает все опции отсортированные по group_slug, weight, title.

**Запрос:**
```http
GET /options
```

**Ответ:**
```json
[
  {
    "id": 1,
    "group_slug": "brands",
    "title": "Adidas",
    "weight": 10,
    "is_hidden_from_user_filters": false,
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  },
  {
    "id": 2,
    "group_slug": "brands",
    "title": "Nike",
    "weight": 20,
    "is_hidden_from_user_filters": false,
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  },
  {
    "id": 3,
    "group_slug": "colors",
    "title": "Белый",
    "weight": 10,
    "is_hidden_from_user_filters": false,
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  }
]
```

### 🏥 Проверка здоровья

#### GET /health
Проверка состояния API.

**Запрос:**
```http
GET /health
```

**Ответ:**
```json
{
  "status": "ok",
  "message": "API работает"
}
```

## 🗂 Структуры данных

### Product
```json
{
  "id": 1,
  "title": "string",
  "slug": "string",
  "description": "string|null",
  "type_id": 1,
  "gender_id": 1,
  "brand_id": 1,
  "country_id": 1,
  "send_to_telegram": false,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z",
  "type": "ListOption",
  "gender": "ListOption",
  "brand": "ListOption|null",
  "country": "ListOption|null",
  "files": "File[]",
  "items": "ProductItem[]"
}
```

### ProductItem
```json
{
  "id": 1,
  "product_id": 1,
  "size_id": 1,
  "size_year_id": 1,
  "color_id": 1,
  "price_buy": 2000,
  "price": 3500,
  "price_final": 3200,
  "price_sell": 3000,
  "count": 1,
  "is_sold": false,
  "is_for_sale": true,
  "is_reserved": false,
  "sold_at": "2024-01-01T00:00:00Z|null",
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z",
  "size": "ListOption|null",
  "size_year": "ListOption|null",
  "color": "ListOption|null"
}
```

### File
```json
{
  "id": 1,
  "status": "FINISHED",
  "collection": "default",
  "original_filename": "image.jpg",
  "type": "image/jpeg",
  "url": "https://s3.amazonaws.com/gelya-prodaet/uploads/products/image.jpg",
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

**Пример с null URL (когда S3_URL не настроен):**
```json
{
  "id": 2,
  "status": "FINISHED", 
  "collection": "default",
  "original_filename": "image2.jpg",
  "type": "image/jpeg",
  "url": null,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

**Примечания по полям:**
- Возвращаются только файлы со статусом `"FINISHED"` и `deleted_at IS NULL`
- `url` может быть `null` если S3_URL не настроен в конфигурации
- `url` формируется по формуле: `https://{S3_URL}/{S3_BUCKET}/{file.path}/{file.filename}`
- Возможные статусы: `UPLOADING`, `CREATED`, `WAITING_QUEUE`, `CONVERTING`, `FINISHED`, `DELETED`, `ERROR`

### ListOption
```json
{
  "id": 1,
  "group_slug": "string",
  "title": "string",
  "weight": 10,
  "is_hidden_from_user_filters": false,
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

## 🚀 Особенности

- **Только чтение**: API предназначено только для чтения данных (GET запросы)
- **Полные связи**: Товары возвращаются с полными связями включая items, опции и файлы
- **Файлы из S3**: Автоматическое формирование URL для файлов из S3 хранилища
- **Фильтрация файлов**: Возвращаются только файлы со статусом FINISHED и не удаленные
- **Nullable URL**: URL файлов будет `null` если S3_URL не настроен в конфигурации  
- **Умное формирование URL**: URL формируется по формуле `https://{S3_URL}/{S3_BUCKET}/{file.path}/{file.filename}`
- **Автоматическая фильтрация**: Показываются только товары с элементами доступными для продажи (`is_for_sale=true`)
- **Автоматическая конвертация размеров**: При фильтрации по размерам происходит cross-поиск между `size_id` и `size_year_id`
- **Гибкая фильтрация**: Поддержка фильтрации по множественным критериям
- **Статус активности**: Фильтрация по состоянию продаж (`is_active`)
- **Множественные фильтры**: Поддержка списков ID через запятую
- **Пагинация**: Настраиваемое количество товаров на странице с метаинформацией
- **Сортировка**: По дате создания или минимальной цене с учетом скидок
- **Умная сортировка по цене**: Учитывает `price_final` если указана, иначе `price`
- **Полная метаинформация**: Данные о текущей странице, общем количестве, навигации
- **Валидация параметров**: Автоматическая проверка и корректировка невалидных значений
- **Сортировка опций**: Опции автоматически сортируются по group_slug, weight, title
- **Высокая производительность**: Go + GORM с оптимизированными запросами

## 🔗 Примеры использования

### Получить первую страницу товаров
```javascript
const response = await fetch('/api/v1/products?page=1&per_page=10');
const result = await response.json();

console.log('Товары:', result.data);
console.log('Метаинформация:', result.meta);
// meta содержит: page, per_page, total, total_pages, has_next, has_prev
```

### Получить товары с сортировкой по цене
```javascript
// От дешевых к дорогим
const response = await fetch('/api/v1/products?sort_by=price&sort_direction=asc&page=1&per_page=20');
const cheapProducts = await response.json();
```

### Фильтрация с пагинацией
```javascript
// Активные мужские футболки от Nike, отсортированные по цене
const response = await fetch('/api/v1/products?is_active=true&gender_id=1&type_id=1&brand_id=3&sort_by=price&sort_direction=asc&page=1&per_page=12');
const filteredProducts = await response.json();

console.log(`Показано ${filteredProducts.data.length} из ${filteredProducts.meta.total} товаров`);
console.log(`Страница ${filteredProducts.meta.page} из ${filteredProducts.meta.total_pages}`);
```

### Реализация пагинации на фронтенде
```javascript
async function loadProducts(page = 1, perPage = 20, filters = {}) {
  const params = new URLSearchParams({
    page: page.toString(),
    per_page: perPage.toString(),
    ...filters
  });
  
  const response = await fetch(`/api/v1/products?${params}`);
  const result = await response.json();
  
  return {
    products: result.data,
    pagination: result.meta
  };
}

// Использование
const { products, pagination } = await loadProducts(1, 15, {
  is_active: 'true',
  type_id: '1,2',
  sort_by: 'price',
  sort_direction: 'asc'
});

// Проверка наличия следующей страницы
if (pagination.has_next) {
  console.log('Есть следующая страница');
}
```

### Получить все опции для фильтров
```javascript
const response = await fetch('/api/v1/options');
const options = await response.json();

// Группировка по типам для создания фильтров
const optionsByGroup = options.reduce((acc, option) => {
  if (!acc[option.group_slug]) {
    acc[option.group_slug] = [];
  }
  acc[option.group_slug].push(option);
  return acc;
}, {});

// Теперь можно использовать для создания UI фильтров:
// optionsByGroup.types - для фильтра по типам
// optionsByGroup.brands - для фильтра по брендам
// optionsByGroup.sizes - для фильтра по размерам
// и т.д.
```

### Cross-конвертация размеров в фильтрах
```javascript
// Поиск товаров для ребенка 5 лет
const response = await fetch('/api/v1/products?size_year_id=15');
const result = await response.json();
// Найдет товары как с size_year_id=15 (5 лет), так и с size_id соответствующим росту 116см

// Поиск товаров по росту 104см
const response2 = await fetch('/api/v1/products?size_id=10');
const result2 = await response2.json();
// Найдет товары как с size_id=10 (104см), так и с size_year_id соответствующим возрасту 3-4 года

// Широкий поиск по размерам
const filters = {
  size_id: '10,11',        // 104см, 110см
  size_year_id: '13,14'    // 3 года, 4 года
};
const params = new URLSearchParams(filters);
const response3 = await fetch(`/api/v1/products?${params}`);
// API автоматически найдет все связанные размеры и возрасты
```

### Получить конкретный товар по slug
```javascript
const response = await fetch('/api/v1/products/futbolka-nike');
const product = await response.json();
// Возвращается объект товара напрямую (без meta)

console.log('Товар:', product.title); // "Футболка Nike"
console.log('Slug:', product.slug);   // "futbolka-nike"
```

### Работа с файлами товаров
```javascript
const response = await fetch('/api/v1/products/futbolka-nike');
const product = await response.json();

// Получаем все файлы товара
const files = product.files;
console.log('Количество файлов:', files.length);

// Фильтруем только изображения с валидными URL
const images = files.filter(file => 
  file.type?.startsWith('image/') && file.url !== null
);

// Отображаем изображения
images.forEach((image, index) => {
  const img = document.createElement('img');
  img.src = image.url;
  img.alt = image.original_filename;
  img.className = 'product-image';
  document.getElementById('product-gallery').appendChild(img);
});

// Получаем первое изображение как главное (с fallback если URL null)
const mainImage = images[0]?.url || '/placeholder.jpg';
document.getElementById('main-image').src = mainImage;

// Обработка случая когда S3 не настроен
if (files.length > 0 && files.every(f => f.url === null)) {
  console.log('S3_URL не настроен - файлы недоступны');
  // Показать плейсхолдер или сообщение об ошибке
}
```

## 📏 Маппинг размеров

API автоматически конвертирует размеры между числовыми значениями (см/рост) и возрастными категориями:

| Размер (см) | Возраст (лет) |
|-------------|---------------|
| 92          | 1-2           |
| 98          | 2-3           |
| 104         | 3-4           |
| 110         | 4-5           |
| 116         | 5-6           |
| 122         | 6-7           |
| 128         | 7-8           |
| 134         | 8-9           |
| 140         | 9-10          |
| 146         | 10-11         |
| 152         | 12-13         |
| 158         | 14-15         |
| 164         | 16-17         |

**Примеры cross-конвертации:**
- Поиск по `size_id` для размера "104" → найдутся товары с `size_year_id` "3" и "4"
- Поиск по `size_year_id` для возраста "5" → найдутся товары с `size_id` "116"

## ❌ Ошибки

Все ошибки возвращаются в формате:
```json
{
  "error": "Описание ошибки"
}
```

**Коды ошибок:**
- `400` - Неверные параметры запроса
- `404` - Ресурс не найден
- `500` - Внутренняя ошибка сервера 