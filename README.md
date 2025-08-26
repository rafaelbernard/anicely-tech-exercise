# Engineering Tech Exercise

A Clean Architecture implementation of the [Technical Exercise detailed at ./test-requirements.md](./test-requirements.md).

## Architecture Overview

The application follows **Clean Architecture** principles with **Domain-Driven Design (DDD)** patterns, implementing a employee management system with the following layers:

### Domain Layer (Business Logic)
- **Employee Domain**: Core business entities with rich domain modeling
- **Company Domain**: Separate bounded context for company management
- **Value Objects**: Email, Money, EmployeeId, CompanyId with built-in validation
- **Domain Services**: Business rule enforcement and domain logic

### Application Layer (Use Cases)
- **CreateEmployee**: Handle employee creation with validation
- **UpdateEmployeeEmail**: Manage email updates with domain constraints
- **UploadEmployeesFromCsv**: Bulk import with company auto-creation

### Infrastructure Layer (Data & External Systems)
- **Repository Pattern**: DBAL-based repositories for data persistence
- **Database**: MySQL 8.0+ with proper schema design
- **Logging**: AWS CloudWatch integration for production monitoring

## Quick Start

### Prerequisites
All pre-requisites are included in the Docker setup.

- **PHP 8.4+** (matches existing implementation)
- **Docker & Docker Compose**
- **Node.js 18+** (for frontend assets)
- **MySQL 8.0+**

### Installation Commands

```bash
# Clone and navigate
git clone https://github.com/rafaelbernard/anicely-tech-exercise
cd anicely-tech-exercise

# Start services (all-in-one command)
make up

# Alternative: Manual Docker setup
docker-compose up
```

It will run non-detached mode, so you will see the logs in the terminal preparing app, database, etc. 
Wait to see something like this:
```bash
app-1    | [Mon Aug 25 23:33:24 2025] PHP 8.4.11 Development Server (http://0.0.0.0:8000) started
```

Access the application
```bash
open http://localhost:8000/employees
```

**That's it!** The application includes pre-built assets, database seeds, and all necessary configurations.

## Technology Stack

### Backend Technologies
- **Symfony 7.3**: Modern PHP framework with robust architecture
- **Doctrine DBAL**: Database abstraction without heavy ORM overhead
- **Bref Framework**: Serverless deployment on AWS Lambda and other AWS readiness services
- **Monolog**: Structured logging with CloudWatch formatter

### Frontend Technologies
- **Webpack Encore**: Asset compilation and optimization
- **Stimulus**: Progressive enhancement with minimal JavaScript
- **Bootstrap**: Responsive UI framework via CDN
- **AJAX**: Dynamic email editing without page reloads

### Development Tools
- **Docker**: Containerized development environment
- **Make**: Automated build and deployment commands
- **Symfony Flex**: Automated package configuration

## AWS Deployment Ready

### Serverless Configuration (or other AWS deployment)
- **Bref Integration**: Ready for AWS deployment
- **CloudWatch Logging**: Structured JSON logs for monitoring
- **Symfony Bridge**: Optimized for serverless environments

### Production Features
- **Environment Configuration**: Proper .env setup for different environments
- **Asset Optimization**: Webpack Encore with production builds
- **Security**: API-token validation, input validation, secure headers

## Frontend Architecture

### Stimulus Controllers
- **Employee List Controller**: AJAX email editing with real-time feedback
- **CSV Upload Controller**: Client-side validation and progress tracking
- **Employee Form Controller**: Dynamic form validation

### Asset Pipeline
```javascript
// Webpack Encore configuration
.addEntry('app', './assets/app.js')
.enableStimulusBridge('./assets/controllers.json')
.enableSassLoader()
.enableVersioning()
```

## Features Implemented

### Core Functionality
- ✅ **CRUD Operations**: Complete employee management
- ✅ **CSV Import**: Bulk upload with validation
- ✅ **Company Analytics**: Average salary calculations
- ✅ **AJAX Interface**: Dynamic email editing
- ✅ **Responsive Design**: Mobile-friendly interface

### Advanced Features
- ✅ **Domain Validation**: Value objects with business rules
- ✅ **Error Handling**: Comprehensive error reporting
- ✅ **Flash Messages**: User feedback system
- ✅ **API Endpoints**: RESTful API with token authentication (can be enabled/disabled)
- ✅ **Logging**: CloudWatch integration for monitoring

## Validation & Testing

### Domain Validation
- **Email Format**: RFC-compliant email validation
- **Salary Constraints**: Non-negative money values
- **ID Validation**: Positive integer constraints
- **Duplicate Prevention**: Business rule enforcement

### File Upload Validation
- **File Type**: CSV format validation
- **File Size**: Configurable size limits
- **Content Validation**: Proper CSV structure checking
- **Encoding**: UTF-8 encoding support

## Development Commands

```bash
# Container management
make up              # Start all services
make down            # Stop all services  
make build           # Rebuild containers
make logs            # View application logs
make bash            # Access PHP container

# Asset compilation (inside the container)
npm run dev          # Development build
npm run watch        # Watch for changes
npm run build        # Production build

# Unit tests (inside the container)
composer run tests

# Time: 00:00.798, Memory: 24.00 MB
# OK (77 tests, 198 assertions)

cat coverage/unit/coverage.txt  # View test coverage summary

# Database operations (inside the container)
bin/console cache:clear                  # Clear Symfony cache
```

## Next Steps for Development

### Recommended Improvements
1. **Integration Tests**: Test controllers, repositories and any other class that needs infrastructure
1. **Authentication**: User login, password reset, account deletion. Depending on app requirements. Can use OAuth2.
1. **Authorization**: User roles, permissions, access control
1. **Security**: CSRF, XSS, Validation (eg: symfony/validator)
1. **Front-End framework**: Vue.js, React, Angular
1. **Caching Layer**: Implement Redis for performance
1. **Performance Monitoring**: Add APM integration (Sentry, New Relic, DataDog)
1. **CI/CD**: Docker, Kubernetes, Serverless

