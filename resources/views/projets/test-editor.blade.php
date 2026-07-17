@extends('layouts.app')

@section('title', 'Éditeur de Tests — {{ $template->name }}')

@section('content')
    <livewire:excel-test-editor :project="$project" :template="$template" />
    <livewire:ai-test-case-generator :project="$project" :template="$template" />
@endsection
