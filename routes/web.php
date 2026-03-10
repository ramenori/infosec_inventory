<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeploymentController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SuppliersController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\ContactPersonController;
use App\Http\Controllers\Admin\AccountsController;

Route::redirect('/', '/admin/login');

Route::prefix('admin')->group(function () {

    // Public Login Routes
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminLoginController::class, 'login'])->name('admin.login.submit');

    // Protected Routes (Require Authentication)
    Route::middleware('auth')->group(function () {
        
        // Logout Route
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
        
        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Inventory Routes
        Route::prefix('inventory')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('admin.inventory');
            Route::get('/create', [InventoryController::class, 'create'])->name('admin.inventory.create');
            Route::post('/', [InventoryController::class, 'store'])->name('admin.inventory.store');
            Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('admin.inventory.edit');
            Route::put('/{id}', [InventoryController::class, 'update'])->name('admin.inventory.update');
            Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('admin.inventory.destroy');
        });

        // Category Routes
        Route::prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('admin.category');
            Route::post('/', [CategoryController::class, 'store'])->name('admin.category.store');
            Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('admin.category.edit');
            Route::put('/{id}', [CategoryController::class, 'update'])->name('admin.category.update');
            Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('admin.category.destroy');
        });

        // Deployment Routes
        Route::prefix('deployment')->group(function () {
            Route::get('/', [DeploymentController::class, 'index'])->name('admin.deployment');
            Route::post('/select-category', [DeploymentController::class, 'selectCategory'])->name('admin.deployment.selectCategory');
            Route::get('/clear-category', [DeploymentController::class, 'clearCategory'])->name('admin.deployment.clearCategory');
            Route::post('/add-to-cart', [DeploymentController::class, 'addToCart'])->name('admin.deployment.addToCart');
            Route::post('/bulk-add-to-cart', [DeploymentController::class, 'bulkAddToCart'])->name('admin.deployment.bulkAddToCart');
            Route::delete('/remove-from-cart/{id}', [DeploymentController::class, 'removeFromCart'])->name('admin.deployment.removeFromCart');
            Route::delete('/clear-cart', [DeploymentController::class, 'clearCart'])->name('admin.deployment.clearCart');
            Route::put('/update-cart/{id}', [DeploymentController::class, 'updateCart'])->name('admin.deployment.updateCart');
            Route::post('/deploy', [DeploymentController::class, 'deploy'])->name('admin.deployment.deploy');
            Route::get('/history', [DeploymentController::class, 'history'])->name('admin.deployment.history');
            Route::get('/{id}', [DeploymentController::class, 'show'])->name('admin.deployment.show');
        });

        // Reports
        Route::get('reports', [ReportsController::class, 'index'])->name('admin.reports');

        // Suppliers Routes
        Route::prefix('suppliers')->group(function () {
            Route::get('/', [SuppliersController::class, 'index'])->name('admin.suppliers');
            Route::post('/', [SuppliersController::class, 'store'])->name('admin.suppliers.store');
            Route::get('/{id}/edit', [SuppliersController::class, 'edit'])->name('admin.suppliers.edit');
            Route::put('/{id}', [SuppliersController::class, 'update'])->name('admin.suppliers.update');
            Route::delete('/{id}', [SuppliersController::class, 'destroy'])->name('admin.suppliers.destroy');
        });

        // ✅ FIXED: Contact Person Routes - NOW INSIDE auth middleware and admin prefix
        Route::prefix('contactperson')->group(function () {
            Route::get('/', [ContactPersonController::class, 'index'])->name('admin.contactperson');
            Route::post('/', [ContactPersonController::class, 'store'])->name('admin.contactperson.store');
            Route::put('/{id}', [ContactPersonController::class, 'update'])->name('admin.contactperson.update');
            Route::delete('/{id}', [ContactPersonController::class, 'destroy'])->name('admin.contactperson.destroy');
        });

        // Accounts Management Routes
        Route::prefix('accounts')->group(function () {
            Route::get('/', [AccountsController::class, 'index'])->name('admin.accounts');
            Route::post('/', [AccountsController::class, 'store'])->name('admin.accounts.store');
            Route::get('/{id}/edit', [AccountsController::class, 'edit'])->name('admin.accounts.edit');
            Route::put('/{id}', [AccountsController::class, 'update'])->name('admin.accounts.update');
            Route::delete('/{id}', [AccountsController::class, 'destroy'])->name('admin.accounts.destroy');
        });
    });
});

// Optional: Add a catch-all route for undefined routes
Route::fallback(function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});