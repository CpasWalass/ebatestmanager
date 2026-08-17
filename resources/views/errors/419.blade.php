@extends('errors.layout')

@section('title', 'Session expirée')
@section('code', '419')

@section('icon')
<svg style="width:40px;height:40px;color:#8b0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
</svg>
@endsection

@section('message')
    Votre session a expiré ou le jeton de sécurité est invalide.<br>
    Merci de réessayer.
@endsection

@section('actions')
<a href="/login" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold">
    Se reconnecter
</a>
@endsection
