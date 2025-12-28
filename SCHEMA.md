# Hotel Room Management Database Schema Documentation

## (Spatie-Style Role & Permission System)

## Overview

This database supports a SaaS-based Hotel Room Management system with **flexible role-based access control** using Spatie-style permissions. Admin, Hotel Owners, and Staff can have custom roles with granular permissions.

---

## 📋 TABLE 1: USERS

**Purpose:** Store all system users (Admin, Hotel Owners, Staff)

| Column Name    | Data Type       | Constraints                 | Description                           |
| -------------- | --------------- | --------------------------- | ------------------------------------- |
| id             | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique user identifier                |
| username       | VARCHAR(50)     | UNIQUE, NOT NULL            | Login username                        |
| email          | VARCHAR(100)    | UNIQUE, NOT NULL            | User email address                    |
| password_hash  | VARCHAR(255)    | NOT NULL                    | Encrypted password                    |
| full_name      | VARCHAR(200)    | NULL                        | Full name of user                     |
| phone          | VARCHAR(20)     | NULL                        | Contact phone number                  |
| user_type      | ENUM            | NOT NULL                    | 'super_admin', 'hotel_owner', 'staff' |
| parent_user_id | BIGINT UNSIGNED | FOREIGN KEY → users(id)     | Hotel owner who created this user     |
| status         | ENUM            | NOT NULL                    | 'active', 'suspended', 'deleted'      |
| created_at     | TIMESTAMP       | DEFAULT NOW                 | Account creation date                 |
| updated_at     | TIMESTAMP       | AUTO UPDATE                 | Last modification date                |
| created_by     | BIGINT UNSIGNED | FOREIGN KEY → users(id)     | Who created this account              |
| last_login     | TIMESTAMP       | NULL                        | Last login timestamp                  |

**Indexes:**

-   username
-   email
-   user_type
-   status
-   parent_user_id

**User Types:**

-   **super_admin** = System administrator (Anthropic/Company)
-   **hotel_owner** = Customer who owns one or more hotels
-   **staff** = Employee working under a hotel owner

---

## 🎭 TABLE 2: ROLES

**Purpose:** Define all roles in the system (system-wide + custom per hotel owner)

| Column Name    | Data Type       | Constraints                 | Description                                          |
| -------------- | --------------- | --------------------------- | ---------------------------------------------------- |
| id             | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique role identifier                               |
| name           | VARCHAR(100)    | NOT NULL                    | Role name (e.g., 'Admin', 'Manager', 'Receptionist') |
| slug           | VARCHAR(100)    | NOT NULL                    | URL-friendly identifier (e.g., 'admin', 'manager')   |
| description    | TEXT            | NULL                        | What this role is for                                |
| scope          | ENUM            | NOT NULL                    | 'system', 'hotel_owner'                              |
| created_by     | BIGINT UNSIGNED | FOREIGN KEY → users(id)     | Who created this role                                |
| hotel_owner_id | BIGINT UNSIGNED | FOREIGN KEY → users(id)     | If scope='hotel_owner', which owner created it       |
| is_default     | BOOLEAN         | DEFAULT FALSE               | Default role for new users                           |
| created_at     | TIMESTAMP       | DEFAULT NOW                 | Creation date                                        |
| updated_at     | TIMESTAMP       | AUTO UPDATE                 | Last update date                                     |

**Indexes:**

-   slug
-   scope
-   hotel_owner_id
-   Composite: (slug, hotel_owner_id) - UNIQUE

**Scope Types:**

-   **system** = Created by super admin, applies globally (e.g., 'Super Admin', 'Hotel Owner')
-   **hotel_owner** = Created by hotel owner for their team (e.g., 'Night Manager', 'Housekeeping')

**Important:** Hotel owners can only create/edit roles with `scope='hotel_owner'` and `hotel_owner_id = their own ID`

---

## 🔑 TABLE 3: PERMISSIONS

**Purpose:** Define all possible actions in the system

| Column Name  | Data Type       | Constraints                 | Description                                          |
| ------------ | --------------- | --------------------------- | ---------------------------------------------------- |
| id           | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique permission identifier                         |
| name         | VARCHAR(100)    | UNIQUE, NOT NULL            | Permission name (e.g., 'create-hotels')              |
| display_name | VARCHAR(200)    | NOT NULL                    | Human-readable name (e.g., 'Create Hotels')          |
| category     | VARCHAR(50)     | NULL                        | Group permissions (e.g., 'hotels', 'rooms', 'users') |
| description  | TEXT            | NULL                        | What this permission allows                          |
| created_at   | TIMESTAMP       | DEFAULT NOW                 | Creation date                                        |

**Indexes:**

-   name
-   category

**Permission Categories:**

-   **users** = User management permissions
-   **hotels** = Hotel management permissions
-   **rooms** = Room management permissions
-   **reservations** = Reservation management
-   **reports** = Analytics and reports
-   **system** = System-level settings

**Example Permissions:**

```
users.create           - Create new users
users.edit             - Edit user details
users.delete           - Delete users
users.view-all         - View all system users
users.view-own         - View users created by self

hotels.create          - Create new hotels
hotels.edit-own        - Edit own hotels
hotels.edit-all        - Edit any hotel
hotels.delete-own      - Delete own hotels
hotels.view-all        - View all hotels
hotels.view-own        - View only own hotels

rooms.create           - Add new rooms
rooms.edit             - Edit room details
rooms.delete           - Delete rooms
rooms.change-status    - Change room availability status
rooms.admin-reserve    - Make admin override reservations (blue status)

guests.create          - Add new guests
guests.edit            - Edit guest information
guests.delete          - Delete guest records
guests.view-own        - View guests in own hotels
guests.view-all        - View all guests (admin)

reservations.create    - Create reservations for guests
reservations.edit-own  - Edit own hotel reservations
reservations.edit-all  - Edit any reservation (admin)
reservations.cancel    - Cancel reservations
reservations.checkin   - Check guests in
reservations.checkout  - Check guests out
reservations.view-own  - View own hotel reservations
reservations.view-all  - View all reservations (admin)
reservations.override  - Create admin override reservations

payments.receive       - Receive payments
payments.refund        - Process refunds
payments.view          - View payment history

reports.view-own       - View own hotel reports
reports.view-all       - View all system reports

system.settings        - Manage system settings
system.logs            - View activity logs
```

