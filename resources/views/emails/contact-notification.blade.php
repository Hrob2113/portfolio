<x-mail::message>
# New Contact Message

**From:** {{ $contactMessage->name }} ({{ $contactMessage->email }})

@if($contactMessage->subject)
**Subject:** {{ $contactMessage->subject }}
@endif

---

{{ $contactMessage->message }}

---

<x-mail::button :url="route('admin.messages.show', $contactMessage)">
View in Admin
</x-mail::button>

This message was sent via your portfolio contact form.
</x-mail::message>
