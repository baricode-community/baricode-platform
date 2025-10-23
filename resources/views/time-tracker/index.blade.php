@extends('components.layouts.app')

@section('title', 'Time Tracker')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Time Tracker</h1>
        <p class="text-gray-600 dark:text-gray-400">Track your project time like a pro</p>
    </div>

    @livewire('time-tracker.time-tracker-main')
</div>
@endsection
