# API Documentation - Speak Lab Project

## 🔐 Authentication với Laravel Sanctum

Dự án này sử dụng Laravel Sanctum để xác thực API. Sanctum cung cấp một hệ thống token đơn giản để xác thực các request API.

## 📋 Các Endpoint API

### Public Routes (Không cần xác thực)

#### 1. Health Check

```http
GET /api/health
```

**Response:**

```json
{
    "status": "ok",
    "message": "API is running",
    "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

#### 2. Đăng ký User

```http
POST /api/register
```

**Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2024-01-01T12:00:00.000000Z",
            "updated_at": "2024-01-01T12:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    },
    "message": "User registered successfully"
}
```

#### 3. Đăng nhập

```http
POST /api/login
```

**Body:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "token": "2|def456...",
        "token_type": "Bearer"
    },
    "message": "Login successful"
}
```

### Protected Routes (Cần xác thực)

Tất cả các route bên dưới cần header `Authorization: Bearer {token}`

#### 4. Lấy thông tin user hiện tại

```http
GET /api/user
```

**Headers:**

```
Authorization: Bearer {token}
```

#### 5. Đăng xuất

```http
POST /api/logout
```

**Headers:**

```
Authorization: Bearer {token}
```

#### 6. Lấy danh sách users

```http
GET /api/users
```

#### 7. Lấy thông tin user theo ID

```http
GET /api/users/{id}
```

#### 8. Tạo user mới

```http
POST /api/users
```

**Body:**

```json
{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "password123"
}
```

#### 9. Cập nhật user

```http
PUT /api/users/{id}
```

**Body:**

```json
{
    "name": "Jane Smith",
    "email": "jane.smith@example.com"
}
```

#### 10. Xóa user

```http
DELETE /api/users/{id}
```

## 🔧 Cách sử dụng

### 1. Đăng ký/Đăng nhập

```bash
# Đăng ký
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Đăng nhập
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### 2. Sử dụng token để truy cập protected routes

```bash
# Lấy thông tin user
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Lấy danh sách users
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## 🛡️ Middleware Group Sanctum

Dự án sử dụng middleware group `auth:sanctum` để bảo vệ các route:

```php
Route::middleware('auth:sanctum')->group(function () {
    // Tất cả routes trong group này cần xác thực
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // User management routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
});
```

## 📝 Response Format

Tất cả API responses đều có format chuẩn:

**Success Response:**

```json
{
    "success": true,
    "data": { ... },
    "message": "Operation successful"
}
```

**Error Response:**

```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... } // Chỉ có khi validation error
}
```

## 🚀 Chạy dự án

```bash
# Cài đặt dependencies
composer install

# Chạy migrations
php artisan migrate

# Chạy server
php artisan serve
```

API sẽ có sẵn tại: `http://localhost:8000/api`
