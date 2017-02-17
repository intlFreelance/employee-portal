@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
                <div class="panel-body">
                    @if(Auth::user()->hasRole('employee'))
                        <h5>The status of you checklist is
                        @if(isset(Auth::user()->employeeChecklist) && Auth::user()->employeeChecklist->status == "complete")
                            <span class="bg-success" style="color: white;">&nbsp;Complete&nbsp;</span>
                        @else
                            <span class="bg-danger" style="color: white;">&nbsp;Incomplete&nbsp;</span>
                        @endif
                        <a href="{{ route('employees.checklist', [Auth::user()->id, 'home']) }}" class="pull-right">Go to Checklist <span class="glyphicon glyphicon-check"></span></a></h5>
                    @else
                        You are logged in!
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
