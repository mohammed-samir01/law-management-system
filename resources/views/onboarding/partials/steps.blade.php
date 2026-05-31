@php $active = $active ?? 1; @endphp
<div class="flex items-center justify-center gap-2 sm:gap-4 mb-12">
    @foreach([1 => 'اختر خطتك', 2 => 'بيانات المكتب', 3 => 'تم'] as $num => $label)
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $active >= $num ? 'bg-gold text-white' : 'bg-gray-200 text-gray-400' }}">
                @if($active > $num)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @else
                    {{ $num }}
                @endif
            </div>
            <span class="text-sm font-medium hidden sm:inline {{ $active >= $num ? 'text-navy' : 'text-gray-400' }}">{{ $label }}</span>
        </div>
        @if($num < 3)
            <div class="w-8 sm:w-16 h-0.5 {{ $active > $num ? 'bg-gold' : 'bg-gray-200' }}"></div>
        @endif
    @endforeach
</div>
