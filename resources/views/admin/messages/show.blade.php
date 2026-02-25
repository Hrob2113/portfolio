<x-admin-layout title="Message from {{ $message->name }}">

<div class="admin-header">
  <h1>Message</h1>
  <a href="{{ route('admin.messages.index') }}" class="btn btn-ghost">Back</a>
</div>

<div class="card">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
    <div>
      <div class="form-label">From</div>
      <div style="color:var(--text)">{{ $message->name }}</div>
    </div>
    <div>
      <div class="form-label">Email</div>
      <div><a href="mailto:{{ $message->email }}" style="color:var(--amber)">{{ $message->email }}</a></div>
    </div>
    <div>
      <div class="form-label">Subject</div>
      <div style="color:var(--text)">{{ $message->subject ?? '—' }}</div>
    </div>
    <div>
      <div class="form-label">Date</div>
      <div style="color:var(--mid)">{{ $message->created_at->format('M d, Y \a\t H:i') }}</div>
    </div>
  </div>

  <div class="form-label">Message</div>
  <div style="background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:8px;padding:16px;margin-top:8px;white-space:pre-wrap;font-size:14px;line-height:1.7;color:var(--text)">{{ $message->message }}</div>
</div>

<div style="display:flex;gap:12px">
  @unless($message->is_read)
    <form method="POST" action="{{ route('admin.messages.read', $message) }}">
      @csrf
      @method('PATCH')
      <button type="submit" class="btn btn-primary">Mark as read</button>
    </form>
  @endunless

  <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">Delete</button>
  </form>
</div>

</x-admin-layout>
