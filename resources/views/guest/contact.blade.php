@extends('layouts.guest')

@section('title', 'Contact Us')

@section('content')
<div class="container mx-auto px-4 py-10 space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">Contact Us</h1>
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-3">
        <p class="text-sm text-gray-700">Email: info@university.edu</p>
        <p class="text-sm text-gray-700">Phone: +123 456 7890</p>
        <p class="text-sm text-gray-700">Address: 123 University Avenue, City, Country</p>
        <p class="text-sm text-gray-700">
            Follow us:
            <a href="#" class="text-blue-600 hover:underline">Facebook</a>,
            <a href="#" class="text-blue-600 hover:underline">Twitter</a>,
            <a href="#" class="text-blue-600 hover:underline">Instagram</a>
        </p>
    </div>
</div>
@endsection

