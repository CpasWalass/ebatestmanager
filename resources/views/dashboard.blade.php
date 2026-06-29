@extends('layouts.app')

@section('title', 'Dashboard - EbaTestManager')

@section('breadcrumb')
    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Dashboard</a>
    <span class="text-gray-400">/</span>
    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Admin</a>
@endsection

@section('content')
    <livewire:admin-dashboard />
@endsection
