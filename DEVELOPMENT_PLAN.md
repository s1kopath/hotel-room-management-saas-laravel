# Hotel Room Management SaaS - Development Plan

## Current State Analysis

### ✅ What's Already Done

-   Laravel 12 setup
-   Basic authentication (Login/Register/Password Reset) - **Complete**
-   **✅ Database Schema Implementation - COMPLETE**
    -   All 17 database migrations created (users, roles, permissions, hotels, rooms, guests, reservations, etc.)
    -   All migrations use BIGINT UNSIGNED (auto-increment) primary keys
    -   All foreign keys and indexes properly defined
-   **✅ Eloquent Models - COMPLETE**
    -   User model updated with all relationships, scopes, and helper methods
    -   All 15 models created: Role, Permission, Hotel, UserHotelAccess, HotelImage, Room, RoomImage, Guest, Reservation, AdminReservationHistory, RoomStatusHistory, ActivityLog, SystemSetting, AdminReservationArchive
    -   All relationships, casts, and scopes properly defined
-   Admin middleware (using `is_admin` flag) - **Works but needs updating to use permission system**
-   Basic dashboard structure - **Template exists, needs real data**
-   User management UI (DataTables) - **CRUD partially done (create works, edit/destroy incomplete)**
-   File handling service (FileHandlerService) - **Complete and ready to use**
-   Helpers for image handling - **Complete**
-   Blade layout structure - **Complete**
-   Modal loading system - **Complete**

### ❌ What Needs to Be Done

-   Create user factory and seeder
-   Update authentication to work with new user structure
-   Complete UserController (edit, update, destroy methods)
-   Update UsersDataTable to match new schema
-   Create Permission service/trait for checking permissions
-   Create permission middleware
-   Seed initial data (super admin, default roles, permissions)
-   Build hotel management system (controllers, views)
-   Build room management with status tracking (controllers, views)
-   Build guest management (controllers, views)
-   Build reservation system (controllers, views)
-   Implement admin override reservations
-   Build admin reservation history (30-day + archive)
-   Build hotel access control system
-   Activity logging service implementation
-   System settings management UI

---

## Development Phases

### **PHASE 1: Foundation & Database Setup** ⚙️

**Priority: CRITICAL**  
**Estimated Time: 2-3 days**

#### 1.1 Update Core User System

-   [x] Update users migration to match schema (user_type enum, parent_user_id, status, etc.) - **COMPLETE**
-   [x] Update User model (relationships, casts, scopes) - **COMPLETE**
-   [ ] Create user factory and seeder
-   [ ] Update authentication to work with new user structure
-   [ ] Complete UserController (edit, update, destroy methods - currently incomplete)
-   [ ] Update UserController to use new schema fields
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
-   [ ] Create Permission service/trait for checking permissions
-   [ ] Create permission middleware

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

-   [ ] Create super admin seeder
-   [ ] Create default roles seeder (Super Admin, Hotel Owner, Manager, Receptionist, Housekeeping)
-   [ ] Create default permissions seeder
-   [ ] Assign permissions to roles
-   [ ] Create system settings seeder

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

1. **Start with Phase 1** - Foundation must be solid before building features
2. **Implement in order** - Each phase builds on previous phases
3. **Test as you go** - Don't wait until the end to test
4. **Use migrations** - Version control your database changes
5. **Follow schema** - Stick closely to SCHEMA.md definitions

### Key Decisions Made ✅

1. **Auto-increment IDs** ✅ **DECIDED**

    - Using BIGINT UNSIGNED (auto-increment) for all primary keys
    - Removed UUID requirement from schema

2. **Role-Permission Package** ✅ **DECIDED**

    - Custom implementation chosen (matches schema exactly)
    - All migrations and models created
    - Need to implement permission checking service/trait and middleware

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
