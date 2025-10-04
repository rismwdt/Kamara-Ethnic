@extends('layouts.client')

@section('title','Kamara Ethnic')

@section('content')

@include('klien.home')
@include('klien.tentang-kami')
@include('klien.paket-acara', ['events' => $events])
@include('klien.modal-pesanan')

@endsection
