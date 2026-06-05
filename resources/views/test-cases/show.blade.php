@extends('layouts.app')

@section('title', 'Tableur — ' . $template->name)

@section('content')
    <livewire:excel-test-editor :project="$project" :template="$template" />
@endsection
