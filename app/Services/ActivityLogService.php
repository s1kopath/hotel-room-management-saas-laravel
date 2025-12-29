<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public function log(
        string $actionType,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        ?Request $request = null
    ): ActivityLog {
        $request = $request ?? request();

        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action_type' => $actionType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Log login activity
     */
    public function logLogin(?string $username = null): ActivityLog
    {
        $description = $username 
            ? "User logged in with username: {$username}" 
            : "User logged in";

        return $this->log('login', 'user', Auth::id(), $description);
    }

    /**
     * Log logout activity
     */
    public function logLogout(): ActivityLog
    {
        return $this->log('logout', 'user', Auth::id(), 'User logged out');
    }

    /**
     * Log hotel creation
     */
    public function logHotelCreated(int $hotelId, string $hotelName): ActivityLog
    {
        return $this->log(
            'create_hotel',
            'hotel',
            $hotelId,
            "Created hotel: {$hotelName}"
        );
    }

    /**
     * Log hotel update
     */
    public function logHotelUpdated(int $hotelId, string $hotelName): ActivityLog
    {
        return $this->log(
            'update_hotel',
            'hotel',
            $hotelId,
            "Updated hotel: {$hotelName}"
        );
    }

    /**
     * Log hotel deletion
     */
    public function logHotelDeleted(int $hotelId, string $hotelName): ActivityLog
    {
        return $this->log(
            'delete_hotel',
            'hotel',
            $hotelId,
            "Deleted hotel: {$hotelName}"
        );
    }

    /**
     * Log room creation
     */
    public function logRoomCreated(int $roomId, string $roomNumber): ActivityLog
    {
        return $this->log(
            'create_room',
            'room',
            $roomId,
            "Created room: {$roomNumber}"
        );
    }

    /**
     * Log room update
     */
    public function logRoomUpdated(int $roomId, string $roomNumber): ActivityLog
    {
        return $this->log(
            'update_room',
            'room',
            $roomId,
            "Updated room: {$roomNumber}"
        );
    }

    /**
     * Log room status change
     */
    public function logRoomStatusChanged(int $roomId, string $roomNumber, string $oldStatus, string $newStatus): ActivityLog
    {
        return $this->log(
            'change_room_status',
            'room',
            $roomId,
            "Changed room {$roomNumber} status from {$oldStatus} to {$newStatus}"
        );
    }

    /**
     * Log guest creation
     */
    public function logGuestCreated(int $guestId, string $guestName): ActivityLog
    {
        return $this->log(
            'create_guest',
            'guest',
            $guestId,
            "Created guest: {$guestName}"
        );
    }

    /**
     * Log guest update
     */
    public function logGuestUpdated(int $guestId, string $guestName): ActivityLog
    {
        return $this->log(
            'update_guest',
            'guest',
            $guestId,
            "Updated guest: {$guestName}"
        );
    }

    /**
     * Log reservation creation
     */
    public function logReservationCreated(int $reservationId, string $reservationNumber): ActivityLog
    {
        return $this->log(
            'create_reservation',
            'reservation',
            $reservationId,
            "Created reservation: {$reservationNumber}"
        );
    }

    /**
     * Log reservation update
     */
    public function logReservationUpdated(int $reservationId, string $reservationNumber): ActivityLog
    {
        return $this->log(
            'update_reservation',
            'reservation',
            $reservationId,
            "Updated reservation: {$reservationNumber}"
        );
    }

    /**
     * Log check-in
     */
    public function logCheckIn(int $reservationId, string $reservationNumber): ActivityLog
    {
        return $this->log(
            'check_in',
            'reservation',
            $reservationId,
            "Checked in guest for reservation: {$reservationNumber}"
        );
    }

    /**
     * Log check-out
     */
    public function logCheckOut(int $reservationId, string $reservationNumber): ActivityLog
    {
        return $this->log(
            'check_out',
            'reservation',
            $reservationId,
            "Checked out guest for reservation: {$reservationNumber}"
        );
    }

    /**
     * Log reservation cancellation
     */
    public function logReservationCancelled(int $reservationId, string $reservationNumber, ?string $reason = null): ActivityLog
    {
        $description = "Cancelled reservation: {$reservationNumber}";
        if ($reason) {
            $description .= " - Reason: {$reason}";
        }

        return $this->log(
            'cancel_reservation',
            'reservation',
            $reservationId,
            $description
        );
    }

    /**
     * Log role creation
     */
    public function logRoleCreated(int $roleId, string $roleName): ActivityLog
    {
        return $this->log(
            'create_role',
            'role',
            $roleId,
            "Created role: {$roleName}"
        );
    }

    /**
     * Log role update
     */
    public function logRoleUpdated(int $roleId, string $roleName): ActivityLog
    {
        return $this->log(
            'update_role',
            'role',
            $roleId,
            "Updated role: {$roleName}"
        );
    }

    /**
     * Log role deletion
     */
    public function logRoleDeleted(int $roleId, string $roleName): ActivityLog
    {
        return $this->log(
            'delete_role',
            'role',
            $roleId,
            "Deleted role: {$roleName}"
        );
    }

    /**
     * Log user creation
     */
    public function logUserCreated(int $userId, string $username): ActivityLog
    {
        return $this->log(
            'create_user',
            'user',
            $userId,
            "Created user: {$username}"
        );
    }

    /**
     * Log user update
     */
    public function logUserUpdated(int $userId, string $username): ActivityLog
    {
        return $this->log(
            'update_user',
            'user',
            $userId,
            "Updated user: {$username}"
        );
    }

    /**
     * Log hotel access granted
     */
    public function logHotelAccessGranted(int $staffId, int $hotelId, string $staffName, string $hotelName): ActivityLog
    {
        return $this->log(
            'grant_hotel_access',
            'user_hotel_access',
            $staffId,
            "Granted hotel access to {$staffName} for hotel: {$hotelName}"
        );
    }

    /**
     * Log hotel access revoked
     */
    public function logHotelAccessRevoked(int $staffId, int $hotelId, string $staffName, string $hotelName): ActivityLog
    {
        return $this->log(
            'revoke_hotel_access',
            'user_hotel_access',
            $staffId,
            "Revoked hotel access from {$staffName} for hotel: {$hotelName}"
        );
    }
}

