@props(['title' => 'Admin'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title }} — Portfolio CMS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Crimson+Pro:wght@300;400&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #06060A;
  --teal: #1A5060;
  --orange: #E84020;
  --amber: #C41208;
  --cream: #EAD8B8;
  --text: #EDE6DA;
  --mid: rgba(237,230,218,.52);
  --low: rgba(237,230,218,.26);
  --border: rgba(237,230,218,.09);
  --surface: rgba(255,255,255,.04);
  --fd: 'Barlow Condensed', sans-serif;
  --fb: 'Crimson Pro', Georgia, serif;
  --fm: 'IBM Plex Mono', monospace;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{
  background:var(--bg);
  color:var(--text);
  font-family:var(--fm);
  font-size:14px;
  line-height:1.6;
  min-height:100vh;
  display:flex;
}
a{color:inherit;text-decoration:none}

.admin-sidebar{
  width:240px;min-height:100vh;
  background:rgba(255,255,255,.02);
  border-right:1px solid var(--border);
  padding:24px 0;position:fixed;top:0;left:0;
  display:flex;flex-direction:column;
}
.admin-brand{
  font-family:var(--fd);font-size:18px;font-weight:800;
  letter-spacing:.08em;text-transform:uppercase;
  padding:0 24px 24px;border-bottom:1px solid var(--border);margin-bottom:8px;
}
.admin-brand span{color:var(--amber)}
.admin-nav{list-style:none;padding:8px 12px;flex:1}
.admin-nav li a{
  display:flex;align-items:center;gap:10px;
  padding:10px 12px;border-radius:8px;font-size:13px;
  letter-spacing:.04em;color:var(--mid);transition:background .15s,color .15s;
}
.admin-nav li a:hover{background:rgba(255,255,255,.06);color:var(--text)}
.admin-nav li a.active{background:rgba(196,18,8,.12);color:var(--text)}
.admin-nav .badge{
  margin-left:auto;background:var(--amber);color:#fff;
  font-size:11px;padding:1px 7px;border-radius:99px;font-weight:500;
}
.admin-user{
  padding:16px 24px;border-top:1px solid var(--border);
  font-size:12px;color:var(--low);display:flex;align-items:center;justify-content:space-between;
}
.admin-user form button{
  background:none;border:none;color:var(--mid);cursor:pointer;
  font-family:var(--fm);font-size:12px;padding:4px 8px;border-radius:4px;transition:background .15s;
}
.admin-user form button:hover{background:rgba(255,255,255,.06)}

.admin-main{margin-left:240px;flex:1;padding:32px 40px;min-height:100vh}
.admin-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:32px}
.admin-header h1{font-family:var(--fd);font-size:28px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}

.card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;margin-bottom:20px}
.card-title{font-family:var(--fd);font-size:16px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:16px;color:var(--text)}

.stats{display:flex;gap:20px;margin-bottom:32px}
.stat{flex:1;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px 24px}
.stat-value{font-family:var(--fd);font-size:32px;font-weight:800;color:var(--text)}
.stat-label{font-size:12px;color:var(--mid);margin-top:4px;letter-spacing:.06em;text-transform:uppercase}

.admin-table{width:100%;border-collapse:collapse}
.admin-table th{text-align:left;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--low);padding:10px 12px;border-bottom:1px solid var(--border)}
.admin-table td{padding:12px;border-bottom:1px solid var(--border);font-size:13px;color:var(--mid)}
.admin-table tr:hover td{background:rgba(255,255,255,.02)}

.form-group{margin-bottom:20px}
.form-label{display:block;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--low);margin-bottom:6px}
.form-input,.form-textarea{
  width:100%;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:8px;
  padding:10px 14px;color:var(--text);font-family:var(--fm);font-size:13px;transition:border-color .15s;
}
.form-input:focus,.form-textarea:focus{outline:none;border-color:rgba(196,18,8,.4)}
.form-textarea{min-height:80px;resize:vertical}

.btn{
  display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:8px;
  font-family:var(--fm);font-size:12px;letter-spacing:.06em;text-transform:uppercase;
  border:none;cursor:pointer;transition:opacity .15s,transform .1s;
}
.btn:hover{opacity:.85}
.btn:active{transform:scale(.98)}
.btn-primary{background:var(--amber);color:#fff}
.btn-danger{background:rgba(239,68,68,.15);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}
.btn-ghost{background:rgba(255,255,255,.06);color:var(--mid);border:1px solid var(--border)}

.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px}
.alert-success{background:rgba(16,185,129,.1);color:#6ee7b7;border:1px solid rgba(16,185,129,.2)}

.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}

.badge-read{color:rgba(16,185,129,.7);font-size:12px}
.badge-unread{color:var(--amber);font-size:12px;font-weight:500}

.pagination-wrap{margin-top:24px;display:flex;justify-content:center}
.pagination-wrap nav{font-size:13px}
.pagination-wrap nav span,.pagination-wrap nav a{padding:6px 12px;border-radius:6px;margin:0 2px}
.pagination-wrap nav a{color:var(--mid);background:var(--surface);border:1px solid var(--border)}
.pagination-wrap nav a:hover{background:rgba(255,255,255,.08)}
.pagination-wrap nav span.font-medium{display:none}
.pagination-wrap nav .relative span[aria-current]{background:var(--amber);color:#fff;border-radius:6px;padding:6px 12px}

.trans-row{display:grid;grid-template-columns:200px 1fr 1fr;gap:16px;align-items:start;padding:12px 0;border-bottom:1px solid var(--border)}
.trans-key{font-size:12px;color:var(--low);padding-top:10px;word-break:break-all}
</style>
</head>
<body>

@php $unreadCount = \App\Models\ContactMessage::query()->unread()->count(); @endphp

<aside class="admin-sidebar">
  <div class="admin-brand"><span>CMS</span> Admin</div>
  <ul class="admin-nav">
    <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li><a href="{{ route('admin.translations.index') }}" class="{{ request()->routeIs('admin.translations.*') ? 'active' : '' }}">Translations</a></li>
    <li><a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">Settings</a></li>
    <li>
      <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
        Messages
        @if($unreadCount > 0)
          <span class="badge">{{ $unreadCount }}</span>
        @endif
      </a>
    </li>
  </ul>
  <div class="admin-user">
    <span>{{ Auth::user()->name }}</span>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit">Logout</button>
    </form>
  </div>
</aside>

<main class="admin-main">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{ $slot }}
</main>

</body>
</html>
