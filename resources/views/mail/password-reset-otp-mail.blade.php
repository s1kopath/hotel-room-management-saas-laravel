<x-mail::message>
# Hello {{ $user->name }}

Your OTP is <strong>{{ $otp }}</strong> to reset your password for {{ config('app.name') }}. If you did not request this, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
