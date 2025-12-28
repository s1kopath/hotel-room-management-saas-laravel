# Hotel Room Management SaaS - Development Plan

**Last Updated:** Phase 1 - 85% Complete ✅

## 🎯 Current Status

**Phase 1 (Foundation) - 85% Complete:**

-   ✅ All database migrations (17 tables)
-   ✅ All Eloquent models (15 models)
-   ✅ Authentication system updated
-   ✅ Middleware system complete (5 middlewares)
-   ✅ All seeders created (6 seeders)
-   ✅ UserFactory updated
-   ⏳ UserController needs completion (edit/update/destroy)
-   ⏳ UsersDataTable needs update

**Ready to Start:** Phase 2 - Hotel & Room Management

---

## Current State Analysis

### ✅ What's Already Done

-   Laravel 12 setup
-   **✅ Authentication System - COMPLETE**
    -   Login/Register/Password Reset updated for new schema
    -   Login supports username OR email
    -   User status validation (active/suspended/deleted)
    -   Last login tracking
-   **✅ Database Schema Implementation - COMPLETE**
    -   All 17 database migrations created (users, roles, permissions, hotels, rooms, guests, reservations, etc.)
    -   All migrations use BIGINT UNSIGNED (auto-increment) primary keys
    -   All foreign keys and indexes properly defined
-   **✅ Eloquent Models - COMPLETE**
    -   User model updated with all relationships, scopes, and helper methods
    -   All 15 models created: Role, Permission, Hotel, UserHotelAccess, HotelImage, Room, RoomImage, Guest, Reservation, AdminReservationHistory, RoomStatusHistory, ActivityLog, SystemSetting, AdminReservationArchive
    -   All relationships, casts, and scopes properly defined
-   **✅ Middleware System - COMPLETE**
    -   AdminMiddleware updated (checks authentication and status)
    -   SuperAdminMiddleware created
    -   PermissionMiddleware created
    -   RoleMiddleware created
    -   HotelAccessMiddleware created
    -   All middlewares registered in bootstrap/app.php
-   **✅ Seeders & Factories - COMPLETE**
    -   SuperAdminSeeder - creates super admin user
    -   RoleSeeder - creates all default roles
    -   PermissionSeeder - creates all permissions
    -   RolePermissionSeeder - assigns permissions to roles
    -   SystemSettingSeeder - creates system settings
    -   UserSeeder - creates hotel owners and staff
    -   UserFactory updated with all schema fields and states
-   **✅ User Controller - PARTIALLY COMPLETE**
    -   Store method updated to use new schema
    -   Edit, update, destroy methods still need completion
-   Basic dashboard structure - **Template exists, needs real data**
-   User management UI (DataTables) - **CRUD partially done (create works, edit/destroy incomplete)**
-   File handling service (FileHandlerService) - **Complete and ready to use**
-   Helpers for image handling - **Complete**
-   Blade layout structure - **Complete**
-   Modal loading system - **Complete**

### ❌ What Needs to Be Done

-   Complete UserController (edit, update, destroy methods)
-   Update UsersDataTable to match new schema
-   Build hotel management system (controllers, views)
-   Build room management with status tracking (controllers, views)
-   Build guest management (controllers, views)
-   Build reservation system (controllers, views)
-   Implement admin override reservations
-   Build admin reservation history (30-day + archive)
-   Activity logging service implementation
-   System settings management UI
-   Role & Permission management UI
-   Hotel access management UI

---

## Development Phases

### **PHASE 1: Foundation & Database Setup** ⚙️

**Priority: CRITICAL**  
**Estimated Time: 2-3 days**  
**Status: ~85% COMPLETE** ✅

#### 1.1 Update Core User System

-   [x] Update users migration to match schema (user_type enum, parent_user_id, status, etc.) - **COMPLETE**
-   [x] Update User model (relationships, casts, scopes) - **COMPLETE**
-   [x] Create user factory and seeder - **COMPLETE**
-   [x] Update authentication to work with new user structure - **COMPLETE**
-   [ ] Complete UserController (edit, update, destroy methods - currently incomplete)
-   [x] Update UserController to use new schema fields (store method) - **COMPLETE**
-   [ ] Update UsersDataTable to match new schema

#### 1.2 Install/Implement Role-Permission System

**Decision Point:** Use Spatie Permission package OR build custom?

-   **Option A:** Install `spatie/laravel-permission` (recommended for speed)
-   **Option B:** Build custom implementation (more control, matches schema exactly)

**Recommendation:** Option B - Build custom to match exact schema requirements

-   [x] Create roles migration - **COMPLETE**
-   [x] Create permissions migration - **COMPLETE**
-   [x] Create role_permissions pivot migration - **COMPLETE**
-   [x] Create user_roles pivot migration - **COMPLETE**
-   [x] Create Role model with relationships - **COMPLETE**
-   [x] Create Permission model with relationships - **COMPLETE**
-   [x] Create Permission service/trait for checking permissions (hasPermission method in User model) - **COMPLETE**
-   [x] Create permission middleware - **COMPLETE**

#### 1.3 Core System Tables

