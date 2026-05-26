@props(['id' => null, 'maxWidth' => null, 'placement' => null])

@php
    $id = $id ?? md5($attributes->wire('model') ?? $attributes->get('x-model') ?? uniqid());

    $maxWidth = [
        'sm' => 'w-72 sm:w-80',
        'md' => 'w-80 sm:w-96',
        'lg' => 'w-96 sm:w-[28rem]',
        'xl' => 'w-full sm:w-[32rem]',
        '2xl' => 'w-full sm:w-[40rem]',
        '3xl' => 'w-full sm:w-[48rem]',
        '4/5' => 'w-full sm:w-4/5',
    ][$maxWidth ?? 'lg'];

    $placement = $placement ?? 'right';
    
    // Position classes based on placement (right/left)
    $positionClasses = $placement === 'left' ? 'left-0' : 'right-0';
    
    // Transition classes based on placement
    $transitionEnterStart = $placement === 'left' ? '-translate-x-full' : 'translate-x-full';
    $transitionLeaveEnd = $placement === 'left' ? '-translate-x-full' : 'translate-x-full';
@endphp

<div
    @if($attributes->wire('model')->value())
        x-data="{ show: @entangle($attributes->wire('model')) }"
    @else
        x-data="{ show: false }"
        x-modelable="show"
    @endif
    {{ $attributes->only('x-model') }}
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    id="{{ $id }}"
    class="fixed inset-0 z-50 overflow-hidden"
    style="display: none;"
>
    <!-- Overlay Backdrop -->
    <div x-show="show" 
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
         @click="show = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
    ></div>

    <!-- Drawer Content -->
    <div x-show="show" 
         class="fixed top-0 {{ $positionClasses }} h-full bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto transform transition-all {{ $maxWidth }}"
         x-trap.inert.noscroll="show"
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="{{ $transitionEnterStart }}"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="{{ $transitionLeaveEnd }}"
    >
        {{ $slot }}
    </div>
</div>
