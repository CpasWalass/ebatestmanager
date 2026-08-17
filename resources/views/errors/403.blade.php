@extends('errors.layout')

@section('title', 'Accès refusé')
@section('code', '403')

@section('icon')
<svg style="width:40px;height:40px;color:#8b0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
</svg>
@endsection

@section('message')
    Vous n'avez pas les permissions nécessaires pour accéder à cette page.
@endsection
