<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Portfolio CMS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root{--bg:#06060A;--amber:#C41208;--text:#EDE6DA;--mid:rgba(237,230,218,.52);--low:rgba(237,230,218,.26);--border:rgba(237,230,218,.09);--surface:rgba(255,255,255,.04);--fd:'Barlow Condensed',sans-serif;--fm:'IBM Plex Mono',monospace}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:var(--bg);color:var(--text);font-family:var(--fm);font-size:14px;line-height:1.6;min-height:100vh;display:flex;align-items:center;justify-content:center}
.login-card{width:100%;max-width:400px;background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:40px}
.login-brand{font-family:var(--fd);font-size:24px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;text-align:center;margin-bottom:32px}
.login-brand span{color:var(--amber)}
.field{margin-bottom:20px}
.field label{display:block;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--low);margin-bottom:6px}
.field input[type="email"],.field input[type="password"]{
  width:100%;background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:8px;
  padding:10px 14px;color:var(--text);font-family:var(--fm);font-size:13px;transition:border-color .15s;
}
.field input:focus{outline:none;border-color:rgba(196,18,8,.4)}
.remember{display:flex;align-items:center;gap:8px;margin-bottom:20px;font-size:12px;color:var(--mid)}
.remember input{accent-color:var(--amber)}
.login-btn{
  width:100%;padding:12px;border:none;border-radius:8px;background:var(--amber);color:#fff;
  font-family:var(--fm);font-size:12px;letter-spacing:.06em;text-transform:uppercase;
  cursor:pointer;transition:opacity .15s;
}
.login-btn:hover{opacity:.85}
.error{color:#fca5a5;font-size:12px;margin-top:6px}
.status{background:rgba(16,185,129,.1);color:#6ee7b7;border:1px solid rgba(16,185,129,.2);padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:20px}
.forgot{display:block;text-align:center;margin-top:16px;font-size:12px;color:var(--low);transition:color .15s}
.forgot:hover{color:var(--text)}
</style>
</head>
<body>
<div class="login-card">
  <div class="login-brand"><span>CMS</span> Admin</div>

  @if(session('status'))
    <div class="status">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="field">
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
      @error('email') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input id="password" type="password" name="password" required autocomplete="current-password">
      @error('password') <p class="error">{{ $message }}</p> @enderror
    </div>

    <label class="remember">
      <input type="checkbox" name="remember"> Remember me
    </label>

    <button type="submit" class="login-btn">Log in</button>
  </form>

  @if(Route::has('password.request'))
    <a href="{{ route('password.request') }}" class="forgot">Forgot your password?</a>
  @endif
</div>
</body>
</html>
