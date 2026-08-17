@extends('errors.layout')

@section('title', 'Page introuvable')
@section('code', '404')

@section('icon')
<svg style="width:40px;height:40px;color:#8b0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection

@section('message')
    Cette page n'existe pas ou a été déplacée.<br>
    Vérifiez l'URL ou revenez à l'accueil.
@endsection