---

## 🔗 TABLE 4: ROLE_PERMISSIONS

**Purpose:** Link roles to their permissions (many-to-many relationship)

| Column Name   | Data Type       | Constraints                             | Description            |
| ------------- | --------------- | --------------------------------------- | ---------------------- |
| id            | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT             | Unique link identifier |
| role_id       | BIGINT UNSIGNED | FOREIGN KEY → roles(id), NOT NULL       | Which role             |
| permission_id | BIGINT UNSIGNED | FOREIGN KEY → permissions(id), NOT NULL | Which permission       |
| created_at    | TIMESTAMP       | DEFAULT NOW                             | When assigned          |

**Indexes:**

-   role_id
-   permission_id
-   Composite: (role_id, permission_id) - UNIQUE

**Example:**

-   Role "Hotel Manager" has permissions: `hotels.edit-own`, `rooms.create`, `rooms.edit`, `rooms.change-status`
-   Role "Receptionist" has permissions: `rooms.change-status`, `rooms.view-own`

---

## 👤 TABLE 5: USER_ROLES

**Purpose:** Assign roles to users (many-to-many relationship)

| Column Name | Data Type       | Constraints                       | Description                  |
| ----------- | --------------- | --------------------------------- | ---------------------------- |
| id          | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT       | Unique assignment identifier |
| user_id     | BIGINT UNSIGNED | FOREIGN KEY → users(id), NOT NULL | Which user                   |
| role_id     | BIGINT UNSIGNED | FOREIGN KEY → roles(id), NOT NULL | Which role                   |
| assigned_by | BIGINT UNSIGNED | FOREIGN KEY → users(id)           | Who assigned this role       |
| assigned_at | TIMESTAMP       | DEFAULT NOW                       | When assigned                |

**Indexes:**

-   user_id
-   role_id
-   Composite: (user_id, role_id) - UNIQUE

**Note:** Users can have multiple roles. Permissions are cumulative (union of all role permissions).

---

## 🏨 TABLE 6: HOTELS

**Purpose:** Store hotel information owned by hotel owners

| Column Name | Data Type       | Constraints                       | Description                           |
| ----------- | --------------- | --------------------------------- | ------------------------------------- |
| id          | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT       | Unique hotel identifier               |
| user_id     | BIGINT UNSIGNED | FOREIGN KEY → users(id), NOT NULL | Hotel owner (user_type='hotel_owner') |
| name        | VARCHAR(200)    | NOT NULL                          | Hotel name                            |
| slug        | VARCHAR(200)    | UNIQUE, NOT NULL                  | URL-friendly identifier               |
| address     | TEXT            | NULL                              | Full address                          |
| city        | VARCHAR(100)    | NULL                              | City name                             |
| state       | VARCHAR(100)    | NULL                              | State/Province                        |
| country     | VARCHAR(100)    | NULL                              | Country                               |
| postal_code | VARCHAR(20)     | NULL                              | ZIP/Postal code                       |
| phone       | VARCHAR(20)     | NULL                              | Contact number                        |
| email       | VARCHAR(100)    | NULL                              | Hotel email                           |
| description | TEXT            | NULL                              | Hotel description                     |
| total_rooms | INT             | DEFAULT 0                         | Total number of rooms                 |
| status      | ENUM            | DEFAULT 'active'                  | 'active', 'inactive', 'archived'      |
| created_at  | TIMESTAMP       | DEFAULT NOW                       | Record creation date                  |
| updated_at  | TIMESTAMP       | AUTO UPDATE                       | Last update date                      |

**Indexes:**

-   user_id
-   slug
-   name
-   city
-   status

**Relationships:**

-   ONE hotel owner can have MANY hotels
-   If hotel owner is deleted → all their hotels are archived (not deleted)

---

## 🔗 TABLE 7: USER_HOTEL_ACCESS

**Purpose:** Link users (especially staff) to specific hotels they can manage

| Column Name | Data Type       | Constraints                        | Description                    |
| ----------- | --------------- | ---------------------------------- | ------------------------------ |
| id          | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT        | Unique access record           |
| user_id     | BIGINT UNSIGNED | FOREIGN KEY → users(id), NOT NULL  | User being granted access      |
| hotel_id    | BIGINT UNSIGNED | FOREIGN KEY → hotels(id), NOT NULL | Hotel they can access          |
| granted_by  | BIGINT UNSIGNED | FOREIGN KEY → users(id), NOT NULL  | Hotel owner who granted access |
| granted_at  | TIMESTAMP       | DEFAULT NOW                        | When access was granted        |
| revoked_at  | TIMESTAMP       | NULL                               | When access was revoked        |
| is_active   | BOOLEAN         | DEFAULT TRUE                       | Is access currently active     |

**Indexes:**

-   user_id
-   hotel_id
-   is_active
-   Composite: (user_id, hotel_id)

**Important Rules:**

-   Hotel owners automatically have access to ALL their hotels
-   Staff must be explicitly granted access to specific hotels
-   One staff member can work at multiple hotels
-   Access can be revoked without deleting the user

---

## 🖼️ TABLE 8: HOTEL_IMAGES

**Purpose:** Store multiple images for each hotel

