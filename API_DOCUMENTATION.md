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
- `GET /api/products/{id}` - Get single product details
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
- `min_price` - Minimum price
- `max_price` - Maximum price
- `search` - Search query
- `sort` - Sort options: `price_low`, `price_high`, `name_asc`, `name_desc`, `newest`
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

Paginated responses include:

```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [...],
        "first_page_url": "...",
        "from": 1,
        "last_page": 5,
        "last_page_url": "...",
        "links": [...],
        "next_page_url": "...",
        "path": "...",
        "per_page": 12,
        "prev_page_url": null,
        "to": 12,
        "total": 50
    }
}
```

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error
