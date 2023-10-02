<form method="post" action="{{ route('localized.locale.update') }}">
    @csrf
    <select onchange="this.form.submit()" name="locale" {{ $attributes }}>
        @foreach (ALajusticia\Localized\Localized::availableLanguages() as $locale => $language)
            <option value="{{ $locale }}" @selected(\Illuminate\Support\Facades\App::getLocale() == $locale)>
                    {{ ucfirst($language) }}
            </option>
        @endforeach
    </select>
</form>
