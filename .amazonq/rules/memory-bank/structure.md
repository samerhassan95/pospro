# Super Admin - Project Structure

## Directory Organization

### Core Application Structure
```
app/
├── Console/           # Artisan commands and kernel
├── Events/           # Event classes for notifications and SMS
├── Exceptions/       # Custom exception handlers
├── Helpers/          # Utility classes and helper functions
├── Http/            # Controllers, middleware, and requests
├── Imports/         # Excel import classes for bulk operations
├── Jobs/            # Queue jobs for background processing
├── Library/         # Payment gateway integrations
├── Listeners/       # Event listeners for SMS and notifications
├── Mail/            # Email templates and mail classes
├── Models/          # Eloquent models and database entities
├── Notifications/   # Push notification classes
├── Providers/       # Service providers for dependency injection
├── Services/        # Business logic services (ZATCA, PDF, etc.)
└── Traits/          # Reusable trait classes
```

### Modular Architecture
```
Modules/
├── Business/              # Core business management module
├── CustomDomainAddon/     # White-label domain support
├── HrmAddon/             # Human resource management
├── MultiBranchAddon/     # Multi-branch operations
├── ThermalPrinterAddon/  # Thermal printer integration
└── WarehouseAddon/       # Advanced warehouse management
```

### Database & Migrations
```
database/
├── migrations/       # Database schema migrations (100+ files)
├── seeders/         # Data seeders for initial setup
├── factories/       # Model factories for testing
└── sql/            # Raw SQL scripts for updates
```

### Frontend Assets
```
public/
├── assets/          # CSS, JS, images, and plugins
├── modules/         # Module-specific frontend assets
├── uploads/         # User uploaded files and QR codes
└── flags/          # Country flag icons for localization
```

### Configuration & Localization
```
config/              # Laravel configuration files
lang/               # 50+ language translation files
resources/views/    # Blade templates organized by feature
```

## Core Components & Relationships

### Business Entity Model
- **Business**: Central entity for multi-tenancy
- **User**: Belongs to business with role-based permissions
- **Branch**: Multiple locations per business
- **Warehouse**: Inventory storage locations

### Product & Inventory System
- **Product**: Core product entity with variations
- **Category/Brand**: Product organization
- **Stock**: Real-time inventory tracking
- **Warehouse**: Multi-location inventory management

### Sales & Purchase Flow
- **Sale/Purchase**: Transaction entities
- **SaleDetails/PurchaseDetails**: Line items
- **Party**: Customers and suppliers
- **Payment**: Transaction payments and due tracking

### Subscription & Plans
- **Plan**: Feature-based subscription tiers
- **PlanSubscribe**: Business subscription management
- **Gateway**: Payment gateway configurations

## Architectural Patterns

### Multi-Tenancy
- Business-based data isolation
- Shared database with business_id foreign keys
- Middleware for business context switching

### Modular Design
- Laravel Modules package for extensibility
- Independent module routing and controllers
- Shared core models and services

### Event-Driven Architecture
- Events for SMS notifications and payments
- Listeners for background processing
- Queue jobs for heavy operations

### Service Layer Pattern
- Dedicated services for complex operations (ZATCA, PDF)
- Repository pattern for data access
- Trait-based code reuse

### API-First Approach
- RESTful APIs for all operations
- Sanctum authentication
- Module-specific API routes

## Key Integrations

### Payment Systems
- 15+ payment gateway integrations in app/Library/
- Unified payment interface across gateways
- Multi-currency support

### External Services
- SMS providers (Twilio, Kavenegar, etc.)
- Cloud storage (AWS S3, Google Cloud)
- Firebase for notifications
- ZATCA for Saudi B2B compliance

### Frontend Technologies
- Bootstrap-based responsive design
- jQuery for dynamic interactions
- Chart.js for analytics
- DataTables for data management