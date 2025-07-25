@extends('front.front_layouts')

@section('content')

    <section class="relative pt-24 pb-20 overflow-hidden border-b">
        <div class="container mx-auto px-4 mb-12">
            {!! clean($frontPageDetails->page_content) !!}
        </div>
    </section>

    @include('front.sections.call_to_action')
@endsection
