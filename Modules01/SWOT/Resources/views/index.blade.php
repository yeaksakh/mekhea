@extends('swot::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('swot.name') !!}
    </p>
@endsection
