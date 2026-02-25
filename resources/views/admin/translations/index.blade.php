<x-admin-layout title="Translations">

<div class="admin-header">
  <h1>Translations</h1>
</div>

<div class="grid-3">
  @foreach($groups as $group)
    <a href="{{ route('admin.translations.edit', $group->group) }}" class="card" style="text-decoration:none">
      <div class="card-title">{{ $group->group }}</div>
      <p style="color:var(--mid);font-size:13px">{{ intdiv($group->total, 2) }} keys &middot; {{ $group->total }} entries</p>
    </a>
  @endforeach
</div>

</x-admin-layout>
