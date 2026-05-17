@extends('layouts.app')
@section('title', __('booking.title'))
@section('page-title', __('booking.title'))

@section('content')
    <livewire:schedule-grid :studioId="$selectedStudio->id" />
@endsection

@push('scripts')
<script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush
