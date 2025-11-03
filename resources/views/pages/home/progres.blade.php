@extends('layouts.base')

@section('title', 'Belajar Ngoding Gratis - 100% Gratis')

@section('content')
    <section class="py-24 px-4 bg-gradient-to-br from-indigo-800 via-purple-800 to-pink-800 relative overflow-hidden">
        <!-- Decorative Blobs -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-pink-400 opacity-30 rounded-full blur-3xl -z-10 animate-pulse">
        </div>
        <div
            class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-400 opacity-30 rounded-full blur-3xl -z-10 animate-pulse">
        </div>
        <div class="max-w-5xl mx-auto relative z-10">
            <h2 class="text-4xl md:text-5xl font-extrabold text-center mb-10 text-white drop-shadow-lg animate-fade-in-up">
                Progres Komunitas: <span
                    class="bg-gradient-to-r from-pink-400 via-purple-400 to-indigo-400 bg-clip-text text-transparent">Sedang
                    Apa Kita?</span>
            </h2>
            <div class="flex flex-col md:flex-row items-center justify-center gap-12">
                <div
                    class="rounded-2xl shadow-2xl border-4 border-white/30 overflow-hidden bg-white/10 backdrop-blur-lg animate-fade-in-up-2 w-full md:w-[90%]">
                    <iframe src="https://app.xmind.com/embed/FLALuwuV?sheet-id=a4f2eff2-3ca8-47f1-85f1-7c7464dac1e9"
                        width="100%" height="420" frameborder="0" scrolling="no" allowfullscreen
                        class="rounded-2xl w-full" loading="lazy" referrerpolicy="no-referrer"></iframe>
                </div>
            </div>
        </div>
    </section>
@endsection
