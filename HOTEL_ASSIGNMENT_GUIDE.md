# Hotel Assignment Guide

This document explains how hotels are assigned to users in the Hotel SaaS platform.

## Overview

In this SaaS platform, there are two types of hotel assignments:

1. **Hotel Ownership** - Hotels belong to hotel owners (via `user_id` in `hotels` table)
2. **Staff Access** - Staff members get access to specific hotels (via `user_hotel_access` table)

---

## 1. Assigning Hotels to Hotel Owners

### For Super Admins

Super admins can assign hotels to any hotel owner when creating or editing a hotel.

#### Creating a Hotel

1. Navigate to **Hotels** → **Add New Hotel**
2. In the create form, you'll see a **"Hotel Owner"** dropdown (only visible to super admins)
3. Select the hotel owner from the dropdown
4. Fill in the hotel details and submit

The hotel will be assigned to the selected hotel owner.

#### Changing Hotel Ownership

1. Navigate to **Hotels** → Click on a hotel → **Edit**
2. In the edit form, you'll see a **"Hotel Owner"** dropdown (only visible to super admins)
3. Select a different hotel owner from the dropdown
4. Save the changes

The hotel ownership will be transferred to the new owner.

### For Hotel Owners

When a hotel owner creates a hotel:
- The hotel is **automatically assigned** to them
- They cannot assign hotels to other owners
- They can only create hotels for themselves

---

## 2. Granting Staff Access to Hotels

Staff members don't own hotels, but they can be granted access to specific hotels to manage rooms and reservations.

### How to Grant Hotel Access to Staff

1. Navigate to **Users** → Find the staff member → Click **View**
2. Scroll to the **"Accessible Hotels"** section
3. Click **"Manage Hotel Access"** button (or **"Grant Hotel Access"** if they have no access)
4. You'll see a list of all your hotels with checkboxes
5. Check the hotels you want to grant access to
6. Click **"Update Hotel Access"**

### Who Can Manage Staff Hotel Access?

- **Hotel Owners**: Can manage hotel access for their own staff members (users where `parent_user_id` = their ID)
- **Super Admins**: Can manage hotel access for any staff member

### Access Control

- Staff can only see and manage hotels they have been granted access to
- Hotel owners can only grant access to hotels they own
- Super admins can grant access to any hotel

---

## 3. Viewing Hotel Assignments

### View Hotels Owned by a User

1. Navigate to **Users** → Find a hotel owner → Click **View**
2. Scroll to the **"Owned Hotels"** section
3. You'll see a table listing all hotels owned by that user

### View Hotels Accessible to Staff

1. Navigate to **Users** → Find a staff member → Click **View**
2. Scroll to the **"Accessible Hotels"** section
3. You'll see a table listing all hotels the staff member can access

### View Hotel Owner

1. Navigate to **Hotels** → Click on a hotel → **View**
2. In the **"Owner Information"** section, you'll see the hotel owner's details

---

## 4. Database Structure

### Hotels Table
- `user_id` (foreign key to `users.id`) - The hotel owner

### User Hotel Access Table
- `user_id` (foreign key to `users.id`) - The staff member
- `hotel_id` (foreign key to `hotels.id`) - The hotel they can access
- `is_active` - Whether the access is currently active
- `granted_by` - Who granted the access
- `granted_at` - When access was granted

---

## 5. Permissions

### Hotel Creation
- `hotels.create` - Required to create hotels
- Super admins can assign hotels to any owner
- Hotel owners can only create hotels for themselves

### Hotel Editing
- `hotels.edit-own` - Edit own hotels
- `hotels.edit-all` - Edit any hotel (super admin)
- Super admins can change hotel ownership

### Staff Hotel Access
- Hotel owners can manage access for their staff
- Super admins can manage access for any staff

---

## 6. Important Notes

1. **Hotel Ownership vs. Access**: 
   - Hotel owners **own** hotels (they appear in the "Owned Hotels" section)
   - Staff members **access** hotels (they appear in the "Accessible Hotels" section)

2. **Super Admin Privileges**:
   - Can assign hotels to any hotel owner
   - Can change hotel ownership
   - Can grant staff access to any hotel

3. **Hotel Owner Limitations**:
   - Can only create hotels for themselves
   - Can only grant staff access to their own hotels
   - Cannot change hotel ownership

4. **Staff Limitations**:
   - Cannot own hotels
   - Can only access hotels explicitly granted to them
   - Cannot grant access to other staff

---

## 7. Common Workflows

### Workflow 1: Super Admin Creates Hotel for Owner
1. Super admin logs in
2. Goes to Hotels → Add New Hotel
3. Selects hotel owner from dropdown
4. Fills in hotel details
5. Hotel is created and assigned to selected owner

### Workflow 2: Hotel Owner Creates Hotel
1. Hotel owner logs in
2. Goes to Hotels → Add New Hotel
3. Fills in hotel details (no owner selection needed)
4. Hotel is automatically assigned to them

### Workflow 3: Hotel Owner Grants Staff Access
1. Hotel owner creates staff account
2. Goes to Users → Staff member → View
3. Clicks "Manage Hotel Access"
4. Checks hotels to grant access
5. Staff can now manage those hotels

### Workflow 4: Transfer Hotel Ownership
1. Super admin goes to Hotels → Hotel → Edit
2. Changes hotel owner in dropdown
3. Hotel ownership is transferred
4. Previous owner loses access (unless they're super admin)
5. New owner gains full control

---

## 8. Troubleshooting

### Issue: "Hotel Owner" dropdown is empty
**Solution**: Ensure there are active hotel owner users in the system. Super admins can create hotel owner accounts in the Users section.

### Issue: Staff member can't see hotels
**Solution**: 
1. Check if the staff member has been granted access to hotels
2. Go to Users → Staff member → Manage Hotel Access
3. Ensure at least one hotel is checked

### Issue: Can't change hotel owner
**Solution**: Only super admins can change hotel ownership. If you're a hotel owner, contact a super admin.

### Issue: Hotel owner can't grant staff access
**Solution**: 
1. Ensure the staff member's `parent_user_id` is set to the hotel owner's ID
2. Ensure the hotel owner has created at least one hotel
3. Check that you have the necessary permissions

---

## Summary

- **Super Admins**: Can assign hotels to any owner and manage all staff access
- **Hotel Owners**: Own hotels automatically when created, can grant staff access to their hotels
- **Staff**: Access hotels through explicit grants from their parent hotel owner

The system ensures clear separation between ownership and access, making it easy to manage multi-tenant hotel operations in a SaaS environment.


