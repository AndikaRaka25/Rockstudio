@extends('layouts.app')
@section('title', __('messages.nav_schedule'))
@section('page-title', __('messages.nav_schedule'))
@section('content')
    <livewire:schedule-grid :studioId="$selectedStudio->id" />
@endsection