| Column Name   | Data Type       | Constraints                        | Description                       |
| ------------- | --------------- | ---------------------------------- | --------------------------------- |
| id            | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT        | Unique image identifier           |
| hotel_id      | BIGINT UNSIGNED | FOREIGN KEY → hotels(id), NOT NULL | Which hotel this image belongs to |
| image_url     | VARCHAR(500)    | NOT NULL                           | Image file path/URL               |
| image_type    | ENUM            | DEFAULT 'gallery'                  | 'main', 'gallery', 'thumbnail'    |
| display_order | INT             | DEFAULT 0                          | Order for displaying images       |
| uploaded_by   | BIGINT UNSIGNED | FOREIGN KEY → users(id)            | Who uploaded this image           |
| uploaded_at   | TIMESTAMP       | DEFAULT NOW                        | Upload timestamp                  |

**Indexes:**

-   hotel_id
-   image_type

**Relationships:**

-   ONE hotel can have MANY images
-   If hotel is deleted → all images are deleted (CASCADE)

---

## 🚪 TABLE 9: ROOMS

**Purpose:** Store individual room information and status

| Column Name        | Data Type       | Constraints                        | Description                                            |
| ------------------ | --------------- | ---------------------------------- | ------------------------------------------------------ |
| id                 | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT        | Unique room identifier                                 |
| hotel_id           | BIGINT UNSIGNED | FOREIGN KEY → hotels(id), NOT NULL | Which hotel this room belongs to                       |
| room_number        | VARCHAR(50)     | NOT NULL                           | Room number (101, 205, etc.)                           |
| room_type          | VARCHAR(100)    | NULL                               | Single, Double, Suite, etc.                            |
| floor_number       | INT             | NULL                               | Which floor                                            |
| capacity           | INT             | NULL                               | Number of guests                                       |
| description        | TEXT            | NULL                               | Room details                                           |
| **status**         | **ENUM**        | **NOT NULL**                       | **'vacant', 'reserved', 'occupied', 'admin_reserved'** |
| last_status_change | TIMESTAMP       | DEFAULT NOW                        | When status was last changed                           |
| status_updated_by  | BIGINT UNSIGNED | FOREIGN KEY → users(id)            | Who changed the status                                 |
| created_at         | TIMESTAMP       | DEFAULT NOW                        | Room creation date                                     |
| updated_at         | TIMESTAMP       | AUTO UPDATE                        | Last update date                                       |

**Color Mapping:**

-   🟢 **vacant** = Green (Available)
-   🟡 **reserved** = Yellow (Reserved by user)
-   🔴 **occupied** = Red (Guest checked in)
-   🔵 **admin_reserved** = Blue (Reserved by admin)

**Indexes:**

-   hotel_id
-   status
-   room_number
-   Composite: (hotel_id, room_number) - UNIQUE

**Relationships:**

-   ONE hotel can have MANY rooms
-   If hotel is deleted → all rooms are archived (soft delete)

---

## 🖼️ TABLE 10: ROOM_IMAGES

**Purpose:** Store multiple images for each room

| Column Name   | Data Type       | Constraints                       | Description                      |
| ------------- | --------------- | --------------------------------- | -------------------------------- |
| id            | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT       | Unique image identifier          |
| room_id       | BIGINT UNSIGNED | FOREIGN KEY → rooms(id), NOT NULL | Which room this image belongs to |
| image_url     | VARCHAR(500)    | NOT NULL                          | Image file path/URL              |
| display_order | INT             | DEFAULT 0                         | Display order                    |
| uploaded_by   | BIGINT UNSIGNED | FOREIGN KEY → users(id)           | Who uploaded this image          |
| uploaded_at   | TIMESTAMP       | DEFAULT NOW                       | Upload timestamp                 |

**Indexes:**

-   room_id

**Relationships:**

-   ONE room can have MANY images
-   If room is deleted → all images are deleted (CASCADE)

---

## 💼 TABLE 11: GUESTS

**Purpose:** Store guest information for repeat customers

