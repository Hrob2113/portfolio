<x-admin-layout title="Edit — {{ $group }}">

<div class="admin-header">
  <h1>{{ $group }}</h1>
  <a href="{{ route('admin.translations.index') }}" class="btn btn-ghost">Back</a>
</div>

<form method="POST" action="{{ route('admin.translations.update', $group) }}">
  @csrf
  @method('PUT')

  <div class="card">
    <div style="display:grid;grid-template-columns:200px 1fr 1fr;gap:16px;padding-bottom:8px;border-bottom:1px solid var(--border);margin-bottom:4px">
      <div class="form-label" style="margin:0;padding-top:0">Key</div>
      <div class="form-label" style="margin:0;padding-top:0">English</div>
      <div class="form-label" style="margin:0;padding-top:0">Czech</div>
    </div>

    @php $index = 0; @endphp
    @foreach($translations as $key => $locales)
      <div class="trans-row">
        <div class="trans-key">{{ $key }}</div>
        @foreach(['en', 'cs'] as $locale)
          @php $translation = $locales->firstWhere('locale', $locale); @endphp
          @if($translation)
            <div>
              <input type="hidden" name="translations[{{ $index }}][id]" value="{{ $translation->id }}">
              <textarea class="form-textarea" name="translations[{{ $index }}][value]" rows="2">{{ $translation->value }}</textarea>
            </div>
            @php $index++; @endphp
          @else
            <div style="color:var(--low);padding-top:10px;font-size:12px">—</div>
          @endif
        @endforeach
      </div>
    @endforeach
  </div>

  <button type="submit" class="btn btn-primary">Save &amp; compile</button>
</form>

</x-admin-layout>
