# Employee CRUD Application - Tech Exercise

## The Test
• Given a CSV file [provided below]
• Make a simple web-based interface which:
    ◦ Accepts the CSV file
    ◦ Imports the CSV file into a database
    ◦ Displays the list of Employees
    ◦ allows the user to edit an Employee’s Email Address
    ◦ Shows the average salary of each company

### You must
• Use a relational Database, e.g., MySQL
• Use PHP
• NOT use any 3rd-party PHP libraries for the ORM layer, or CSV handling
• Submit in a new Github repository, and link to it in your email.

### You may
• use plain JavaScript, or any JS framework you’re comfortable with. Our techstack uses VUE

## About

A Clean Architecture implementation of an Employee CRUD system with DDD and SOLID principles.

## Features

- **CRUD Operations**: Create, Read, Update, Delete employees
- **CSV Upload**: Bulk import employees with duplicate detection and company auto-creation
- **Email Editing**: Inline email editing via AJAX with instant feedback
- **Company Management**: Separate Company domain with foreign key relationships
- **Company Analytics**: View average salaries by company
- **Command Pattern**: AddEmployee and AddCompany commands for data operations
- **Value Objects**: EmployeeId, CompanyId, Email, Money with validation
- **Clean Architecture**: Domain, Application, Infrastructure layers with DDD principles
- **Feedback Messages**: Flash messages and dynamic alerts for user operations
- **Authentication**: Optional API (token-based) authentication
- **Environment Configuration**: Configurable CSV upload limits via environment variables

## Setup

### Quick Start (Make Commands)
For convenience, use the provided Makefile commands:

```bash
make up          # Start all services
make down        # Stop all services
make build       # Build containers
make logs        # View application logs
make bash        # Access PHP container shell
```

### Manual Setup (Docker Commands)
Alternatively, use Docker commands directly:

1. Start services:
```bash
docker-compose up -d
```

All assets, caches and database seed are already built. This is all you need to run the application.

3. Access the application:
- Web: http://localhost:8000/employees
- MySQL: localhost:3306 (root/password)

_Check `Makefile` for any other commands, if you want to know them._

## CSV Format

```csv
Company Name,Employee Name,Email Address,Salary
ACME Corporation,John Doe,johndoe@acme.com,50000
Stark Industries,Tony Stark,tony@stark.com,100000
Wayne Enterprises,Bruce Wayne,bruce@wayneenterprises.com,90000
```

**Upload Features:**
- Automatic company creation if not exists
- Duplicate detection by name and email
- Validation errors reported per line
- Processing summary with counts

## Validation

- **EmployeeId**: Must be positive integer (required)
- **CompanyId**: Must be positive integer (required)
- **Email**: Valid email format with domain validation
- **Salary**: Non-negative number
- **CSV**: Max 1MB file size, proper format validation
- **Duplicates**: Prevents duplicate employees (same name + email)
- **Frontend**: Real-time validation with instant feedback
- **Backend**: Domain-level validation with value objects

## Database Schema

Check [_build-deploy/database/init.sql](./_build-deploy/database/init.sql).

## Routes

- `/employees` - List all employees with company names
- `/employees/create` - Create new employee
- `/employees/upload` - CSV upload with company auto-creation
- `/employees/companies` - Company salary averages
- `/employees/{id}/update-email` - AJAX email update
- `/employees/{id}/delete` - Delete employee with confirmation
- `/api/*` - API routes (optionally require X-API-TOKEN header)

## AWS CloudWatch Logging

The application uses `Bref\Monolog\CloudWatchFormatter` for AWS-optimized logging:

# Possible Improvements

- **Authentication**: User login, password reset, account deletion. Depending on app requirements. Can use OAuth2.
- **Authorization**: User roles, permissions, access control
- **Security**: CSRF, XSS
- **Tests**: Integration, Acceptance
- **Validation**: More robust rules (using a validator library, like symfony/validator)
- **Performance**: Caching
- **Deployment**: Docker, Kubernetes, Serverless
- **Monitoring**: Sentry, New Relic, DataDog