| Column Name     | Data Type       | Constraints                 | Description                                               |
| --------------- | --------------- | --------------------------- | --------------------------------------------------------- |
| id              | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique guest identifier                                   |
| first_name      | VARCHAR(100)    | NOT NULL                    | Guest first name                                          |
| last_name       | VARCHAR(100)    | NOT NULL                    | Guest last name                                           |
| email           | VARCHAR(100)    | UNIQUE                      | Guest email address                                       |
| phone           | VARCHAR(20)     | NULL                        | Primary contact number                                    |
| phone_secondary | VARCHAR(20)     | NULL                        | Secondary contact                                         |
| address         | TEXT            | NULL                        | Guest address                                             |
| city            | VARCHAR(100)    | NULL                        | City                                                      |
| state           | VARCHAR(100)    | NULL                        | State/Province                                            |
| country         | VARCHAR(100)    | NULL                        | Country                                                   |
| postal_code     | VARCHAR(20)     | NULL                        | ZIP/Postal code                                           |
| id_type         | VARCHAR(50)     | NULL                        | ID type (Passport, Driver's License, etc.)                |
| id_number       | VARCHAR(100)    | NULL                        | ID number                                                 |
| date_of_birth   | DATE            | NULL                        | Date of birth                                             |
| nationality     | VARCHAR(100)    | NULL                        | Nationality                                               |
| preferences     | TEXT            | NULL                        | Guest preferences (JSON: room type, floor, smoking, etc.) |
| notes           | TEXT            | NULL                        | Special notes about guest                                 |
| vip_status      | BOOLEAN         | DEFAULT FALSE               | VIP guest flag                                            |
| created_by      | BIGINT UNSIGNED | FOREIGN KEY → users(id)     | Who created this guest record                             |
| hotel_owner_id  | BIGINT UNSIGNED | FOREIGN KEY → users(id)     | Which hotel owner's system this guest belongs to          |
| created_at      | TIMESTAMP       | DEFAULT NOW                 | Record creation date                                      |
| updated_at      | TIMESTAMP       | AUTO UPDATE                 | Last update date                                          |

**Indexes:**

-   email
-   phone
-   hotel_owner_id
-   Composite: (email, hotel_owner_id)

**Key Features:**

-   Guests can be searched quickly for repeat bookings
-   Store preferences for better service
-   Each hotel owner has their own guest database
-   VIP guests can be flagged for special treatment

---

## 📅 TABLE 12: RESERVATIONS

**Purpose:** Main reservations table for all bookings (Hotel Owner, Staff, and Admin)

| Column Name         | Data Type       | Constraints                        | Description                                                                 |
| ------------------- | --------------- | ---------------------------------- | --------------------------------------------------------------------------- |
| id                  | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT        | Unique reservation identifier                                               |
| reservation_number  | VARCHAR(50)     | UNIQUE, NOT NULL                   | Human-readable booking reference (e.g., "RES-2024-001")                     |
| room_id             | BIGINT UNSIGNED | FOREIGN KEY → rooms(id), NOT NULL  | Reserved room                                                               |
| hotel_id            | BIGINT UNSIGNED | FOREIGN KEY → hotels(id), NOT NULL | Hotel of the room                                                           |
| guest_id            | BIGINT UNSIGNED | FOREIGN KEY → guests(id), NOT NULL | Guest information                                                           |
| check_in_date       | DATE            | NOT NULL                           | Expected check-in date                                                      |
| check_out_date      | DATE            | NOT NULL                           | Expected check-out date                                                     |
| actual_check_in     | TIMESTAMP       | NULL                               | Actual check-in time                                                        |
| actual_check_out    | TIMESTAMP       | NULL                               | Actual check-out time                                                       |
| number_of_guests    | INT             | DEFAULT 1                          | Number of guests                                                            |
| reservation_type    | ENUM            | NOT NULL                           | 'regular', 'admin_override'                                                 |
| status              | ENUM            | NOT NULL                           | 'pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show' |
| payment_status      | ENUM            | DEFAULT 'pending'                  | 'pending', 'partial', 'paid', 'refunded'                                    |
| total_amount        | DECIMAL(10,2)   | DEFAULT 0.00                       | Total booking amount                                                        |
| paid_amount         | DECIMAL(10,2)   | DEFAULT 0.00                       | Amount paid so far                                                          |
| special_requests    | TEXT            | NULL                               | Guest special requests                                                      |
| notes               | TEXT            | NULL                               | Internal notes                                                              |
| created_by          | BIGINT UNSIGNED | FOREIGN KEY → users(id), NOT NULL  | Who created this reservation (staff/owner/admin)                            |
| cancelled_by        | BIGINT UNSIGNED | FOREIGN KEY → users(id)            | Who cancelled (if applicable)                                               |
| cancelled_at        | TIMESTAMP       | NULL                               | When cancelled                                                              |
| cancellation_reason | TEXT            | NULL                               | Why cancelled                                                               |
| created_at          | TIMESTAMP       | DEFAULT NOW                        | Reservation creation time                                                   |
| updated_at          | TIMESTAMP       | AUTO UPDATE                        | Last update time                                                            |

**Indexes:**

-   reservation_number
-   room_id
-   hotel_id
-   guest_id
-   check_in_date
-   check_out_date
-   status
-   reservation_type
-   created_by
-   Composite: (hotel_id, check_in_date, status)
-   Composite: (room_id, check_in_date, check_out_date)

**Reservation Types:**

-   **regular** = Normal booking by hotel owner/staff
-   **admin_override** = Super admin reservation (shows as blue, cannot be modified by hotel staff)

**Status Flow:**

```
pending → confirmed → checked_in → checked_out
               ↓
           cancelled / no_show
```

**Key Features:**

-   Complete booking lifecycle tracking
-   Payment tracking (partial/full)
-   Admin reservations are marked and protected
-   Check-in/check-out timestamps
-   Cancellation tracking with reason

---

## 📊 TABLE 13: ADMIN_RESERVATIONS_HISTORY

**Purpose:** Track admin override reservations specifically for the last 30 days (separate view)

| Column Name    | Data Type       | Constraints                              | Description                           |
| -------------- | --------------- | ---------------------------------------- | ------------------------------------- |
| id             | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT              | Unique history identifier             |
| reservation_id | BIGINT UNSIGNED | FOREIGN KEY → reservations(id), NOT NULL | Link to main reservation              |
| admin_id       | BIGINT UNSIGNED | FOREIGN KEY → users(id), NOT NULL        | Super admin who made override         |
| action_type    | ENUM            | NOT NULL                                 | 'created', 'modified', 'released'     |
| action_at      | TIMESTAMP       | DEFAULT NOW                              | When action occurred                  |
| notes          | TEXT            | NULL                                     | Admin notes                           |
| archive_month  | VARCHAR(7)      | NULL                                     | Month in format YYYY-MM for archiving |

**Indexes:**

-   reservation_id
-   admin_id
-   action_at
-   archive_month

**Key Features:**

-   Shows only last 30 days of ADMIN override actions
-   Can be archived monthly
-   Links to main reservations table
-   Tracks admin-specific actions

---

## 📜 TABLE 14: ROOM_STATUS_HISTORY

**Purpose:** Audit trail for all room status changes

| Column Name     | Data Type       | Constraints                       | Description           |
| --------------- | --------------- | --------------------------------- | --------------------- |
| id              | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT       | Unique history record |
| room_id         | BIGINT UNSIGNED | FOREIGN KEY → rooms(id), NOT NULL | Which room            |
| previous_status | ENUM            | NULL                              | Status before change  |
| new_status      | ENUM            | NOT NULL                          | Status after change   |
| changed_by      | BIGINT UNSIGNED | FOREIGN KEY → users(id), NOT NULL | Who made the change   |
| changed_at      | TIMESTAMP       | DEFAULT NOW                       | When it was changed   |
| notes           | TEXT            | NULL                              | Reason for change     |

**Indexes:**

-   room_id
-   changed_at
-   changed_by

**Purpose:**

-   Complete audit trail
-   Track who changed what and when
-   Cannot be edited or deleted (audit security)

---

## 📊 TABLE 15: ACTIVITY_LOGS

**Purpose:** Track all user activities for security and monitoring

| Column Name | Data Type       | Constraints                 | Description                                   |
| ----------- | --------------- | --------------------------- | --------------------------------------------- |
| id          | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique log identifier                         |
| user_id     | BIGINT UNSIGNED | FOREIGN KEY → users(id)     | Which user                                    |
| action_type | VARCHAR(100)    | NOT NULL                    | 'login', 'create_hotel', 'update_room', etc.  |
| entity_type | VARCHAR(50)     | NULL                        | 'user', 'hotel', 'room', 'role', 'permission' |
| entity_id   | BIGINT UNSIGNED | NULL                        | ID of affected entity                         |
| description | TEXT            | NULL                        | Details of the action                         |
| ip_address  | VARCHAR(45)     | NULL                        | User's IP address                             |
| user_agent  | TEXT            | NULL                        | Browser/device info                           |
| created_at  | TIMESTAMP       | DEFAULT NOW                 | When action occurred                          |

**Indexes:**

-   user_id
-   action_type
-   entity_type
-   created_at

**Use Cases:**

-   Security monitoring
-   User activity tracking
-   Compliance reporting
-   Audit trail for role/permission changes

---

## ⚙️ TABLE 16: SYSTEM_SETTINGS

**Purpose:** Store system-wide configuration settings

| Column Name   | Data Type       | Constraints                 | Description               |
| ------------- | --------------- | --------------------------- | ------------------------- |
| id            | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique setting identifier |
| setting_key   | VARCHAR(100)    | UNIQUE, NOT NULL            | Setting name              |
| setting_value | TEXT            | NULL                        | Setting value             |
| description   | TEXT            | NULL                        | What this setting does    |
| updated_at    | TIMESTAMP       | AUTO UPDATE                 | Last update time          |
| updated_by    | BIGINT UNSIGNED | FOREIGN KEY → users(id)     | Admin who updated         |

**Indexes:**

-   setting_key

**Example Settings:**

-   `reservation_archive_days` = 30
-   `auto_archive_enabled` = true
-   `max_upload_size_mb` = 10
-   `allow_hotel_owner_create_roles` = true

---

## 📦 TABLE 17: ADMIN_RESERVATIONS_ARCHIVE

**Purpose:** Store historical admin override actions (older than 30 days)

| Column Name         | Data Type       | Constraints                 | Description                       |
| ------------------- | --------------- | --------------------------- | --------------------------------- |
| id                  | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Archive record ID                 |
| original_history_id | BIGINT UNSIGNED | NULL                        | Original history record ID        |
| reservation_id      | BIGINT UNSIGNED | NULL                        | Reservation that was affected     |
| room_id             | BIGINT UNSIGNED | NULL                        | Room that was reserved            |
| hotel_id            | BIGINT UNSIGNED | NULL                        | Hotel of the room                 |
| admin_id            | BIGINT UNSIGNED | NULL                        | Admin who made override           |
| action_type         | ENUM            | NULL                        | 'created', 'modified', 'released' |
| action_at           | TIMESTAMP       | NULL                        | When action occurred              |
| archive_month       | VARCHAR(7)      | NOT NULL                    | Archive month (YYYY-MM)           |
| archived_at         | TIMESTAMP       | DEFAULT NOW                 | When it was archived              |
| notes               | TEXT            | NULL                        | Admin notes                       |

**Indexes:**

-   archive_month
-   reservation_id
-   room_id
-   hotel_id
-   archived_at

**Purpose:**

-   Keep historical data of admin overrides
-   Can be reset/cleared monthly
-   Useful for reports and analytics
-   Doesn't bloat main tables

---

## 🔗 RELATIONSHIP SUMMARY

```
SUPER ADMIN
  ├── Creates HOTEL OWNERS
  ├── Manages SYSTEM ROLES & PERMISSIONS
  ├── Views ALL HOTELS & ROOMS
  ├── Makes ADMIN OVERRIDE RESERVATIONS (Blue - Cannot be modified by hotel staff)
  ├── Can block/reserve any room across any hotel
  └── Access SYSTEM_SETTINGS

HOTEL OWNER (Can own MULTIPLE hotels)
  ├── Creates HOTELS (unlimited)
  │    ├── HOTEL_IMAGES
  │    └── ROOMS
  │         ├── ROOM_IMAGES
  │         └── Changes ROOM STATUS
  │
  ├── Manages GUESTS database
  ├── Creates RESERVATIONS for customers
  ├── Checks guests IN/OUT
  ├── Tracks PAYMENTS
  ├── Creates CUSTOM ROLES (for their organization)
  ├── Assigns PERMISSIONS to ROLES
  ├── Creates STAFF ACCOUNTS
  ├── Assigns ROLES to STAFF
  ├── Grants HOTEL ACCESS to STAFF
  └── Manages ALL their properties through one account

STAFF
  ├── Has MULTIPLE ROLES (assigned by hotel owner)
  ├── Has CUMULATIVE PERMISSIONS (union of all roles)
  ├── Access SPECIFIC HOTELS (via USER_HOTEL_ACCESS)
  ├── Manages GUESTS (add/edit guest info)
  ├── Creates RESERVATIONS for customers
  ├── Checks guests IN/OUT
  ├── Changes ROOM STATUS based on bookings
  ├── Cannot modify ADMIN OVERRIDE reservations
  └── Can work at MULTIPLE HOTELS

GUESTS
  ├── Stored in GUESTS table
  ├── Can have multiple RESERVATIONS
  ├── Guest info reused for repeat bookings
  └── Preferences and notes tracked

RESERVATIONS (Business Operations)
  ├── Created by Hotel Owner/Staff for guests
  ├── Links to GUEST information
  ├── Tracks check-in/check-out
  ├── Payment tracking
  ├── Room status automatically synced
  ├── Admin override reservations are PROTECTED

ADMIN OVERRIDE RESERVATIONS
  ├── Super admin can reserve any room
  ├── Shows as BLUE in UI
  ├── Hotel owner/staff CANNOT modify or cancel
  ├── Tracked separately in ADMIN_RESERVATIONS_HISTORY
  ├── 30-day history with monthly archive

ROLES & PERMISSIONS (Spatie-Style)
  ├── ROLES have many PERMISSIONS
  ├── USERS have many ROLES
  ├── PERMISSIONS define what users CAN DO
  ├── System checks permissions before allowing actions
  └── Hotel owners create custom roles for their team

AUDIT & HISTORY
  ├── ROOM_STATUS_HISTORY (tracks all status changes)
  ├── ADMIN_RESERVATIONS_HISTORY (30-day admin actions)
  ├── ADMIN_RESERVATIONS_ARCHIVE (historical admin data)
  └── ACTIVITY_LOGS (all user actions + role changes)
```

---

## 🎯 KEY OPERATIONS

### **1. Super Admin Creates Hotel Owner Account**

-   Admin inserts new record into `users` table
-   Set `user_type` = 'hotel_owner'
-   Assign default "Hotel Owner" role from system roles
-   Hotel owner can now log in and create unlimited hotels

### **2. Hotel Owner Creates Multiple Hotels**

-   Hotel owner has ONE account
-   Creates as many hotels as needed (no limit)
-   All hotels linked to their `user_id`
-   Manages all properties through single login

### **3. Hotel Owner Creates Custom Role**

-   Insert into `roles` table
-   Set `scope` = 'hotel_owner'
-   Set `hotel_owner_id` = their user ID
-   Assign permissions via `role_permissions` table
-   Example: Create "Night Shift Manager" role with specific permissions

### **4. Hotel Owner Creates Staff Account**

-   Insert into `users` table
-   Set `user_type` = 'staff'
-   Set `parent_user_id` = hotel owner's ID
-   Assign one or more ROLES to the staff
-   Grant access to specific hotels

### **5. Hotel Owner Assigns Staff to Hotels**

-   Insert into `user_hotel_access` table
-   Link staff to one or more hotels
-   Staff can work at multiple properties
-   Example: Receptionist works at Hotel A and Hotel B

### **6. Staff/Owner Adds Guest Information**

-   Insert into `guests` table
-   Store all guest details, preferences, ID info
-   Link to `hotel_owner_id` for data isolation
-   Guest can be reused for future bookings

### **7. Staff/Owner Creates Reservation for Guest**

-   Check if room is available for dates
-   Check if room has admin override (if yes, DENY)
-   Insert into `reservations` table
-   Set `reservation_type` = 'regular'
-   Link to `guest_id`, `room_id`, `hotel_id`
-   Set `status` = 'pending' or 'confirmed'
-   Update room `status` to 'reserved' (yellow)
-   Record in `room_status_history`

### **8. Guest Checks In**

-   Update reservation `status` = 'checked_in'
-   Set `actual_check_in` timestamp
-   Update room `status` to 'occupied' (red)
-   Record in `room_status_history`

### **9. Guest Checks Out**

-   Update reservation `status` = 'checked_out'
-   Set `actual_check_out` timestamp
-   Update room `status` to 'vacant' (green)
-   Record payment if not already paid
-   Record in `room_status_history`

### **10. Super Admin Makes Override Reservation**

-   Admin can reserve ANY room (even if already booked)
-   Insert into `reservations` table
-   Set `reservation_type` = 'admin_override'
-   Insert record into `admin_reservations_history`
-   Update room `status` to 'admin_reserved' (blue)
-   Hotel staff CANNOT modify this reservation
-   Hotel staff see this room as "blocked by admin"

### **11. Hotel Staff Tries to Modify Admin Override Room**

-   System checks `reservation_type` on the room
-   If `admin_override` → DENY action
-   Show message: "This room is reserved by system admin"
-   Only super admin can release or modify

### **12. Super Admin Releases Override Reservation**

-   Update reservation `status` = 'cancelled' or 'checked_out'
-   Insert 'released' action in `admin_reservations_history`
-   Update room `status` back to 'vacant' (green)
-   Hotel staff can now use the room

### **13. System Checks Permission Before Action**

```
1. User tries to perform action (e.g., "edit room")
2. System gets all user's roles from `user_roles`
3. System gets all permissions from those roles via `role_permissions`
4. System checks if required permission exists (e.g., "rooms.edit")
5. If hotel-specific: check `user_hotel_access` for that hotel
6. Allow or deny action
```

### **14. Staff Changes Room Status Manually**

-   Check if user has `rooms.change-status` permission
-   Check if user has access to this hotel via `user_hotel_access`
-   Check if room has active admin override (if yes, DENY)
-   Update `status` in `rooms` table
-   Record change in `room_status_history`
-   Log action in `activity_logs`

### **15. Hotel Owner Views All Their Hotels**

-   Query `hotels` WHERE `user_id` = owner's ID
-   Shows ALL hotels they own in one dashboard
-   Can switch between properties easily
-   Aggregate statistics across all properties

### **10. Monthly Archive (Automated)**

-   Move reservations older than 30 days
-   Insert into `admin_reservations_archive`
-   Delete old records from main table

---

## 🔐 PERMISSION CHECKING FLOW

### **How to Check if User Can Perform Action:**

```sql
-- Example: Check if user can edit a specific room

-- Step 1: Get user's roles
SELECT r.* FROM roles r
JOIN user_roles ur ON r.id = ur.role_id
WHERE ur.user_id = {user_id};

-- Step 2: Get all permissions from those roles
SELECT DISTINCT p.name FROM permissions p
JOIN role_permissions rp ON p.id = rp.permission_id
JOIN user_roles ur ON rp.role_id = ur.role_id
WHERE ur.user_id = {user_id};

-- Step 3: Check specific permission
SELECT COUNT(*) FROM permissions p
JOIN role_permissions rp ON p.id = rp.permission_id
JOIN user_roles ur ON rp.role_id = ur.role_id
WHERE ur.user_id = {user_id}
AND p.name = 'rooms.edit';

-- Step 4: Check hotel access (if not hotel owner)
SELECT COUNT(*) FROM user_hotel_access
WHERE user_id = {user_id}
AND hotel_id = {hotel_id}
AND is_active = TRUE;

-- If all checks pass → ALLOW action
```

---

## 📊 DEFAULT SYSTEM ROLES & PERMISSIONS

### **System Roles (Created by Super Admin)**

**1. Super Admin Role**

-   All permissions (wildcard: `*`)

**2. Hotel Owner Role**

-   `users.create` - Create staff accounts
-   `users.edit` - Edit staff details
-   `users.delete` - Delete staff accounts
-   `users.view-own` - View their staff
-   `hotels.create` - Create unlimited hotels
-   `hotels.edit-own` - Edit their hotels
-   `hotels.delete-own` - Delete their hotels
-   `hotels.view-own` - View their hotels
-   `rooms.create` - Add rooms
-   `rooms.edit` - Edit rooms
-   `rooms.delete` - Delete rooms
-   `rooms.change-status` - Change room status
-   `guests.create` - Add guests
-   `guests.edit` - Edit guest information
-   `guests.view-own` - View their guests
-   `reservations.create` - Create bookings
-   `reservations.edit-own` - Edit their reservations
-   `reservations.cancel` - Cancel bookings
-   `reservations.checkin` - Check guests in
-   `reservations.checkout` - Check guests out
-   `reservations.view-own` - View their reservations
-   `payments.receive` - Receive payments
-   `payments.view` - View payment history
-   `roles.create` - Create custom roles
-   `roles.edit-own` - Edit their custom roles
-   `roles.delete-own` - Delete their custom roles
-   `permissions.assign` - Assign permissions to roles
-   `reports.view-own` - View their reports

**3. Default Staff Roles (Hotel Owner can customize)**

**Manager Role:**

-   `hotels.view-own` - View assigned hotels
-   `rooms.create` - Add rooms
-   `rooms.edit` - Edit rooms
-   `rooms.delete` - Delete rooms
-   `rooms.change-status` - Change status
-   `guests.create` - Add guests
-   `guests.edit` - Edit guests
-   `guests.view-own` - View guests
-   `reservations.create` - Create bookings
-   `reservations.edit-own` - Edit bookings
-   `reservations.cancel` - Cancel bookings
-   `reservations.checkin` - Check in
-   `reservations.checkout` - Check out
-   `reservations.view-own` - View bookings
-   `payments.receive` - Receive payments
-   `payments.view` - View payments
-   `reports.view-own` - View reports

**Receptionist Role:**

-   `hotels.view-own` - View assigned hotels
-   `rooms.view-own` - View rooms
-   `rooms.change-status` - Change status only
-   `guests.create` - Add guests
-   `guests.edit` - Edit guests
-   `guests.view-own` - View guests
-   `reservations.create` - Create bookings
-   `reservations.view-own` - View bookings
-   `reservations.checkin` - Check in
-   `reservations.checkout` - Check out
-   `payments.receive` - Receive payments

**Housekeeping Role:**

-   `rooms.view-own` - View assigned rooms
-   `rooms.change-status` - Update cleaning status only
-   Note: Usually changes status from 'occupied' to 'vacant' after cleaning

---

## 🎯 EXAMPLE USE CASES

### **Use Case 1: Hotel Owner with 5 Hotels Running Full Operations**

```
User: "John Doe" (user_type: hotel_owner)

Hotels Owned:
  - Grand Hotel Downtown (200 rooms)
  - Beach Resort (150 rooms)
  - Mountain Lodge (80 rooms)
  - City Suites (120 rooms)
  - Airport Inn (60 rooms)

Staff Created: 25 employees across all properties
Custom Roles: "Night Manager", "Head Receptionist", "Maintenance Supervisor"

Guest Database: 5,000+ guests with preferences and history
Active Reservations: 350+ bookings across all hotels
Revenue Tracking: Per hotel and consolidated

Daily Operations:
- Staff checks guests in/out
- Receptionists create bookings
- Housekeeping updates room status after cleaning
- Managers handle payments
- John views dashboard with all 5 hotels
- Generates reports across all properties
```

### **Use Case 2: Staff Member Working Across Multiple Hotels**

```
User: "Sarah Smith" (user_type: staff)
Parent: "John Doe" (hotel owner)
Roles Assigned: "Receptionist" role

Hotels Assigned:
  - Grand Hotel Downtown (works Mon-Wed)
  - Beach Resort (works Thu-Fri)

Daily Tasks:
- Sarah logs in → Sees both hotels
- At Grand Hotel: Checks in guest, creates reservation
- At Beach Resort: Updates room status, processes payment
- Can view guests from both hotels
- Cannot access other 3 hotels owned by John
- Cannot modify admin override rooms (blue)
- All actions logged with hotel context
```

### **Use Case 3: Guest Returning to Hotel**

```
Guest: "Michael Johnson"
Previous Stay: Checked in 6 months ago at Grand Hotel Downtown

New Booking Process:
1. Receptionist searches: "Michael Johnson"
2. System shows existing guest record with:
   - Contact info
   - Previous stays
   - Room preferences (non-smoking, high floor)
   - Special notes (allergic to feather pillows)
3. Receptionist creates new reservation using existing guest record
4. No need to re-enter all information
5. Better guest experience with personalized service
```

### **Use Case 4: Admin Override Scenario**

```
Situation: Government official needs secure accommodation

Super Admin Action:
1. Super admin logs in
2. Views ALL hotels in system
3. Selects premium suite at Beach Resort
4. Creates admin override reservation
5. Room shows as BLUE (admin_reserved)
6. Reservation type = 'admin_override'

Hotel Staff Experience:
- Receptionist sees room is blue/blocked
- Message: "Reserved by System Admin"
- Cannot create booking for this room
- Cannot change room status
- Cannot cancel or modify

When Complete:
- Super admin releases room
- Room becomes available (green)
- Hotel can use it for regular business
```

---

## 🔐 SECURITY & ACCESS CONTROL

### **Super Admin Permissions:**

✅ Full system access (all permissions)
✅ Create/edit/delete hotel owners
✅ Manage system roles and permissions
✅ View all hotels, rooms, users
✅ Make admin reservations (blue status)
✅ Access system settings and logs
✅ Override any action

### **Hotel Owner Permissions:**

✅ Create unlimited hotels
✅ Manage ALL their hotels from one account
✅ Create custom roles for their organization
✅ Assign permissions to custom roles
✅ Create staff accounts
✅ Assign multiple roles to staff
✅ Grant staff access to specific hotels
✅ Staff can work at multiple properties
✅ View their own analytics and reports
❌ Cannot access other owners' hotels
❌ Cannot modify system roles
❌ Cannot see admin reservations history

### **Staff Permissions:**

✅ Based on assigned roles (cumulative permissions)
✅ Can have multiple roles
✅ Access only hotels explicitly granted
✅ Can work at multiple hotels
✅ Perform actions based on permissions
❌ Cannot access hotels not assigned to them
❌ Cannot modify admin-reserved rooms (blue)
❌ Cannot create other users
❌ Cannot modify their own roles

### **Permission Inheritance:**

-   Users can have MULTIPLE roles
-   Permissions are CUMULATIVE (union of all roles)
-   If any role grants permission → User has permission
-   Explicit DENY overrides ALLOW (if implemented)

---

## 📈 PERFORMANCE OPTIMIZATION

1. **Indexes** on foreign keys and frequently queried columns
2. **Composite indexes** for permission checks
3. **Caching** - Cache user permissions in Redis
4. **Eager loading** - Load roles and permissions together
5. **Permission gate** - Check permissions before queries
6. **Archive mechanism** - Keep tables lean
7. **Soft deletes** - Don't actually delete important data

---

## 🔄 MONTHLY MAINTENANCE

**Automatic Tasks (Run on 1st of each month):**

1. Archive admin reservations older than 30 days
2. Clean up revoked hotel access records
3. Generate monthly reports per hotel owner
4. Audit unused roles and permissions

**Manual Cleanup Options:**

-   Hotel owners can delete unused custom roles
-   Admin can archive inactive hotel owners
-   Clear old activity logs (keep 1 year)

---

## ✨ ADVANTAGES OF SPATIE-STYLE SYSTEM

**1. Maximum Flexibility**

-   Hotel owners create roles specific to their needs
-   No hardcoded limitations
-   Easy to add new permissions

**2. Scalability**

-   Works for 1 hotel or 1000 hotels
-   One account manages multiple properties
-   Staff can work across properties

**3. Granular Control**

-   Permissions at feature level
-   Mix and match permissions
-   Create specialized roles

**4. Easy Maintenance**

-   Add new features → Add new permissions
-   Update permissions without code changes
-   Roles are reusable

**5. Multi-Tenancy Ready**

-   Each hotel owner is isolated
-   Custom roles don't affect other owners
-   Secure data separation

**6. Audit-Friendly**

-   Track who has what permissions
-   Log all permission changes
-   Complete activity history

---

## 🚀 IMPLEMENTATION NOTES

### **Backend Middleware (Permission Check)**

```javascript
// Before any action
function checkPermission(userId, permissionName, hotelId = null) {
    // 1. Get user's roles
    const roles = getUserRoles(userId);

    // 2. Get all permissions from those roles
    const permissions = getRolePermissions(roles);

    // 3. Check if permission exists
    if (!permissions.includes(permissionName)) {
        return false; // No permission
    }

    // 4. If hotel-specific action, check hotel access
    if (hotelId) {
        return checkHotelAccess(userId, hotelId);
    }

    return true; // Has permission
}
```

### **Caching Strategy**

```
- Cache user permissions for 1 hour
- Invalidate cache when roles/permissions change
- Store in Redis for fast access
- Key: user:{user_id}:permissions
```

### **Database Queries Optimization**

```sql
-- Create indexes
CREATE INDEX idx_user_roles_user ON user_roles(user_id);
CREATE INDEX idx_role_permissions_role ON role_permissions(role_id);
CREATE INDEX idx_user_hotel_access_user_hotel ON user_hotel_access(user_id, hotel_id, is_active);
```

This design gives you complete flexibility while maintaining security and scalability! 🎉
