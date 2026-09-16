# Local Shop API Documentation

## Base URL
```
http://your-domain.com/api
```

## Authentication
Most endpoints require authentication using Laravel Sanctum. Include the token in the Authorization header:

```
Authorization: Bearer {token}
```

## Public Endpoints

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login user
- `POST /api/forgot-password` - Send password reset link
- `POST /api/reset-password` - Reset password

### Shop Settings
- `GET /api/shop/settings` - Get shop settings
- `GET /api/shop/info` - Get shop information

### Products
- `GET /api/products` - Get all products (with filters)
- `GET /api/products/{id}` - Get single product details with related products
- `GET /api/products/search` - Search products
- `GET /api/products/featured` - Get featured products
- `GET /api/products/latest` - Get latest products

### Categories
- `GET /api/categories` - Get all categories
- `GET /api/categories/{id}` - Get single category
- `GET /api/categories/{id}/products` - Get products by category

## Protected Endpoints (Require Authentication)

### User Profile
- `GET /api/user` - Get user profile
- `PUT /api/user` - Update user profile
- `PUT /api/user/password` - Update user password
- `POST /api/logout` - Logout user

### User Addresses
- `GET /api/user/addresses` - Get user addresses
- `POST /api/user/addresses` - Add new address
- `PUT /api/user/addresses/{id}` - Update address
- `DELETE /api/user/addresses/{id}` - Delete address
- `PUT /api/user/addresses/{id}/default` - Set default address

### Cart
- `GET /api/cart` - Get cart items
- `POST /api/cart` - Add item to cart
- `PUT /api/cart/{id}` - Update cart item quantity
- `DELETE /api/cart/{id}` - Remove item from cart
- `DELETE /api/cart` - Clear cart

### Orders
- `GET /api/orders` - Get user orders
- `GET /api/orders/{id}` - Get single order details
- `POST /api/orders` - Create new order
- `PUT /api/orders/{id}/cancel` - Cancel order
- `PUT /api/orders/{id}/confirm-delivery` - Confirm order delivery

### Wishlist
- `GET /api/wishlist` - Get wishlist items
- `POST /api/wishlist` - Add item to wishlist
- `DELETE /api/wishlist/{id}` - Remove item from wishlist
- `POST /api/wishlist/check` - Check if product is in wishlist

## Response Format

All API responses follow this format:

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data here
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        // Validation errors if any
    }
}
```

## Product Filters

When fetching products, you can use these query parameters:

- `category` - Filter by category slug
- `min_price` - Minimum price (uses discount price if available)
- `max_price` - Maximum price (uses discount price if available)
- `search` - Search query
- `sort` - Sort options:
  - `price_low` - Sort by price lowest to highest (uses discount price if available)
  - `price_high` - Sort by price highest to lowest (uses discount price if available)
  - `name_asc` - Sort by name A to Z
  - `name_desc` - Sort by name Z to A
  - `newest` - Sort by newest first
  - Default: Sort by ID ascending (ID-wise)
- `per_page` - Items per page (default: 12)

## Example Requests

### Register User
```bash
curl -X POST http://your-domain.com/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+1234567890"
  }'
```

### Login
```bash
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get Products
```bash
curl -X GET "http://your-domain.com/api/products?per_page=10&sort=price_low"
```

### Get Single Product with Related Products
```bash
curl -X GET "http://your-domain.com/api/products/1"
```

Response includes:
```json
{
    "success": true,
    "data": {
        "product": { ... },
        "related_products": [ ... ]
    }
}
```

### Get Cart (Authenticated)
```bash
curl -X GET http://your-domain.com/api/cart \
  -H "Authorization: Bearer {your_token}"
```

### Add to Cart (Authenticated)
```bash
curl -X POST http://your-domain.com/api/cart \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

### Create Order (Authenticated)
```bash
curl -X POST http://your-domain.com/api/orders \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "address_id": 1,
    "payment_method": "cod",
    "items": [
      {
        "product_id": 1,
        "quantity": 2
      }
    ]
  }'
```

## Pagination

Paginated responses include pagination metadata:

```json
{
    "success": true,
    "data": [...],
    "pagination": {
        "total": 50,
        "per_page": 12,
        "current_page": 1,
        "last_page": 5,
        "from": 1,
        "to": 12
    }
}
```

- `total` - Total number of items
- `per_page` - Items per page
- `current_page` - Current page number
- `last_page` - Total number of pages
- `from` - Starting item number
- `to` - Ending item number

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error
