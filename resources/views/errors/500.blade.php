@extends('errors.layout')

@section('title', 'Erreur interne du serveur')
@section('code', '500')

@section('icon')
<svg style="width:40px;height:40px;color:#8b0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>
@endsection

@section('message')
    Une erreur inattendue est survenue de notre côté.<br>
    L'équipe technique a été notifiée.
@endsection
