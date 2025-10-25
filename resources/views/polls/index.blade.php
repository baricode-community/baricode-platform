@extends('components.layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Your Polls</h1>
        @livewire('poll.manage-polls')
    </div>
@endsection