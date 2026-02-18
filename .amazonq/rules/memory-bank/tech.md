# Super Admin - Technology Stack

## Core Framework
- **Laravel 10.41+**: PHP web application framework
- **PHP 8.1+**: Minimum PHP version requirement
- **MySQL/PostgreSQL**: Primary database support

## Backend Dependencies

### Laravel Ecosystem
- **laravel/sanctum**: API authentication
- **laravel/socialite**: Social login integration
- **laravel/tinker**: Interactive REPL
- **spatie/laravel-permission**: Role-based access control
- **nwidart/laravel-modules**: Modular architecture support

### Payment Integrations
- **stripe/stripe-php**: Stripe payment gateway
- **omnipay/paypal**: PayPal integration
- **razorpay/razorpay**: Razorpay payment system
- **mollie/mollie-api-php**: Mollie payments
- **karim007/laravel-bkash-tokenize**: bKash mobile payments
- **dipesh79/laravel-phonepe**: PhonePe integration
- **mercadopago/dx-php**: MercadoPago payments
- **paytm/paytmchecksum**: Paytm wallet integration

### Document & Report Generation
- **barryvdh/laravel-dompdf**: PDF generation
- **mpdf/mpdf**: Advanced PDF creation
- **maatwebsite/excel**: Excel import/export
- **phpoffice/phpspreadsheet**: Spreadsheet manipulation
- **simplesoftwareio/simple-qrcode**: QR code generation
- **ageekdev/laravel-barcode**: Barcode generation

### Communication Services
- **twilio/sdk**: SMS and communication
- **tzsk/sms**: Multi-provider SMS gateway
- **mediaburst/clockworksms**: SMS service
- **kavenegar/php**: Iranian SMS provider
- **melipayamak/php**: SMS gateway integration
- **smsgatewayme/client**: SMS gateway client

### Cloud & Storage
- **aws/aws-sdk-php**: Amazon Web Services integration
- **kreait/firebase-php**: Firebase services
- **guzzlehttp/guzzle**: HTTP client library

### Utility Libraries
- **hardevine/shoppingcart**: Shopping cart functionality
- **league/omnipay**: Payment abstraction layer
- **safiull/laravel-installer**: Application installer

## Frontend Technologies

### CSS Framework
- **Bootstrap 5**: Responsive CSS framework
- **Custom SCSS**: Project-specific styling
- **Font Awesome**: Icon library

### JavaScript Libraries
- **jQuery**: DOM manipulation and AJAX
- **Chart.js**: Data visualization
- **DataTables**: Advanced table functionality
- **Select2**: Enhanced select boxes
- **Moment.js**: Date manipulation
- **SweetAlert2**: Beautiful alerts and modals

### Build Tools
- **Vite**: Modern build tool and dev server
- **Laravel Mix**: Asset compilation (legacy support)
- **Node.js & NPM**: Package management

## Development Tools

### Code Quality
- **Laravel Pint**: PHP code style fixer
- **PHPUnit**: Testing framework
- **Laravel Breeze**: Authentication scaffolding
- **Spatie Laravel Ignition**: Error page enhancement

### Development Environment
- **Laravel Sail**: Docker development environment
- **Artisan**: Command-line interface
- **Tinker**: Interactive shell

## Database Technologies

### Primary Database
- **MySQL 8.0+**: Recommended database
- **PostgreSQL**: Alternative database support
- **SQLite**: Development and testing

### Migration System
- **100+ Migration Files**: Comprehensive schema management
- **Seeders**: Initial data population
- **Factories**: Test data generation

## Server Requirements

### Minimum Requirements
- **PHP 8.1+** with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **MySQL 5.7+** or **PostgreSQL 10+**
- **Composer**: Dependency management
- **Node.js 16+**: Frontend build tools

### Recommended Production Setup
- **Nginx/Apache**: Web server
- **Redis**: Caching and session storage
- **Supervisor**: Queue worker management
- **SSL Certificate**: HTTPS encryption

## Development Commands

### Installation
```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
```

### Development
```bash
php artisan serve          # Start development server
npm run dev               # Build assets for development
php artisan queue:work    # Process background jobs
php artisan tinker        # Interactive shell
```

### Production
```bash
npm run build            # Build assets for production
php artisan optimize     # Optimize application
php artisan config:cache # Cache configuration
```

## Module System
- **Laravel Modules**: Modular architecture support
- **Independent Routing**: Module-specific routes
- **Asset Management**: Per-module frontend assets
- **Database Migrations**: Module-specific migrations