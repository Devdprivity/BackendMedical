# 🏥 Medical Management System - Backend API

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL">
  <img src="https://img.shields.io/badge/API-REST-green?style=for-the-badge" alt="REST API">
</p>

<p align="center">
  <strong>Comprehensive Healthcare Management System with RESTful API</strong><br>
  Built with Laravel for scalability, security, and performance.
</p>

## 🌟 Overview

The Medical Management System is a comprehensive healthcare management platform designed to streamline medical operations. It provides a robust RESTful API for managing patients, doctors, appointments, surgeries, medications, and billing processes.

**🔗 Live Demo:** [https://backendmedical-main-kqne9d.laravel.cloud/](https://backendmedical-main-kqne9d.laravel.cloud/)

## ✨ Features

### 👨‍⚕️ **Doctor Management**
- Complete CRUD operations for medical professionals
- Specialty tracking and credentials management
- Schedule management and availability
- Performance analytics and reporting

### 👥 **Patient Registry**
- Comprehensive patient information management
- Medical history and vital signs tracking
- Insurance and emergency contact data
- Demographic information and preferences

### 📅 **Appointment System**
- Advanced scheduling with conflict detection
- Automated reminders and notifications
- Status management and workflow automation
- Calendar integration and availability checking

### 🏥 **Surgery Management**
- Surgical procedure scheduling and tracking
- Operating room management
- Pre and post-operative care coordination
- Complications and outcome monitoring

### 💊 **Pharmacy System**
- Medication inventory management
- Stock alerts and expiration monitoring
- Prescription tracking and fulfillment
- Movement history and audit trails

### 💰 **Billing & Invoicing**
- Comprehensive billing system
- Insurance processing and claims management
- Payment tracking and financial reporting
- Itemized billing and tax calculations

## 🚀 Quick Start

### Prerequisites

- **PHP 8.1+**
- **Composer**
- **PostgreSQL**
- **Laravel 10.x**
- **Node.js & NPM** (for frontend assets)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/BackendMedical.git
   cd BackendMedical
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   ```bash
   # Update .env with your database credentials
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=medical_system
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## 📚 API Documentation

### Authentication

The API uses Laravel Sanctum for authentication. All endpoints require a valid Bearer token except for login and registration.

```bash
# Login
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

### Core Endpoints

| Module | Endpoint | Description |
|--------|----------|-------------|
| **Doctors** | `GET /api/doctors` | List all doctors |
| **Patients** | `GET /api/patients` | List all patients |
| **Appointments** | `GET /api/appointments` | List all appointments |
| **Surgeries** | `GET /api/surgeries` | List all surgeries |
| **Medications** | `GET /api/medications` | List all medications |
| **Invoices** | `GET /api/invoices` | List all invoices |

**📖 Complete API Documentation:** Available at `/` (interactive documentation with real-time testing)

## 🗄️ Database Schema

The system includes the following main entities:

- **Users** - System users and authentication
- **Clinics** - Medical facilities and locations
- **Doctors** - Medical professionals and specialties
- **Patients** - Patient information and medical history
- **Appointments** - Scheduling and appointment management
- **Surgeries** - Surgical procedures and outcomes
- **Medical Exams** - Laboratory tests and results
- **Medications** - Pharmacy inventory and prescriptions
- **Invoices** - Billing and payment management
- **Vital Signs** - Patient monitoring data

## 🔧 Configuration

### Environment Variables

```env
# Application
APP_NAME="Medical Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=medical_system

# Authentication
SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com
SESSION_DOMAIN=.your-domain.com
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Generate coverage report
php artisan test --coverage
```

## 📊 Seeders & Sample Data

The system includes comprehensive seeders with realistic medical data:

```bash
# Seed all data
php artisan db:seed

# Seed specific modules
php artisan db:seed --class=DoctorUserSeeder
php artisan db:seed --class=PatientSeeder
php artisan db:seed --class=AppointmentSeeder
```

**Sample Credentials:**
- **Email:** `admin@example.com`
- **Password:** `password`

## 🚀 Deployment

### Laravel Cloud

This project is configured for deployment on Laravel Cloud with automatic CI/CD.

```bash
# Deploy to production
git push origin main
```

### Manual Deployment

```bash
# Production optimization
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔒 Security Features

- **Authentication:** Laravel Sanctum with token-based auth
- **Authorization:** Role-based access control (RBAC)
- **Data Validation:** Comprehensive input validation
- **SQL Injection Protection:** Eloquent ORM with prepared statements
- **CSRF Protection:** Built-in Laravel CSRF protection
- **Rate Limiting:** API rate limiting by user role
- **Encryption:** Sensitive data encryption at rest

## 📈 Performance

- **Database Optimization:** Indexed queries and optimized relationships
- **Caching:** Redis/Memcached support for sessions and cache
- **API Pagination:** Efficient pagination for large datasets
- **Eager Loading:** Optimized database queries
- **Response Compression:** Gzip compression enabled

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards
- Write comprehensive tests for new features
- Update documentation for API changes
- Use meaningful commit messages

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Team

- **Lead Developer:** [Your Name]
- **Backend API:** Laravel 10.x
- **Database:** PostgreSQL
- **Deployment:** Laravel Cloud

## 📞 Support

- **Documentation:** [API Documentation](https://backendmedical-main-kqne9d.laravel.cloud/)
- **Issues:** [GitHub Issues](https://github.com/yourusername/BackendMedical/issues)
- **Email:** support@medicalsystem.com

---

<p align="center">
  <strong>Built with ❤️ using Laravel</strong><br>
  Medical Management System © 2024
</p>
