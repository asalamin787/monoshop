# MonoShop Route Documentation

## Overview
This document provides an overview of all routes available in the MonoShop e-commerce application.

## Route Structure

### 1. Web Routes (`routes/web.php`)
Standard web application routes for frontend views.

### 2. API Routes (`routes/api.php`)
Authenticated API routes requiring user login.
- **Prefix**: `/api`
- **Middleware**: `api`, `api.auth`

#### User Profile
- `GET /api/user` - Get current user profile
- `PUT /api/user/profile` - Update user profile

#### Products
- `GET /api/products` - List products (authenticated)
- `GET /api/products/{product}` - Get single product
- `GET /api/categories` - List categories
- `GET /api/categories/{category}/products` - Get category products

#### Orders
- `GET /api/orders` - Get user orders
- `GET /api/orders/{order}` - Get specific order
- `POST /api/orders` - Create new order

#### Addresses
- `GET /api/addresses` - Get user addresses
- `POST /api/addresses` - Create new address
- `PUT /api/addresses/{address}` - Update address
- `DELETE /api/addresses/{address}` - Delete address

#### Coupons
- `POST /api/coupons/validate` - Validate coupon code

### 3. Admin Routes (`routes/admin.php`)
Administrative interface routes.
- **Prefix**: `/admin`
- **Middleware**: `web`, `admin`

#### Analytics
- `GET /admin/analytics` - Dashboard statistics
- `GET /admin/analytics/sales` - Sales data
- `GET /admin/analytics/products` - Product analytics
- `GET /admin/analytics/users` - User analytics

#### Order Management
- `GET /admin/orders` - List all orders
- `GET /admin/orders/{order}` - View order details
- `PUT /admin/orders/{order}/status` - Update order status
- `PUT /admin/orders/{order}/payment-status` - Update payment status
- `POST /admin/orders/{order}/notes` - Add order notes

#### Product Management
- `GET /admin/products` - List products
- `POST /admin/products` - Create product
- `GET /admin/products/{product}` - View product
- `PUT /admin/products/{product}` - Update product
- `DELETE /admin/products/{product}` - Delete product
- `POST /admin/products/{product}/toggle-status` - Toggle active status
- `POST /admin/products/{product}/toggle-featured` - Toggle featured status

#### User Management
- `GET /admin/users` - List users
- `GET /admin/users/{user}` - View user details
- `PUT /admin/users/{user}/role` - Update user role
- `POST /admin/users/{user}/toggle-status` - Toggle user status
- `DELETE /admin/users/{user}` - Delete user

#### Category Management
- `GET /admin/categories` - List categories
- `POST /admin/categories` - Create category
- `PUT /admin/categories/{category}` - Update category
- `DELETE /admin/categories/{category}` - Delete category

#### Coupon Management
- `GET /admin/coupons` - List coupons
- `POST /admin/coupons` - Create coupon
- `PUT /admin/coupons/{coupon}` - Update coupon
- `POST /admin/coupons/{coupon}/toggle-status` - Toggle coupon status
- `DELETE /admin/coupons/{coupon}` - Delete coupon

#### Settings Management
- `GET /admin/settings` - Get all settings
- `PUT /admin/settings` - Update multiple settings
- `GET /admin/settings/{key}` - Get specific setting
- `PUT /admin/settings/{key}` - Update specific setting

#### System Management
- `POST /admin/system/cache/clear` - Clear application cache
- `POST /admin/system/maintenance/enable` - Enable maintenance mode
- `POST /admin/system/maintenance/disable` - Disable maintenance mode
- `GET /admin/system/info` - Get system information

### 4. Customer Routes (`routes/customer.php`)
Customer dashboard and account management.
- **Prefix**: `/customer`
- **Middleware**: `web`, `customer`

#### Dashboard
- `GET /customer` - Customer dashboard with statistics

#### Profile Management
- `GET /customer/profile` - Get profile
- `PUT /customer/profile` - Update profile
- `PUT /customer/profile/password` - Change password
- `DELETE /customer/profile` - Delete account

#### Address Management
- `GET /customer/addresses` - List addresses
- `POST /customer/addresses` - Create address
- `GET /customer/addresses/{address}` - View address
- `PUT /customer/addresses/{address}` - Update address
- `DELETE /customer/addresses/{address}` - Delete address
- `POST /customer/addresses/{address}/set-default` - Set default address

#### Order Management
- `GET /customer/orders` - List orders
- `GET /customer/orders/{order}` - View order details
- `POST /customer/orders/{order}/cancel` - Cancel order
- `POST /customer/orders/{order}/reorder` - Reorder items
- `GET /customer/orders/{order}/invoice` - Download invoice

#### Wishlist
- `GET /customer/wishlist` - View wishlist
- `POST /customer/wishlist/add/{product}` - Add to wishlist
- `DELETE /customer/wishlist/remove/{product}` - Remove from wishlist
- `DELETE /customer/wishlist/clear` - Clear wishlist

