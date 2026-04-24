{{-- Custom dropdown so each model can show a brand logo (native <option> cannot). --}}
@php
    $hiddenId = $hiddenId ?? 'aiModelValue';
    $simpleIconsBase = 'https://cdn.jsdelivr.net/npm/simple-icons@v13/icons/';
    $initialKey = array_key_exists($questAiDefault, $questAiModels) ? $questAiDefault : array_key_first($questAiModels);
    $initialMeta = $questAiModels[$initialKey] ?? [];
    $initialFa = $initialMeta['icon_fa'] ?? '';
    $initialSlug = $initialMeta['brand_slug'] ?? '';
    $initialIconUrl = ($initialFa === '' && $initialSlug !== '') ? $simpleIconsBase . $initialSlug . '.svg' : '';
@endphp
<div class="quest-ai-model-picker" data-quest-ai-picker>
    <input type="hidden" class="js-quest-ai-model" id="{{ $hiddenId }}" value="{{ $initialKey }}" autocomplete="off">
    <button type="button" class="quest-ai-model-trigger form-control" data-quest-ai-trigger aria-haspopup="listbox" aria-expanded="false" title="{{ $title ?? 'Choose AI model' }}">
        <span class="quest-ai-model-trigger-inner" data-quest-ai-trigger-inner aria-hidden="true">
            @if ($initialFa !== '')
                <i class="{{ $initialFa }} quest-ai-model-logo-fa" aria-hidden="true"></i>
            @elseif ($initialIconUrl !== '')
                <img src="{{ $initialIconUrl }}" alt="" class="quest-ai-model-logo" width="22" height="22" loading="eager" decoding="async">
            @else
                <i class="fas fa-robot quest-ai-model-logo-fa" aria-hidden="true"></i>
            @endif
            <span>{{ $initialMeta['label'] ?? '' }}</span>
        </span>
        <i class="fas fa-chevron-down quest-ai-model-chevron" aria-hidden="true"></i>
    </button>
    <ul class="quest-ai-model-menu" data-quest-ai-menu role="listbox" hidden>
        @foreach ($questAiModels as $key => $meta)
            @php
                $fa = $meta['icon_fa'] ?? '';
                $slug = $meta['brand_slug'] ?? '';
                $iconUrl = ($fa === '' && $slug !== '') ? $simpleIconsBase . $slug . '.svg' : '';
            @endphp
            <li role="option"
                tabindex="-1"
                class="quest-ai-model-option"
                data-value="{{ $key }}"
                data-label="{{ e($meta['label']) }}">
                @if ($fa !== '')
                    <i class="{{ $fa }} quest-ai-model-logo-fa" aria-hidden="true"></i>
                @elseif ($iconUrl !== '')
                    <img src="{{ $iconUrl }}" alt="" class="quest-ai-model-logo" width="22" height="22" loading="lazy" decoding="async">
                @else
                    <i class="fas fa-robot quest-ai-model-logo-fa" aria-hidden="true"></i>
                @endif
                <span>{{ $meta['label'] }}</span>
            </li>
        @endforeach
    </ul>
</div>
