@extends('shared.view_main')

@section('content')
<div class="container">

    <h2>Adoption Certificate</h2>

    <p><strong>Pet Name:</strong> {{ $adoption->pet->name }}</p>
    <p><strong>Breed:</strong> {{ $adoption->pet->breed }}</p>

    <hr>

    <p><strong>Adopted By:</strong>
        {{ $adoption->adopter->first_name }} {{ $adoption->adopter->last_name }}
    </p>

    <p><strong>Given By:</strong>
        {{ $adoption->giver->first_name }} {{ $adoption->giver->last_name }}
    </p>

    <p><strong>Date:</strong> {{ $adoption->adoption_date }}</p>

    @if($adoption->admin)
        <p><strong>Approved By:</strong>
            {{ $adoption->admin->first_name }} {{ $adoption->admin->last_name }}
        </p>
    @endif

    <br><br>

    <button onclick="window.print()">Print / Save as PDF</button>

</div>
@endsection