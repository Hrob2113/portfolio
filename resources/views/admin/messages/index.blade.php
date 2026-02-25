<x-admin-layout title="Messages">

<div class="admin-header">
  <h1>Messages</h1>
</div>

<div class="card">
  @if($messages->isEmpty())
    <p style="color:var(--low);font-size:13px">No messages yet.</p>
  @else
    <table class="admin-table">
      <thead>
        <tr>
          <th>Status</th>
          <th>Name</th>
          <th>Email</th>
          <th>Subject</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($messages as $msg)
          <tr>
            <td>
              @if($msg->is_read)
                <span class="badge-read">Read</span>
              @else
                <span class="badge-unread">New</span>
              @endif
            </td>
            <td style="{{ $msg->is_read ? '' : 'color:var(--text);font-weight:500' }}">{{ $msg->name }}</td>
            <td>{{ $msg->email }}</td>
            <td>{{ $msg->subject ?? '—' }}</td>
            <td>{{ $msg->created_at->format('M d, Y H:i') }}</td>
            <td><a href="{{ route('admin.messages.show', $msg) }}" class="btn btn-ghost" style="padding:6px 12px;font-size:11px">View</a></td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="pagination-wrap">
      {{ $messages->links() }}
    </div>
  @endif
</div>

</x-admin-layout>
