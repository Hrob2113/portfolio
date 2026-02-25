<x-admin-layout title="Dashboard">

<div class="admin-header">
  <h1>Dashboard</h1>
</div>

<div class="stats">
  <div class="stat">
    <div class="stat-value">{{ $unreadCount }}</div>
    <div class="stat-label">Unread Messages</div>
  </div>
  <div class="stat">
    <div class="stat-value">{{ \App\Models\Translation::count() }}</div>
    <div class="stat-label">Translations</div>
  </div>
  <div class="stat">
    <div class="stat-value">{{ \App\Models\Setting::count() }}</div>
    <div class="stat-label">Settings</div>
  </div>
</div>

<div class="card">
  <div class="card-title">Recent Messages</div>
  @if($recentMessages->isEmpty())
    <p style="color:var(--low);font-size:13px">No messages yet.</p>
  @else
    <table class="admin-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Subject</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($recentMessages as $msg)
          <tr>
            <td><a href="{{ route('admin.messages.show', $msg) }}" style="color:var(--text)">{{ $msg->name }}</a></td>
            <td>{{ $msg->email }}</td>
            <td>{{ $msg->subject ?? '—' }}</td>
            <td>{{ $msg->created_at->format('M d, Y') }}</td>
            <td>
              @if($msg->is_read)
                <span class="badge-read">Read</span>
              @else
                <span class="badge-unread">New</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>

<div class="grid-3">
  <a href="{{ route('admin.translations.index') }}" class="card" style="text-decoration:none">
    <div class="card-title">Translations</div>
    <p style="color:var(--mid);font-size:13px">Edit site content in EN &amp; CS</p>
  </a>
  <a href="{{ route('admin.settings.edit') }}" class="card" style="text-decoration:none">
    <div class="card-title">Settings</div>
    <p style="color:var(--mid);font-size:13px">Social links &amp; contact info</p>
  </a>
  <a href="{{ route('admin.messages.index') }}" class="card" style="text-decoration:none">
    <div class="card-title">Messages</div>
    <p style="color:var(--mid);font-size:13px">View contact form submissions</p>
  </a>
</div>

</x-admin-layout>
