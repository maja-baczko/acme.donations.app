# ACME Corp Donation Platform

A comprehensive employee donation platform built with Laravel 11, Vue 3, and PostgreSQL, enabling corporate employees to create fundraising campaigns and contribute to causes they care about.

## 🚀 Features

### Employee Features
- **Campaign Management**: Create, edit, and manage fundraising campaigns
- **Campaign Discovery**: Search and filter campaigns by category, status, and progress
- **Donation History**: Track personal donation history and download receipts
- **Anonymous Donations**: Option to donate anonymously

### Admin Features
- **Dashboard**: Comprehensive analytics and statistics
- **User Management**: Manage employee accounts and permissions
- **Settings Management**: Configure platform parameters and payment providers
- **Audit Logs**: Track all platform activities

### Admin Features
- **Campaign Management**: Create campaigns
- **Reports**: Generate detailed reports on campaigns and donations

## 🛠️ Tech Stack

### Backend
- **PHP 8.3+** with strict types
- **Laravel 12** (latest version)
- **PostgreSQL 16+** for data persistence
- **Composer** for dependency management
- **Pest PHP** for testing
- **PHPStan Level 8** for static analysis
- **Laravel Sanctum** for API authentication

### Frontend
- **Vue 3** (Composition API) with TypeScript
- **Vite** for fast development and building
- **Vue Router** for navigation
- **Pinia** for state management
- **Axios** for API communication

## 📋 Prerequisites

- PHP >= 8.3
- Composer >= 2.6
- Node.js >= 20.x
- PostgreSQL >= 16
- Redis (for caching and queues)

## 🔧 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/maja-baczko/acme.donations.app.git
cd donation-platform
```

### 2. Backend Setup

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Configure database in .env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=acme_donations
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database with initial data
php artisan db:seed

# Create storage link
php artisan storage:link
```

### 3. Frontend Setup

```bash
cd frontend

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Configure API URL
VITE_API_BASE_URL=http://localhost:8000/api/v1

# Start development server
npm run dev
```

### 4. Start the Application

```bash
# In backend directory
php artisan serve

# In frontend directory (separate terminal)
npm run dev
```

The application will be available at:
- Frontend: http://localhost:5173
- Backend API: http://localhost:8000

## 🧪 Testing

### Backend Tests

```bash
# Run all tests
 docker compose exec backend ./vendor/bin/pest
```

## Documents

The application follows Domain-Driven Design (DDD) principles with CQRS:

```
documents
└──  analysis
        ├── Enhancements → planned next steps
        ├── Implementation → implemented features
        └── Security & Validation
└── documentation
        ├── Routes → list of the routes
        └── Routes_testing → comprehensive guide on how to test routes in Postman
