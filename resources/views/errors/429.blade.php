@extends('errors.layout')

@section('title', 'Trop de requêtes')
@section('code', '429')

@section('icon')
<svg style="width:40px;height:40px;color:#8b0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
</svg>
@endsection

@section('message')
    Vous avez effectué trop de tentatives en peu de temps.<br>
    Merci de patienter un instant avant de réessayer.
@endsection