#### Order History
- `GET /customer/order-history` - Filtered order history
- `GET /customer/order-history/summary` - Order statistics

#### Notifications
- `GET /customer/notifications` - Get notifications
- `POST /customer/notifications/{id}/mark-read` - Mark as read
- `POST /customer/notifications/mark-all-read` - Mark all as read

### 5. Authentication Routes (`routes/auth.php`)
User authentication and account management.
- **Prefix**: `/auth`
- **Middleware**: `web`

#### Login/Logout
- `GET /auth/login` - Login form
- `POST /auth/login` - Process login
- `POST /auth/admin/login` - Admin login
- `POST /auth/logout` - Logout user

#### Registration
- `GET /auth/register` - Registration form
- `POST /auth/register` - Process registration

#### Password Reset
- `GET /auth/forgot-password` - Forgot password form
- `POST /auth/forgot-password` - Send reset link
- `GET /auth/reset-password/{token}` - Reset password form
- `POST /auth/reset-password` - Process password reset

#### Email Verification
- `GET /auth/email/verify` - Verification notice
- `GET /auth/email/verify/{id}/{hash}` - Verify email
- `POST /auth/email/verification-notification` - Resend verification

#### Social Login (Placeholder)
- `GET /auth/social/{provider}` - Redirect to social provider
- `GET /auth/social/{provider}/callback` - Handle callback

#### Security
- `GET /auth/security/sessions` - View active sessions
- `DELETE /auth/security/sessions/{sessionId}` - Revoke session
- `DELETE /auth/security/sessions` - Revoke all sessions
- `GET /auth/security/activity` - View account activity

#### API Authentication
- `POST /auth/api/login` - API login
- `POST /auth/api/logout` - API logout
- `POST /auth/api/refresh` - Refresh API token

### 6. Public API Routes (`routes/public-api.php`)
Public API routes that don't require authentication.
- **Prefix**: `/api/public`
- **Middleware**: `api`, `throttle:public-api`

#### Product Catalog
- `GET /api/public/products` - Browse products with filters
- `GET /api/public/products/{product}` - Product details
- `GET /api/public/categories` - List categories
- `GET /api/public/categories/{category}/products` - Category products
- `GET /api/public/featured-products` - Featured products
- `GET /api/public/latest-products` - Latest products
- `GET /api/public/popular-products` - Popular products

#### Search
- `GET /api/public/search/suggestions` - Search suggestions

#### Store Information
- `GET /api/public/offers` - Active offers
- `GET /api/public/sliders` - Homepage sliders
- `GET /api/public/settings` - Public store settings
- `GET /api/public/stats` - Store statistics

#### Customer Interaction
- `POST /api/public/contact` - Contact form submission
- `POST /api/public/newsletter/subscribe` - Newsletter signup
- `POST /api/public/newsletter/unsubscribe` - Newsletter unsubscribe

#### Product Utilities
- `GET /api/public/products/{product}/availability` - Check availability
- `POST /api/public/products/price-check` - Bulk price check

## Middleware Groups

### Custom Middleware
- **admin**: Ensures user has admin role
- **customer**: Ensures user has customer role
- **role**: Check specific role permissions
- **guest.only**: Only allow unauthenticated users
- **api.auth**: API authentication using Sanctum
- **maintenance**: Check maintenance mode

### Rate Limiting
- **api**: 60 requests per minute
- **auth**: 5 requests per minute (login attempts)
- **admin**: 120 requests per minute
- **public-api**: 30 requests per minute

## Authentication

### Web Authentication
Uses Laravel's built-in session-based authentication.

### API Authentication
Uses Laravel Sanctum for token-based authentication.

### Role-Based Access Control
- **Admin**: Full system access
- **Customer**: Customer area access only
- **Guest**: Public areas only

## Error Handling

All routes include proper error handling with appropriate HTTP status codes:
- 200: Success
- 201: Created
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 429: Too Many Requests
- 500: Server Error

## Response Format

All API routes return JSON responses with consistent structure:

```json
{
  "message": "Success message",
  "data": {},
  "errors": {},
  "meta": {}
}
```

## Rate Limiting

Rate limiting is implemented to prevent abuse:
- Public API: 30 requests/minute per IP
- Authenticated API: 60 requests/minute per user
- Auth endpoints: 5 requests/minute per IP
- Admin endpoints: 120 requests/minute per user

## Security Features

- CSRF protection on web routes
- Throttling on sensitive endpoints
- Role-based access control
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- Secure password hashing
- Token-based API authentication

## Usage Examples

### Create Order (API)
```bash
curl -X POST /api/orders \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "items": [
      {"product_id": 1, "quantity": 2}
    ],
    "address_id": 1,
    "payment_method": "card"
  }'
```

### Get Products (Public API)
```bash
curl -X GET "/api/public/products?search=laptop&category=electronics&min_price=100"
```

### Admin Login
```bash
curl -X POST /auth/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

This comprehensive routing system provides a solid foundation for the MonoShop e-commerce application with proper separation of concerns, security, and scalability.
