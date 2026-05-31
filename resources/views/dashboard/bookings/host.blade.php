@extends('layouts.dashboard')
@section('title', 'Host Bookings Dashboard — EstateYard')
@section('page-title', 'Host Bookings')
@section('page-subtitle', 'Manage incoming stays for your properties')

@section('content')
@include('bookings.host-bookings')
@endsection
