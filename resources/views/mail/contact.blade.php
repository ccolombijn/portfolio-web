<x-mail::message>
# New Contact Form Submission

**From:** {{ $formData['name'] }} <br>
**Email:** {{ $formData['email'] }} <br>
**Mobile:** {{ $formData['mobile'] }}

---

**Message:**<br>
{{ $formData['message'] }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>