-   [x] Create hotels migration - **COMPLETE**
-   [x] Create user_hotel_access migration - **COMPLETE**
-   [x] Create system_settings migration - **COMPLETE**
-   [x] Create activity_logs migration - **COMPLETE**
-   [x] Create hotel_images migration - **COMPLETE**
-   [x] Create rooms migration - **COMPLETE**
-   [x] Create room_images migration - **COMPLETE**
-   [x] Create guests migration - **COMPLETE**
-   [x] Create reservations migration - **COMPLETE**
-   [x] Create admin_reservations_history migration - **COMPLETE**
-   [x] Create room_status_history migration - **COMPLETE**
-   [x] Create admin_reservations_archive migration - **COMPLETE**

#### 1.4 Seed Initial Data

-   [x] Create super admin seeder - **COMPLETE**
-   [x] Create default roles seeder (Super Admin, Hotel Owner, Manager, Receptionist, Housekeeping) - **COMPLETE**
-   [x] Create default permissions seeder - **COMPLETE**
-   [x] Assign permissions to roles - **COMPLETE**
-   [x] Create system settings seeder - **COMPLETE**
-   [x] Create user seeder (hotel owners, staff, test users) - **COMPLETE**

---

### **PHASE 2: Hotel & Room Management** 🏨

**Priority: HIGH**  
**Estimated Time: 3-4 days**

#### 2.1 Hotel Management

-   [x] Create Hotel model with relationships - **COMPLETE**
-   [x] Create hotel_images migration and model - **COMPLETE**
-   [ ] Create HotelController (CRUD operations)
-   [ ] Create hotel views (index, create, edit, show)
-   [ ] Implement hotel image upload
-   [ ] Add permission checks (hotels.create, hotels.edit-own, etc.)
-   [ ] Create hotel access control logic

#### 2.2 Room Management

-   [x] Create rooms migration - **COMPLETE**
-   [x] Create room_images migration - **COMPLETE**
-   [x] Create Room model with relationships - **COMPLETE**
-   [x] Create room_status_history migration and model - **COMPLETE**
-   [ ] Create RoomController
-   [ ] Create room views with color-coded status
-   [ ] Implement room status change logic (vacant/reserved/occupied/admin_reserved)
-   [ ] Track status changes in history table

#### 2.3 Room Status Color System

-   [ ] Implement status colors:
    -   🟢 Green = vacant
    -   🟡 Yellow = reserved
    -   🔴 Red = occupied
    -   🔵 Blue = admin_reserved
-   [ ] Create room status change UI
-   [ ] Add permission checks (rooms.change-status)

---

### **PHASE 3: Guest & Reservation Management** 📅

**Priority: HIGH**  
**Estimated Time: 4-5 days**

#### 3.1 Guest Management

-   [x] Create guests migration - **COMPLETE**
-   [x] Create Guest model - **COMPLETE**
-   [ ] Create GuestController
-   [ ] Create guest views (index, create, edit, show)
-   [ ] Implement guest search functionality
-   [ ] Store guest preferences (JSON field)
-   [ ] Add permission checks (guests.create, guests.edit, etc.)

#### 3.2 Reservation System

-   [x] Create reservations migration - **COMPLETE**
-   [x] Create Reservation model with relationships - **COMPLETE**
-   [ ] Create ReservationController
-   [ ] Create reservation views
-   [ ] Implement reservation creation flow:
    -   Select hotel → Select room → Select guest → Select dates → Confirm
-   [ ] Implement check-in functionality
-   [ ] Implement check-out functionality
-   [ ] Handle reservation status flow (pending → confirmed → checked_in → checked_out)
-   [ ] Add payment tracking
-   [ ] Add permission checks (reservations.create, reservations.edit-own, etc.)

#### 3.3 Admin Override Reservations

-   [x] Create admin_reservations_history migration - **COMPLETE**
-   [x] Create AdminReservationHistory model - **COMPLETE**
-   [ ] Implement admin override reservation creation
-   [ ] Mark rooms as admin_reserved (blue status)
-   [ ] Prevent hotel staff from modifying admin reservations
-   [ ] Create admin reservation release functionality
-   [ ] Track all admin actions in history

---

### **PHASE 4: Admin Features & History** 👨‍💼

**Priority: MEDIUM**  
**Estimated Time: 2-3 days**

#### 4.1 Admin Reservation History

-   [ ] Create view for last 30 days admin reservations
-   [ ] Implement monthly archive functionality
-   [x] Create admin_reservations_archive migration - **COMPLETE**
-   [ ] Create archive service/job
-   [ ] Create monthly archive command/scheduler
-   [ ] Add reset/clear archive functionality

#### 4.2 Enhanced Admin Dashboard

-   [ ] Display all hotels across all owners
-   [ ] Show admin reserved rooms (blue) across all hotels
-   [ ] Admin reservation management UI
-   [ ] System-wide analytics

---

### **PHASE 5: Role & Permission Management UI** 🔐

**Priority: MEDIUM**  
**Estimated Time: 3-4 days**

#### 5.1 Role Management

