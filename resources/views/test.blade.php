@extends('layouts.app')

@push('custome-css')
<style>
    h1 {
        color: green;
    }
</style>
@endpush

@section('content')
<h1>Test Main</h1>
<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Possimus, explicabo.</p>
@endsection


@section('aside')
<h1>Test Aside</h1>
<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Possimus, explicabo.</p>
@endsection


@push('custome-js')
<script>
    
</script>
@endpush
