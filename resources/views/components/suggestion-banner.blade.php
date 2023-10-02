@if(session()->has('localizedSuggestedLocale'))
    <div {{ $attributes }} class="localized-suggestion-banner">

        <div class="close-btn">
            <form method="post" action="{{ route('localized.locale.update') }}">
                @csrf
                <input type="hidden" name="locale" value="{{ app()->getLocale() }}" />
                <button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="content-layout">
            <div class="content">
                <div class="content-text">
                    <div class="content-question">
                        <div>
                            {{ __('localized::messages.suggestion', ['current' => __('localized::locales.' . app()->getLocale()), 'suggested' => __('localized::locales.' . session('localizedSuggestedLocale'))]) }}
                        </div>
                        <div>
                            {{ __('localized::messages.suggestion', ['current' => __('localized::locales.' . app()->getLocale(), [], session('localizedSuggestedLocale')), 'suggested' => __('localized::locales.' . session('localizedSuggestedLocale'), [], session('localizedSuggestedLocale'))], session('localizedSuggestedLocale')) }}
                        </div>
                    </div>
                    <div class="btn-container">
                        <form method="post" action="{{ route('localized.locale.update') }}">
                            @csrf
                            <input type="hidden" name="locale" value="{{ app()->getLocale() }}" />
                            <button class="btn" type="submit">{{ ucfirst(__('localized::locales.' . app()->getLocale())) }}</button>
                        </form>
                        <form method="post" action="{{ route('localized.locale.update') }}">
                            @csrf
                            <input type="hidden" name="locale" value="{{ session('localizedSuggestedLocale') }}" />
                            <button class="btn btn-primary" type="submit">{{ ucfirst(__('localized::locales.' . session('localizedSuggestedLocale'), [], session('localizedSuggestedLocale'))) }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    .localized-suggestion-banner {
        position: sticky;
        top: 0;
        background-color: white;
        z-index: 99;
        padding: 1rem 1.5rem;
    }

    .localized-suggestion-banner .close-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        margin: 3px;
    }

    .localized-suggestion-banner .content-layout {
        max-width: 80rem;
        margin: auto;
    }
    .localized-suggestion-banner .content-layout .content {
        display: flex;
        justify-content: space-between;
    }
    .localized-suggestion-banner .content-layout .content .content-text .content-question {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 1rem;
        max-width: 900px;
    }
    .localized-suggestion-banner .content-layout .content .content-text .btn-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }
    .localized-suggestion-banner .content-layout .content .content-text .btn-container .btn {
        border: 1px solid black;
        padding: 0.3rem;
        border-radius: 5px;
        font-weight: bold;
    }
    .localized-suggestion-banner .content-layout .content .content-text .btn-container .btn-primary {
        background-color: black;
        color: white;
    }

    @media (min-width: 640px) {
        .localized-suggestion-banner .content-layout .content .content-text {
            display: flex;
            gap: 2rem;
            flex-grow: 1;
            justify-content: space-between;
            padding-right: 1rem;
        }
        .localized-suggestion-banner .content-layout .content .content-text .content-question {
            margin-bottom: 0;
        }
    }
    @media (min-width: 768px) {
        .content {
            padding: 1rem 2rem;
        }
        .localized-suggestion-banner .content-layout .content .content-text .btn-container {
            gap: 1rem;
        }
    }
    @media (min-width: 1280px) {
        .localized-suggestion-banner .content-layout .content .content-text .content-question {
            flex-direction: row;
            align-self: center;
        }
    }

    @media (prefers-color-scheme: dark) {
        .localized-suggestion-banner {
            background-color: black;
            color: white;
        }
        .localized-suggestion-banner .content-layout .content .content-text .btn-container .btn {
            border: 1px solid white;
        }
        .localized-suggestion-banner .content-layout .content .content-text .btn-container .btn-primary {
            background-color: white;
            color: black;
        }
    }
</style>
