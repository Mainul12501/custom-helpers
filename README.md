# Helper Functions Package for Laravel

A Laravel package providing comprehensive helper functions for handling API and web responses, authentication, code generation, date formatting, file uploads, and more.

## Requirements

- PHP >= 8.2
- Laravel 11.x or 12.x

## Installation

### Step 1: Install via Composer

```bash
composer require mainul/custom-helpers
```

### Step 2: Publish Configuration (Optional)

Publish the configuration file to customize package settings:

```bash
php artisan vendor:publish --tag=helper-functions-config
```

This will create a `config/helper-functions.php` file where you can customize:
- Route settings (prefix, middleware)
- Default error messages
- API detection patterns
- And more

### Step 3: Publish Routes (Optional)

If you want to customize the routes, publish the route files:

```bash
php artisan vendor:publish --tag=helper-functions-routes
```

This will create:
- `routes/helper-functions-web.php`
- `routes/helper-functions-api.php`

You can then update the routes in these files and disable the package routes in `config/helper-functions.php`:

```php
'routes' => [
    'web' => [
        'enabled' => false, // Disable package web routes
    ],
    'api' => [
        'enabled' => false, // Disable package API routes
    ],
],
```

## Configuration

After publishing the config file, you can customize the package in `config/helper-functions.php`:

### Routes Configuration

```php
'routes' => [
    'web' => [
        'enabled' => true,              // Enable/disable web routes
        'prefix' => null,               // Route prefix
        'middleware' => ['web'],        // Middleware groups
    ],
    'api' => [
        'enabled' => true,              // Enable/disable API routes
        'prefix' => 'api',              // Route prefix
        'middleware' => ['api'],        // Middleware groups
    ],
],
```

### CustomHelper Configuration

```php
'custom_helper' => [
    'default_error_message' => 'Something went wrong. Please try again.',
    'no_data_message' => 'No Data found.',
    'default_success_message' => 'Operation completed successfully.',
    'api_guard' => 'sanctum',           // Auth guard for API
    'default_guard' => null,            // Default auth guard
],
```

## Usage

### Using the CustomHelper Class

```php
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

// Return data for web or API
return CustomHelper::returnDataForWebOrApi($data, 'users.index', $errorMessage);

// Return success message
return CustomHelper::returnSuccessMessage('User created successfully');

// Check authentication
if (CustomHelper::authCheck()) {
    $user = CustomHelper::loggedUser();
}

// Generate code (OTP)
$otp = CustomHelper::generateCode(6, 'number');

// Get session code
$sessionCode = CustomHelper::getSessionCode('mobile_123');
```

## Available Methods

### Response Methods

| Method | Description |
|--------|-------------|
| `returnDataForWebOrApi($data, $viewPath, $jsonErrorMessage, $isForRender, $isReturnBack, $successMsg)` | Return JSON for API/AJAX or view for web requests |
| `returErrorMessage($message, $customMsg)` | Return error response for API or redirect back with error for web |
| `returnSuccessMessage($message)` | Return success response for API or redirect back with success for web |
| `returnRedirectWithMessage($route, $messageType, $message)` | Redirect to route with message (handles API/AJAX/web) |

### Request Detection Methods

| Method | Description |
|--------|-------------|
| `isApiRequest()` | Check if current request is an API request |
| `isAjax()` | Check if current request is an AJAX request |
| `wantsJsonResponse()` | Check if request wants JSON response (API or AJAX) |

### Authentication Methods

| Method | Description |
|--------|-------------|
| `authCheck()` | Check if user is authenticated (uses sanctum for API, default guard for web) |
| `loggedUser()` | Get the currently logged-in user |

### Code Generation Methods

| Method | Description |
|--------|-------------|
| `generateCode($length, $type)` | Generate code - supports `number`, `alpha`, or `random` (alphanumeric) |
| `generateSessionCode($length, $type, $sessionKey)` | Generate and store code in session/cache |
| `getSessionCode($sessionKey)` | Retrieve generated code from session/cache |

### Date & Time Methods

| Method | Description |
|--------|-------------|
| `showDate($date)` | Format date as `d-m-Y` (e.g., 25-12-2024) |
| `showTime($date)` | Format time as `g:i A` (e.g., 3:30 PM) |
| `showDateTime($date)` | Format as `d-m-Y g:i A` (e.g., 25-12-2024 3:30 PM) |
| `showDateTime24Hours($date)` | Format as `d-m-Y H:i` (e.g., 25-12-2024 15:30) |
| `showDateForBlogType($date)` | Format as `F d, Y` (e.g., December 25, 2024) |
| `dateWithTime($date)` | Format as `Y-m-d H:i` |
| `currentDateWithTime()` | Get current date and time as `Y-m-d H:i` |
| `getDurationAmongTwoDates($startDate, $endDate, $durationUnit, $isEndDateIsCurrentDate)` | Calculate duration between dates (years/months/days) |
| `differTime($start, $end, $info)` | Get human-readable time difference |

### File Methods

| Method | Description |
|--------|-------------|
| `getFileExtension($file)` | Get file extension |
| `getFileType($file)` | Get file MIME type |
| `fileUpload($fileObject, $directory, $nameString, $modelFileUrl)` | Upload file to specified directory |
| `fileUploadByBase64($base64String, $imageDirectory, $imageNameString, $modelFileUrl)` | Upload file from base64 string |

### Artisan Command Methods

| Method | Description |
|--------|-------------|
| `startQueueWorkManuallyByArtisanCommand()` | Manually process the queue |
| `clearRouteCache()` | Clear route cache |
| `CacheRoute()` | Cache routes |
| `optimizeClear()` | Clear all optimizations |
| `clearCache()` | Clear application cache |

### Controller Methods (CustomHelperController)

| Route Method | Description |
|--------------|-------------|
| `symlink()` | Create storage symbolic link |
| `optimizeReset()` | Clear all optimizations and return output |
| `phpinfo()` | Display PHP information |

## Adding Custom Routes

### Option 1: Edit Package Routes Directly (Not Recommended)

Edit `src/routes/web.php` or `src/routes/api.php`:

```php
use Illuminate\Support\Facades\Route;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

Route::get('/your-route', function () {
    // Your logic here
});
```

### Option 2: Publish and Customize Routes (Recommended)

1. Publish routes:
```bash
php artisan vendor:publish --tag=helper-functions-routes
```

2. Edit published routes in `routes/helper-functions-web.php` or `routes/helper-functions-api.php`

3. Disable package routes in `config/helper-functions.php`:
```php
'routes' => [
    'web' => ['enabled' => false],
    'api' => ['enabled' => false],
],
```

4. Load your custom routes in `app/Providers/AppServiceProvider.php`:
```php
public function boot()
{
    $this->loadRoutesFrom(base_path('routes/helper-functions-web.php'));
    $this->loadRoutesFrom(base_path('routes/helper-functions-api.php'));
}
```

## Optional Dependencies

The package works with these optional packages:
- `brian2694/laravel-toastr` - For flash messages (optional)

If this package is not installed, the package will skip the related functionality.

## License

MIT

## Support

For issues and feature requests, please use the GitHub issue tracker.
