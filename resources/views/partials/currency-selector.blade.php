@php
    $currencies = \App\Models\Currency::active()->get();
    $currentCurrency = \App\Helpers\Helpers::getCurrentCurrency();
@endphp

@if($currencies->count() > 1)
<div class="currency-selector-wrapper" style="position: relative; display: inline-block; margin-left: 8px;">
    <button class="currency-trigger" id="currencyTrigger" type="button" aria-haspopup="true" aria-expanded="false" style="border: 1px solid #6041E0 !important;">
        <span class="currency-flag-circle">
            {{ $currentCurrency?->symbol ?? '$' }}
        </span>
        <span class="currency-current-code">{{ $currentCurrency?->code ?? 'USD' }}</span>
        <i class="fas fa-chevron-down arrow-icon"></i>
    </button>

    <div class="currency-popover" id="currencyPopover">
        <div class="popover-header">
            Select Currency
        </div>
        <div class="popover-content">
            @foreach($currencies as $currency)
                <a href="{{ route('currency.switch', $currency->id) }}" class="currency-item {{ $currentCurrency->id == $currency->id ? 'active' : '' }}">
                    <div class="currency-item-left">
                        <span class="currency-symbol-box">{{ $currency->symbol }}</span>
                        <div class="currency-details">
                            <span class="currency-code-text">{{ $currency->code }}</span>
                            <span class="currency-name-text">{{ $currency->name }}</span>
                        </div>
                    </div>
                    @if($currentCurrency->id == $currency->id)
                        <i class="fas fa-check text-success"></i>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
    .currency-trigger {
        background: transparent;
        border: 1px solid #e0e0e0;
        border-radius: 50px;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #1a1a1a;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 14px;
    }

    .currency-trigger:hover, .currency-trigger.open {
        background: #f8f9fa;
        border-color: #6041E0;
        color: #6041E0;
    }

    .currency-flag-circle {
        width: 20px;
        height: 20px;
        background: #6041E0;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }

    .arrow-icon {
        font-size: 10px;
        transition: transform 0.2s ease;
        color: #888;
    }

    .currency-trigger.open .arrow-icon {
        transform: rotate(180deg);
        color: #6041E0;
    }

    /* Popover Styles */
    .currency-popover {
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 240px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        border: 1px solid #f0f0f0;
        z-index: 1000;
        overflow: hidden;
        animation: popoverFadeIn 0.2s ease-out;
    }

    .currency-popover.show {
        display: block;
    }

    @keyframes popoverFadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .popover-header {
        padding: 12px 16px;
        background: #f9fafb;
        border-bottom: 1px solid #f0f0f0;
        font-size: 12px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .popover-content {
        max-height: 300px;
        overflow-y: auto;
    }

    .currency-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        text-decoration: none;
        color: #1a1a1a;
        transition: background 0.2s;
        border-bottom: 1px solid #f9f9f9;
    }

    .currency-item:last-child {
        border-bottom: none;
    }

    .currency-item:hover {
        background-color: #f3f4f6;
    }

    .currency-item.active {
        background-color: #f0fdf4; /* Light green tint for active */
    }

    .currency-item-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .currency-symbol-box {
        width: 32px;
        height: 32px;
        background: #f3f4f6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #4b5563;
        font-size: 14px;
    }

    .currency-item.active .currency-symbol-box {
        background: #6041E0;
        color: white;
    }

    .currency-details {
        display: flex;
        flex-direction: column;
    }

    .currency-code-text {
        font-weight: 700;
        font-size: 14px;
        line-height: 1.2;
    }

    .currency-name-text {
        font-size: 11px;
        color: #6b7280;
    }

    .text-success {
        color: #10b981;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .currency-current-code {
            display: none;
        }
        .currency-trigger {
            padding: 6px 8px;
            gap: 4px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.querySelector('.currency-selector-wrapper');
    const trigger = document.getElementById('currencyTrigger');
    const popover = document.getElementById('currencyPopover');

    if (trigger && popover) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            popover.classList.toggle('show');
            trigger.classList.toggle('open');
            trigger.setAttribute('aria-expanded', popover.classList.contains('show'));
        });

        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                popover.classList.remove('show');
                trigger.classList.remove('open');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>
@endif