-   [ ] Create RoleController (for hotel owners to manage custom roles)
-   [ ] Create role management views
-   [ ] Allow hotel owners to create custom roles (scope: hotel_owner)
-   [ ] Allow hotel owners to assign permissions to roles
-   [ ] Create role assignment UI for staff

#### 5.2 Permission Management

-   [ ] Create PermissionController (admin only)
-   [ ] Display available permissions
-   [ ] Allow assigning permissions to roles via UI

#### 5.3 Hotel Access Management

-   [ ] Create UI for hotel owners to grant/revoke hotel access to staff
-   [ ] Create UserHotelAccessController
-   [ ] Staff can see only assigned hotels

---

### **PHASE 6: Activity Logging & Settings** 📊

**Priority: LOW**  
**Estimated Time: 2 days**

#### 6.1 Activity Logging

-   [x] Create ActivityLog model - **COMPLETE**
-   [ ] Implement logging service
-   [ ] Log all major actions (create, update, delete, status changes)
-   [ ] Create activity log viewer (admin only)
-   [ ] Filter logs by user, action type, entity type

#### 6.2 System Settings

-   [x] Create SystemSetting model - **COMPLETE**
-   [ ] Create settings management UI (admin only)
-   [ ] Implement setting helpers (get/set methods already in model)
-   [ ] Add settings like: reservation_archive_days, auto_archive_enabled, etc.

---

### **PHASE 7: Testing & Optimization** ✅

**Priority: HIGH**  
**Estimated Time: 3-4 days**

**Note:** API endpoints are not needed as this is a Blade-based application (no separate frontend).

#### 7.1 Testing

-   [ ] Write feature tests for hotel CRUD
-   [ ] Write feature tests for room status changes
-   [ ] Write feature tests for reservations
-   [ ] Write feature tests for permissions
-   [ ] Write feature tests for admin overrides
-   [ ] Test all permission scenarios

#### 7.2 Performance Optimization

-   [ ] Add database indexes (check schema.md)
-   [ ] Implement permission caching (Redis)
-   [ ] Optimize queries with eager loading
-   [ ] Add query optimization for large datasets

#### 7.3 Security Audit

-   [ ] Review all permission checks
-   [ ] Review access control logic
-   [ ] Test authorization edge cases
-   [ ] Review SQL injection prevention
-   [ ] Review XSS prevention

---

## Implementation Strategy

### Step-by-Step Approach

1. **✅ Phase 1 - Foundation** - **85% COMPLETE**
    - ✅ Database migrations and models
    - ✅ Authentication system updated
    - ✅ Middleware system complete
    - ✅ Seeders and factories ready
    - ⏳ UserController needs completion (edit/update/destroy)
    - ⏳ UsersDataTable needs update
2. **Next: Phase 2** - Hotel & Room Management (controllers and views)
3. **Implement in order** - Each phase builds on previous phases
4. **Test as you go** - Don't wait until the end to test
5. **Use migrations** - Version control your database changes
6. **Follow schema** - Stick closely to SCHEMA.md definitions

### Key Decisions Made ✅

1. **Auto-increment IDs** ✅ **DECIDED**

    - Using BIGINT UNSIGNED (auto-increment) for all primary keys
    - Removed UUID requirement from schema

2. **Role-Permission Package** ✅ **DECIDED & IMPLEMENTED**

    - Custom implementation chosen (matches schema exactly)
    - All migrations and models created ✅
    - Permission checking implemented (hasPermission method in User model) ✅
    - All middlewares created and registered ✅

3. **File Storage** ✅ **READY**

    - FileHandlerService already exists ✅
    - Ready to use for hotel/room images

4. **Frontend Framework** ✅ **DECIDED**
    - Blade templates with DataTables ✅
    - No separate frontend needed - Laravel handles everything

---

## Technical Notes

### Database Conventions

-   All timestamps: created_at, updated_at
-   Soft deletes: Use deleted_at where appropriate

### Permission Checking Pattern

**Using Middleware (Recommended):**

```php
// In routes/web.php
Route::middleware(['admin', 'permission:hotels.create'])->group(function () {
    Route::post('/hotels', [HotelController::class, 'store']);
});

// Check hotel access
Route::middleware(['admin', 'hotel.access'])->group(function () {
    Route::get('/hotels/{hotel}/rooms', [RoomController::class, 'index']);
});
```

**In Controllers:**

```php
// Check permission before action
if (!auth()->user()->hasPermission('hotels.create')) {
    abort(403);
}

// Check hotel access
if (!auth()->user()->hasAccessToHotel($hotelId)) {
    abort(403);
}

// Check admin override
if ($room->hasAdminReservation()) {
    abort(403, 'Room is reserved by admin');
}
```

**Available Middlewares:**

-   `admin` - Basic authentication check
-   `super.admin` - Super admin only
-   `permission:permission.name` - Permission check
-   `role:role-slug` - Role check (supports multiple: role1,role2)
-   `hotel.access` - Hotel access check

### Status Change Pattern

```php
// When changing room status
$room->update(['status' => 'occupied']);
RoomStatusHistory::create([
    'room_id' => $room->id,
    'previous_status' => 'reserved',
    'new_status' => 'occupied',
    'changed_by' => auth()->id(),
]);
```
