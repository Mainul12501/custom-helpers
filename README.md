# Helper Functions Package for Laravel

A Laravel package providing comprehensive helper functions for handling API and web responses, authentication, OTP generation, and more.

## Requirements

- PHP >= 8.2
- Laravel 11.x or 12.x

## Installation

### Step 1: Install via Composer

```bash
composer require mainul/custom-helper-functions
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
- OTP configuration
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

## Package Structure

```
helper-functions/
├── src/
│   ├── config/
│   │   └── helper-functions.php       # Configuration file
│   ├── Facades/
│   │   └── ViewHelper.php             # Facade for ViewHelper
│   ├── Helpers/
│   │   └── ViewHelper.php             # Main helper class
│   ├── Http/
│   │   └── Controllers/               # Controllers directory (for your custom controllers)
│   ├── routes/
│   │   ├── api.php                    # API routes
│   │   └── web.php                    # Web routes
│   └── HelperServiceProvider.php      # Service provider
├── composer.json
└── README.md
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

### ViewHelper Configuration

```php
'view_helper' => [
    'default_error_message' => 'Something went wrong. Please try again.',
    'no_data_message' => 'No Data found.',
    'default_success_message' => 'Operation completed successfully.',
    'api_patterns' => ['/api/'],        // Patterns to detect API requests
    'api_guard' => 'sanctum',           // Auth guard for API
    'default_guard' => null,            // Default auth guard
],
```

### OTP Configuration

```php
'otp' => [
    'length' => 4,
    'min' => 1000,
    'max' => 9999,
    'expiry_minutes' => 5,
],
```

## Usage

### Using the ViewHelper Class

You can use the ViewHelper class in two ways:

#### 1. Direct Class Usage

```php
use Mainul\CustomHelperFunctions\Helpers\ViewHelper;

// Check view for API
return ViewHelper::checkViewForApi($data, 'users.index', $errorMessage);

// Return success message
return ViewHelper::returnSuccessMessage('User created successfully');

// Check authentication
if (ViewHelper::authCheck()) {
    $user = ViewHelper::loggedUser();
}

// Generate OTP
$otp = ViewHelper::generateOtp($mobile);

// Get session OTP
$sessionOtp = ViewHelper::getSessionOtp($mobile);
```

#### 2. Using Facade

```php
use Mainul\CustomHelperFunctions\Facades\ViewHelper;

return ViewHelper::checkViewForApi($data, 'users.index');
```

### Available Methods

#### Response Methods

- `checkViewForApi($data, $viewPath, $jsonErrorMessage)` - Return JSON for API or view for web
- `returnBackViewAndSendDataForApiAndAjax($data, $viewPath, $jsonErrorMessage, $successMessage, $isReturnBack)` - Handle API/AJAX/web responses
- `returnDataForAjaxAndApi($data)` - Return data for AJAX and API
- `returnExceptionError($message)` - Return exception error response
- `returnRedirectWithMessage($route, $messageType, $message)` - Redirect with message
- `returnResponseFromPostRequest($status, $message)` - Return response from POST request
- `returnSuccessMessage($message)` - Return success message

#### Authentication Methods

- `authCheck()` - Check if user is authenticated
- `loggedUser()` - Get logged-in user
- `checkIfUserApprovedOrBlocked($user)` - Check user approval status

#### OTP Methods

- `generateOtp($mobile)` - Generate OTP
- `getSessionOtp($mobile)` - Get session OTP

#### Utility Methods

- `checkIfRequestFromApi()` - Check if request is from API
- `getDurationAmongTwoDates($startDate, $endDate, $durationUnit, $isEndDateIsCurrentDate)` - Calculate duration between dates
- `saveImagePathInJson($imageFileObject, $imageDirectory, $imageNameString, $width, $height, $previousJsonString)` - Save image paths in JSON

## Adding Custom Routes

### Option 1: Edit Package Routes Directly (Not Recommended)

Edit `src/routes/web.php` or `src/routes/api.php`:

```php
use Illuminate\Support\Facades\Route;
use Mainul\CustomHelperFunctions\Helpers\ViewHelper;

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

## Customization

### Adding Custom Helpers

Create your own helper classes in the `src/Helpers` directory and register them in the service provider.

### Extending ViewHelper

You can extend the ViewHelper class to add your own methods:

```php
namespace App\Helpers;

use Mainul\CustomHelperFunctions\Helpers\ViewHelper as BaseViewHelper;

class ViewHelper extends BaseViewHelper
{
    public static function yourCustomMethod()
    {
        // Your custom logic
    }
}
```

## Optional Dependencies

The package works with these optional packages:
- `brian2694/laravel-toastr` - For flash messages (optional)
- `xenon/laravel-bd-sms` - For SMS functionality (optional)

If these packages are not installed, the package will skip the related functionality.

## License

MIT

## Support

For issues and feature requests, please use the GitHub issue tracker.
