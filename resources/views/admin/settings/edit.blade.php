<x-admin-layout title="Settings">

<div class="admin-header">
  <h1>Settings</h1>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
  @csrf
  @method('PUT')

  @foreach($settings as $group => $items)
    <div class="card">
      <div class="card-title">{{ ucfirst($group) }}</div>
      <div class="grid-2">
        @foreach($items as $setting)
          <div class="form-group">
            <label class="form-label" for="s-{{ $setting->key }}">{{ str_replace('_', ' ', $setting->key) }}</label>
            <input
              class="form-input"
              type="{{ $setting->key === 'contact_email' ? 'email' : 'url' }}"
              id="s-{{ $setting->key }}"
              name="settings[{{ $setting->key }}]"
              value="{{ old('settings.'.$setting->key, $setting->value) }}"
            >
            @error('settings.'.$setting->key)
              <p style="color:#fca5a5;font-size:12px;margin-top:4px">{{ $message }}</p>
            @enderror
          </div>
        @endforeach
      </div>
    </div>
  @endforeach

  <button type="submit" class="btn btn-primary">Save settings</button>
</form>

</x-admin-layout>
