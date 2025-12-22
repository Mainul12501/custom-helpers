# Setup Instructions

This guide will help you verify package metadata and prepare it for distribution.

## Step 1: Verify Namespace

The namespace is set to `Mainul\CustomHelperFunctions`. Verify there are no leftover placeholder namespaces.

### Files to Review:

1. **composer.json**
   - Line 2: `"name": "mainul/custom-helper-functions"`
   - Line 22-24: PSR-4 autoload namespace
   - Line 29-31: PSR-4 autoload-dev namespace
   - Line 35: Service Provider path
   - Line 38: Facade alias

2. **src/HelperServiceProvider.php**
   - Line 3: Namespace declaration

3. **src/Facades/ViewHelper.php**
   - Line 3: Namespace declaration
   - Line 16: Facade accessor class path

4. **src/Helpers/ViewHelper.php**
   - Line 3: Namespace declaration

5. **src/Http/Controllers/ExampleController.php**
   - Line 3: Namespace declaration
   - Line 6: ViewHelper import

### Quick Find & Replace:

Search for: `YourVendor`
Replace with: `Mainul\CustomHelperFunctions` if any remain

Search for: `yourvendor/helper-functions`
Replace with: `mainul/custom-helper-functions`

## Step 2: Update Author Information

In **composer.json**, verify:

```json
"authors": [
    {
        "name": "Mainul Islam",
        "email": "mainul12501@gmail.com"
    }
],
```

In **LICENSE**, update:
- Line 3: Copyright year and name

## Step 3: Customize Package Details

Update these files with your information:

1. **composer.json**
   - `description`: Customize the package description
   - `keywords`: Add relevant keywords for your package

2. **README.md**
   - Verify vendor-specific information
   - Add your support/contact details
   - Add your GitHub repository URL

## Step 4: Initialize Git Repository

```bash
git init
git add .
git commit -m "Initial commit"
```

## Step 5: Create GitHub Repository

1. Create a new repository on GitHub
2. Add remote and push:

```bash
git remote add origin https://github.com/Mainul12501/custom-helper-functions.git
git branch -M main
git push -u origin main
```

## Step 6: Prepare for Packagist (Optional)

If you want to publish to Packagist:

1. Create a release tag:
```bash
git tag v1.0.0
git push origin v1.0.0
```

2. Submit to Packagist:
   - Go to https://packagist.org/packages/submit
   - Enter your GitHub repository URL
   - Follow the submission process

## Step 7: Test the Package

### Test Locally

Create a test Laravel application and add this to composer.json:

```json
"repositories": [
    {
        "type": "path",
        "url": "../path-to-your-package"
    }
]
```

Then install:
```bash
composer require mainul/custom-helper-functions
```

### Test from GitHub

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Mainul12501/custom-helper-functions.git"
    }
]
```

## Step 8: Add Routes

Edit these files to add your custom routes:
- `src/routes/web.php` - For web routes
- `src/routes/api.php` - For API routes

Example in `src/routes/web.php`:

```php
use Illuminate\Support\Facades\Route;
use Mainul\CustomHelperFunctions\Http\Controllers\ExampleController;

Route::middleware(config('helper-functions.routes.web.middleware'))
    ->prefix(config('helper-functions.routes.web.prefix'))
    ->group(function () {
        Route::get('/example', [ExampleController::class, 'index']);
        // Add more routes here
    });
```

## Checklist

- [ ] Verify all namespaces
- [ ] Verify composer.json author info
- [ ] Update LICENSE file
- [ ] Verify README.md
- [ ] Add your routes to src/routes/
- [ ] Initialize Git repository
- [ ] Test package locally
- [ ] Create GitHub repository
- [ ] Tag first release
- [ ] (Optional) Submit to Packagist

## Need Help?

Refer to:
- [Laravel Package Development](https://laravel.com/docs/packages)
- [Composer Documentation](https://getcomposer.org/doc/)
- [Packagist Documentation](https://packagist.org/about)
