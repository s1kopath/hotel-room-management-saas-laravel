# Hotel Room Management SaaS - Development Plan

## Current State Analysis

### ✅ What's Already Done

-   Laravel 12 setup
-   Basic authentication (Login/Register/Password Reset)
-   User model with basic fields
-   Admin middleware (using `is_admin` flag)
-   Basic dashboard structure
-   User management UI (DataTables)
-   File handling service
-   API routes structure

### ❌ What Needs to Be Done

-   Update User model to match schema (UUID, user_type, parent_user_id, etc.)
-   Implement role-permission system (Spatie-style, custom implementation)
-   Create all database migrations (17 tables from schema)
-   Create all Eloquent models with relationships
-   Build hotel management system
-   Build room management with status tracking
-   Build guest management
-   Build reservation system
-   Implement admin override reservations
-   Build admin reservation history (30-day + archive)
-   Create permission middleware
-   Build hotel access control system
-   Activity logging system
-   System settings management

---

## Development Phases

### **PHASE 1: Foundation & Database Setup** ⚙️

**Priority: CRITICAL**  
**Estimated Time: 2-3 days**

#### 1.1 Update Core User System

-   [ ] Install UUID package (ramsey/uuid)
-   [ ] Update users migration to match schema (UUID, user_type enum, parent_user_id, status, etc.)
-   [ ] Update User model (relationships, casts, scopes)
-   [ ] Create user factory and seeder
-   [ ] Update authentication to work with new user structure

#### 1.2 Install/Implement Role-Permission System

**Decision Point:** Use Spatie Permission package OR build custom?

-   **Option A:** Install `spatie/laravel-permission` (recommended for speed)
-   **Option B:** Build custom implementation (more control, matches schema exactly)

**Recommendation:** Option B - Build custom to match exact schema requirements

-   [ ] Create roles migration
-   [ ] Create permissions migration
-   [ ] Create role_permissions pivot migration
-   [ ] Create user_roles pivot migration
-   [ ] Create Role model with relationships
-   [ ] Create Permission model with relationships
-   [ ] Create Permission service/trait for checking permissions
-   [ ] Create permission middleware

#### 1.3 Core System Tables

-   [ ] Create hotels migration
-   [ ] Create user_hotel_access migration
-   [ ] Create system_settings migration
-   [ ] Create activity_logs migration

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

-   [ ] Create Hotel model with relationships
-   [ ] Create hotel_images migration and model
-   [ ] Create HotelController (CRUD operations)
-   [ ] Create hotel views (index, create, edit, show)
-   [ ] Implement hotel image upload
-   [ ] Add permission checks (hotels.create, hotels.edit-own, etc.)
-   [ ] Create hotel access control logic

#### 2.2 Room Management

-   [ ] Create rooms migration
-   [ ] Create room_images migration
-   [ ] Create Room model with relationships
-   [ ] Create RoomController
-   [ ] Create room views with color-coded status
-   [ ] Implement room status change logic (vacant/reserved/occupied/admin_reserved)
-   [ ] Create room_status_history migration and model
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

-   [ ] Create guests migration
-   [ ] Create Guest model
-   [ ] Create GuestController
-   [ ] Create guest views (index, create, edit, show)
-   [ ] Implement guest search functionality
-   [ ] Store guest preferences (JSON field)
-   [ ] Add permission checks (guests.create, guests.edit, etc.)

#### 3.2 Reservation System

-   [ ] Create reservations migration
-   [ ] Create Reservation model with relationships
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

-   [ ] Create admin_reservations_history migration
-   [ ] Create AdminReservationHistory model
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
-   [ ] Create admin_reservations_archive migration
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

-   [ ] Create ActivityLog model
-   [ ] Implement logging service
-   [ ] Log all major actions (create, update, delete, status changes)
-   [ ] Create activity log viewer (admin only)
-   [ ] Filter logs by user, action type, entity type

#### 6.2 System Settings

-   [ ] Create SystemSetting model
-   [ ] Create settings management UI (admin only)
-   [ ] Implement setting helpers
-   [ ] Add settings like: reservation_archive_days, auto_archive_enabled, etc.

---

### **PHASE 7: API Endpoints** 🔌

**Priority: MEDIUM**  
**Estimated Time: 3-4 days**

#### 7.1 Hotels API

-   [ ] GET /api/hotels (list user's hotels)
-   [ ] GET /api/hotels/{id} (show hotel)
-   [ ] POST /api/hotels (create hotel)
-   [ ] PUT /api/hotels/{id} (update hotel)
-   [ ] DELETE /api/hotels/{id} (delete hotel)

#### 7.2 Rooms API

-   [ ] GET /api/hotels/{hotelId}/rooms (list rooms)
-   [ ] GET /api/rooms/{id} (show room)
-   [ ] POST /api/rooms (create room)
-   [ ] PUT /api/rooms/{id} (update room)
-   [ ] PUT /api/rooms/{id}/status (change status)
-   [ ] DELETE /api/rooms/{id} (delete room)

#### 7.3 Guests API

-   [ ] GET /api/guests (list guests)
-   [ ] POST /api/guests (create guest)
-   [ ] PUT /api/guests/{id} (update guest)
-   [ ] GET /api/guests/search (search guests)

#### 7.4 Reservations API

-   [ ] GET /api/reservations (list reservations)
-   [ ] POST /api/reservations (create reservation)
-   [ ] PUT /api/reservations/{id} (update reservation)
-   [ ] POST /api/reservations/{id}/checkin (check in)
-   [ ] POST /api/reservations/{id}/checkout (check out)
-   [ ] POST /api/reservations/{id}/cancel (cancel reservation)

#### 7.5 Admin API

-   [ ] POST /api/admin/reservations (create admin override)
-   [ ] GET /api/admin/reservations/history (get history)
-   [ ] DELETE /api/admin/reservations/{id} (release admin reservation)

---

### **PHASE 8: Testing & Optimization** ✅

**Priority: HIGH**  
**Estimated Time: 3-4 days**

#### 8.1 Testing

-   [ ] Write feature tests for hotel CRUD
-   [ ] Write feature tests for room status changes
-   [ ] Write feature tests for reservations
-   [ ] Write feature tests for permissions
-   [ ] Write feature tests for admin overrides
-   [ ] Test all permission scenarios

#### 8.2 Performance Optimization

-   [ ] Add database indexes (check schema.md)
-   [ ] Implement permission caching (Redis)
-   [ ] Optimize queries with eager loading
-   [ ] Add query optimization for large datasets

#### 8.3 Security Audit

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

### Key Decisions to Make

1. **UUID vs Auto-increment IDs**

    - Schema uses UUID → Install ramsey/uuid package
    - Use UUID for all primary keys

2. **Role-Permission Package**

    - Custom implementation recommended (matches schema exactly)
    - Spatie package is great but doesn't match schema structure

3. **File Storage**

    - Already have FileHandlerService
    - Use for hotel/room images

4. **Frontend Framework**
    - Current: Blade templates with DataTables
    - Consider: Keep Blade or add Vue/React later

---

## Technical Notes

### Database Conventions

-   All primary keys: UUID
-   All timestamps: created_at, updated_at
-   Soft deletes: Use deleted_at where appropriate
-   Foreign keys: UUID references

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

---

## Next Steps

1. Review this plan
2. Confirm decisions (UUID, custom roles, etc.)
3. Start with Phase 1.1 - Update User system
4. Follow phases sequentially
