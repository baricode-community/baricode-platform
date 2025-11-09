@extends('components.layouts.app')

@section('title', 'Undang Teman - ' . $habit->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <livewire:habit-invite :habit="$habit" />
</div>
@endsection