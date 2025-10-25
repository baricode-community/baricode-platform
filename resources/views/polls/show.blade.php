@extends('components.layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('polls.index') }}" class="text-blue-600 hover:underline">
                &larr; Back to Polls
            </a>
        </div>
        <livewire:poll.vote-poll :poll="$poll" />
    </div>
@endsection