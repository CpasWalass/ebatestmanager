@extends('errors.layout')

@section('title', 'Non autorisé')
@section('code', '401')

@section('icon')
<svg style="width:40px;height:40px;color:#8b0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
</svg>
@endsection

@section('message')
    Vous devez être connecté pour accéder à cette ressource.
@endsection
