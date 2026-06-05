@extends('layouts.app')

@section('title', 'Cas de Tests — {{ $project->name }}')

@section('content')
    <livewire:test-case-manager :project="$project" />
@endsection
