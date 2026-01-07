@extends('layouts.guest')

@section('title', 'About Us')

@section('content')
<div class="container mx-auto px-4 py-10 space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">About the University</h1>
    <p class="text-gray-700 leading-relaxed">
        We are committed to academic excellence, innovation, and student success. Our mission is to provide quality education and foster a vibrant learning community.
    </p>
    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Mission</h2>
            <p class="mt-2 text-sm text-gray-700">
                Empower learners to achieve their potential through research-driven teaching and inclusive academic support.
            </p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Vision</h2>
            <p class="mt-2 text-sm text-gray-700">
                To be a leader in academic excellence and research, preparing graduates to create positive impact globally.
            </p>
        </div>
    </div>
</div>
@endsection

