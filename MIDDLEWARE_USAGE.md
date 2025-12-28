# Middleware Usage Guide

## Available Middlewares

### 1. `admin` - AdminMiddleware
**Purpose:** Basic authentication check - ensures user is logged in and has active status.

**Usage:**
```php
Route::middleware(['admin'])->group(function () {
    // Routes accessible to all authenticated users
});
```

**What it does:**
- Checks if user is authenticated
- Validates user status (active/suspended/deleted)
- Logs out and redirects if status is suspended or deleted

---

### 2. `super.admin` - SuperAdminMiddleware
**Purpose:** Restricts access to super admin users only.

**Usage:**
```php
Route::middleware(['super.admin'])->group(function () {
    // Routes only accessible to super admin
    Route::get('/admin/settings', [SettingsController::class, 'index']);
});
```

**What it does:**
- Checks if user is authenticated
- Verifies user_type is 'super_admin'
- Returns 403 if not super admin

---

### 3. `permission` - PermissionMiddleware
**Purpose:** Checks if user has a specific permission.

**Usage:**
```php
// Single permission
Route::middleware(['permission:hotels.create'])->group(function () {
    Route::post('/hotels', [HotelController::class, 'store']);
});

// Multiple permissions (user needs ALL)
Route::middleware(['permission:hotels.create', 'permission:hotels.edit'])->group(function () {
    // User must have both permissions
});
```

**What it does:**
- Checks if user is authenticated
- Super admin automatically passes (has all permissions)
- Verifies user has the required permission through their roles
- Returns 403 if permission not found

**Note:** Super admin automatically has all permissions.

---

### 4. `role` - RoleMiddleware
**Purpose:** Checks if user has a specific role (OR logic for multiple roles).

**Usage:**
```php
// Single role
Route::middleware(['role:hotel-owner'])->group(function () {
    Route::get('/hotels', [HotelController::class, 'index']);
});

// Multiple roles (user needs ANY one)
Route::middleware(['role:hotel-owner,manager'])->group(function () {
    // User must have hotel-owner OR manager role
});
```

**What it does:**
- Checks if user is authenticated
- Super admin automatically passes (has all roles)
- Verifies user has at least one of the required roles
- Returns 403 if no matching role found

**Note:** Multiple roles use OR logic (user needs any one).

---

### 5. `hotel.access` - HotelAccessMiddleware
**Purpose:** Checks if user has access to a specific hotel.

**Usage:**
```php
Route::middleware(['hotel.access'])->group(function () {
    Route::get('/hotels/{hotel}/rooms', [RoomController::class, 'index']);
    Route::post('/hotels/{hotel}/rooms', [RoomController::class, 'store']);
});
```

**What it does:**
- Checks if user is authenticated
- Super admin automatically passes (has access to all hotels)
- For hotel owners: checks if hotel belongs to them
- For staff: checks if they have access via `user_hotel_access` table
- Returns 403 if no access

**How it gets hotel_id:**
- From route parameter: `{hotel}` or `{hotel_id}`
- From request input: `hotel_id`

---

## Combining Middlewares

You can combine multiple middlewares:

```php
// User must be authenticated AND have permission
Route::middleware(['admin', 'permission:hotels.create'])->group(function () {
    Route::post('/hotels', [HotelController::class, 'store']);
});

// User must be authenticated AND have role AND have hotel access
Route::middleware(['admin', 'role:hotel-owner,manager', 'hotel.access'])->group(function () {
    Route::get('/hotels/{hotel}/dashboard', [HotelDashboardController::class, 'index']);
});
```

---

## Example Route Groups

### Super Admin Routes
```php
Route::middleware(['super.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', [SystemSettingsController::class, 'index']);
    Route::get('/users', [UserManagementController::class, 'index']);
    Route::post('/reservations/override', [AdminReservationController::class, 'createOverride']);
});
```

### Hotel Owner Routes
```php
Route::middleware(['admin', 'role:hotel-owner'])->prefix('hotel-owner')->name('hotel-owner.')->group(function () {
    Route::resource('hotels', HotelController::class);
    Route::resource('staff', StaffController::class);
    Route::get('/roles', [RoleController::class, 'index']);
});
```

### Hotel-Specific Routes (with access check)
```php
Route::middleware(['admin', 'hotel.access'])->prefix('hotels/{hotel}')->name('hotels.')->group(function () {
    Route::resource('rooms', RoomController::class);
    Route::resource('guests', GuestController::class);
    Route::resource('reservations', ReservationController::class);
});
```

### Permission-Based Routes
```php
Route::middleware(['admin', 'permission:reservations.create'])->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store']);
});

Route::middleware(['admin', 'permission:reservations.checkin'])->group(function () {
    Route::post('/reservations/{reservation}/checkin', [ReservationController::class, 'checkin']);
});
```

---

## Best Practices

1. **Always use `admin` middleware first** for authenticated routes
2. **Use `permission` middleware** for granular access control
3. **Use `role` middleware** for role-based access (less granular)
4. **Use `hotel.access` middleware** for hotel-specific resources
5. **Super admin bypasses all checks** - keep this in mind when designing routes
6. **Combine middlewares** for complex access requirements

---

## Controller-Level Permission Checks

You can also check permissions in controllers:

```php
public function store(Request $request)
{
    // Check permission
    if (!auth()->user()->hasPermission('hotels.create')) {
        abort(403, 'You do not have permission to create hotels.');
    }

    // Check hotel access
    if (!auth()->user()->hasAccessToHotel($request->hotel_id)) {
        abort(403, 'You do not have access to this hotel.');
    }

    // Proceed with creation
}
```

---

## Testing Middlewares

```php
// In tests
$user = User::factory()->create(['user_type' => 'hotel_owner']);
$user->roles()->attach($role);
$role->permissions()->attach($permission);

$this->actingAs($user)
    ->get('/hotels')
    ->assertStatus(200);
```

