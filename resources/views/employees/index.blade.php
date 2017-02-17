@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="pull-left">View Employees</h1>
            </div>
        </div>
        <hr/>
        <?= $grid ?>
    </div>
@endsection