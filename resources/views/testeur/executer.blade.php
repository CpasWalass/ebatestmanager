@extends('layouts.app')

@section('title', 'Exécution — ' . $template->name)

@section('content')
    <livewire:excel-test-editor :project="$project" :template="$template" />
@endsection
