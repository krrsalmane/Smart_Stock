# SmartStock API - Swagger Documentation Setup

## Quick Start

### 1. Install Dependencies
After cloning the project, run:

```bash
composer install
```

This will install the `darkaonline/l5-swagger` package which is used for Swagger/OpenAPI documentation.

### 2. Access Swagger UI

Once the application is running, visit:

- **Swagger UI:** `http://localhost:8000/api/docs`
- **Raw OpenAPI JSON:** `http://localhost:8000/api/documentation`

### 3. API Documentation

The API documentation includes:

#### Authentication Endpoints
- `POST /register` - Register new user
- `POST /login` - Login user (returns JWT token)
- `POST /logout` - Logout user (requires JWT)

#### User Endpoints
- `GET /user` - Get current user profile
- `PUT /user` - Update user profile

#### Product Management (Requires JWT + magasinier role)
- `GET /products` - List all products
- `POST /products` - Create new product
- `GET /products/{id}` - Get product details
- `PUT /products/{id}` - Update product
- `DELETE /products/{id}` - Delete product

#### Command Management (Requires JWT)
- `GET /commands` - List all commands
- `POST /commands` - Create new command
- `GET /commands/{id}` - Get command details
- `PUT /commands/{id}` - Update command

#### Supplier Management (Requires JWT)
- `GET /suppliers` - List all suppliers
- `POST /suppliers` - Create new supplier
- `GET /suppliers/{id}` - Get supplier details
- `PUT /suppliers/{id}` - Update supplier
- `DELETE /suppliers/{id}` - Delete supplier
- `POST /suppliers/{id}/products` - Link product to supplier
- `DELETE /suppliers/{id}/products/{productId}` - Unlink product
- `POST /suppliers/{id}/commands` - Link command to supplier
- `DELETE /suppliers/{id}/commands/{commandId}` - Unlink command

#### Category Management (Requires JWT + magasinier role)
- `GET /categories` - List all categories
- `POST /categories` - Create new category
- `GET /categories/{id}` - Get category details
- `PUT /categories/{id}` - Update category
- `DELETE /categories/{id}` - Delete category

#### Warehouse Management (Requires JWT + magasinier role)
- `GET /warehouses` - List user's warehouses
- `POST /warehouses` - Create warehouse
- `GET /warehouses/{id}` - Get warehouse details
- `PUT /warehouses/{id}` - Update warehouse

#### Stock Movement Management (Requires JWT + magasinier role)
- `GET /mouvements` - List all stock movements
- `POST /mouvements` - Create stock movement
- `GET /mouvements/{id}` - Get movement details
- `PUT /mouvements/{id}` - Update movement

## Authentication

All endpoints (except `/register` and `/login`) require JWT authentication.

### How to get a token:

1. Call `POST /login` with:
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

2. Copy the returned `token` value

3. For all subsequent API calls, add the Authorization header:
```
Authorization: Bearer <your_token_here>
```

In Swagger UI, click the "Authorize" button and paste your token.

## Role-Based Access

- **admin** - Full access to all endpoints
- **magasinier** (Warehouse Staff) - Access to warehouse, product, category, and movement endpoints
- **client** - Access to command endpoints only

## Updating Documentation

The Swagger documentation is defined in: `storage/api-docs/swagger.json`

To update the documentation:
1. Edit the JSON file directly
2. Refresh the browser (no rebuild needed)

For future automation, you can:
- Install `darkaonline/l5-swagger` package (add to require-dev in composer.json)
- Use PHP annotations in controllers to auto-generate docs

## Common HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## Example Request (using cURL)

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "role": "magasinier"
  }'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'

# Get Products (with token)
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer your_token_here"
```

## Next Steps

- Return to the main project to add pagination, filtering, and tests
- Consider adding webhook support for alerts
- Add rate limiting to prevent abuse
