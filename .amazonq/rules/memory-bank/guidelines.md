# Super Admin - Development Guidelines

## Code Quality Standards

### PHP Coding Standards
- **PSR-4 Autoloading**: All classes follow PSR-4 namespace conventions
- **Strict Typing**: Use strict type declarations where appropriate
- **Method Visibility**: Explicit visibility modifiers (public, private, protected)
- **Camel Case**: Method names use camelCase (e.g., `getDashboardData`, `acnooFilter`)
- **Pascal Case**: Class names use PascalCase (e.g., `AcnooProductController`, `DashboardController`)

### Documentation Standards
- **DocBlocks**: Comprehensive PHPDoc comments for all public methods
- **Parameter Documentation**: Clear @var annotations for class properties
- **Return Types**: Explicit return type declarations
- **Inline Comments**: Descriptive comments for complex business logic

### Database Conventions
- **Snake Case**: Database columns use snake_case (e.g., `business_id`, `product_name`)
- **Foreign Keys**: Consistent naming pattern with `_id` suffix
- **Timestamps**: Standard Laravel `created_at` and `updated_at` columns
- **Soft Deletes**: Implemented where data retention is required

## Architectural Patterns

### Multi-Tenancy Implementation
```php
// Business context filtering in all queries
$products = Product::where('business_id', auth()->user()->business_id)->get();

// Middleware for business context
$this->middleware('check.permission:products.read');
```

### Repository Pattern Usage
- **Eloquent Models**: Direct model usage with relationship definitions
- **Query Scoping**: Business-specific data isolation
- **Eager Loading**: Consistent use of `with()` for relationship loading
- **Aggregate Queries**: Use of `withSum()`, `withCount()` for performance

### Service Layer Implementation
```php
// Database transactions for complex operations
DB::beginTransaction();
try {
    // Business logic here
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    return response()->json(['message' => __('Something went wrong.')], 406);
}
```

## Common Implementation Patterns

### Controller Structure
```php
class AcnooProductController extends Controller
{
    use HasUploader; // Trait for file upload functionality
    
    public function __construct()
    {
        // Permission middleware for all actions
        $this->middleware('check.permission:products.read')->only(['index', 'show']);
        $this->middleware('check.permission:products.create')->only(['create', 'store']);
    }
}
```

### Model Relationships
```php
// Consistent relationship definitions
public function business(): BelongsTo
{
    return $this->belongsTo(Business::class);
}

// Scoped relationships for business context
public function activeReservation()
{
    return $this->hasOne(TableReservation::class, 'table_id')
        ->where('status', 'reserved')
        ->latest();
}
```

### Validation Patterns
```php
$request->validate([
    'productName' => 'required|string|max:255',
    'productCode' => [
        'nullable',
        Rule::unique('products')->where(function ($query) {
            return $query->where('business_id', auth()->user()->business_id);
        }),
    ],
    'stocks.*.productStock' => 'nullable|numeric|min:0|max:99999999.99',
]);
```

### Response Patterns
```php
// Consistent JSON response structure
return response()->json([
    'message' => __('Product saved successfully.'),
    'redirect' => route('business.products.index')
]);

// Error responses with status codes
return response()->json([
    'message' => __('Something went wrong.'),
], 406);
```

## Frontend Development Standards

### JavaScript Conventions
- **Event Handling**: Use `addEventListener` for DOM events
- **Function Declarations**: Prefer function declarations over arrow functions for main handlers
- **Error Handling**: Consistent try-catch blocks for calculations
- **DOM Manipulation**: Direct DOM access with `getElementById` and `querySelector`

### AJAX Implementation
```javascript
// Consistent AJAX pattern for filtering
if (request.ajax()) {
    return response()->json([
        'data' => view('business::products.datas', compact('products'))->render()
    ]);
}
```

### CSS/SCSS Organization
- **Bootstrap Integration**: Leverage Bootstrap classes for responsive design
- **Custom Classes**: Prefix custom classes to avoid conflicts
- **Modular Styles**: Component-specific stylesheets in module directories

## Security Best Practices

### Authentication & Authorization
- **Sanctum Integration**: API authentication using Laravel Sanctum
- **Permission Middleware**: Granular permission checking on all routes
- **Business Context**: Automatic business_id filtering for data isolation

### Input Validation
- **Form Requests**: Comprehensive validation rules for all inputs
- **SQL Injection Prevention**: Use Eloquent ORM and parameter binding
- **File Upload Security**: Validate file types and sizes using HasUploader trait

### Data Protection
```php
// Sensitive data casting
protected $casts = [
    'meta' => 'json',
    'zatca_setting' => 'json',
    'moyasar_setting' => 'json'
];

// Mass assignment protection
protected $fillable = [
    'business_id', 'productName', 'productCode'
    // Explicit field listing
];
```

## Performance Optimization

### Database Optimization
- **Eager Loading**: Consistent use of `with()` to prevent N+1 queries
- **Pagination**: Standard pagination for large datasets (`paginate(5)`)
- **Indexing**: Foreign keys and frequently queried columns are indexed
- **Query Optimization**: Use of `select()` to limit returned columns

### Caching Strategy
```php
// Cache business-specific data
Cache::forget("plan-data-{$business->id}");

// Cache expensive calculations
$data = Cache::remember("dashboard-{$businessId}", 3600, function() {
    return $this->calculateDashboardData();
});
```

### File Management
- **Storage Facade**: Use Laravel Storage for file operations
- **Image Optimization**: Consistent image validation and processing
- **Cleanup**: Automatic file deletion when records are removed

## Module Development Standards

### Module Structure
```
Modules/ModuleName/
├── App/Http/Controllers/    # Module controllers
├── Database/migrations/     # Module-specific migrations
├── resources/views/        # Module templates
├── routes/                 # Module routes
└── composer.json          # Module dependencies
```

### API Development
- **RESTful Routes**: Follow REST conventions for all API endpoints
- **Resource Controllers**: Use Laravel resource controllers
- **API Versioning**: Maintain backward compatibility
- **Rate Limiting**: Implement appropriate rate limits

### Internationalization
- **Translation Keys**: Use `__()` helper for all user-facing strings
- **Language Files**: Maintain translations in JSON format
- **Pluralization**: Handle singular/plural forms correctly
- **RTL Support**: Consider right-to-left language support