@props([
    'width' => null,
    'height' => null,
    'format' => 'WebP, PNG, JPG',
    'max' => '2MB',
    'note' => null
])

<div {{ $attributes->merge(['class' => 'image-dimension-help d-flex align-items-center gap-1.5 mt-1.5 text-muted small']) }} style="font-size: 0.78rem;">
    <i class="fa-solid fa-circle-info text-primary opacity-75"></i>
    <span>
        প্রস্তাবিত সাইজ: 
        <strong class="text-dark">
            @if($width && $height)
                {{ $width }}x{{ $height }} px
            @elseif($width)
                প্রস্থ: {{ $width }} px
            @elseif($height)
                উচ্চতা: {{ $height }} px
            @else
                স্ট্যান্ডার্ড অনুপাত
            @endif
        </strong>
        <span class="text-secondary opacity-75">({{ $format }}, সর্বোচ্চ {{ $max }})</span>
        @if($note)
            • <span class="text-primary">{{ $note }}</span>
        @endif
    </span>
</div>
