@extends('layouts.default')

@section('content')
    <h1>{{ $analysis->name }} </h1>

    <!-- Edit Composer Button -->
    {{ Form::open(array('route' => array(perspective, $analysis->id), 'method' => 'get',
                                                'style' => 'display:inline-block')) }}
    <button type="submit" class="btn btn-info btn-mini">Edit</button>
    {{ Form::close() }}


    <!-- Delete Composer Button -->
    {{ Form::open(array('route' => array(perspective, $analysis->id), 'method' => 'delete',
                                                'style' => 'display:inline-block')) }}
    <button type="submit" class="btn btn-danger btn-mini">Delete</button>
    {{ Form::close() }}

    {{ link_to("admin/projects/".$analysis->project, "Return to Project Page") }}
    <br/>

    <div>
        <h2>General Information</h2>
        <table class="table">
            <tr>
                <th>Analysis Name:</th>  <td>{{$analysis->name}}</td>
            </tr>
            <tr>
                <th>Project:</th>  <td>{{$analysis->project}}</td>
            </tr>
            <tr>
                <th>Notes:</th>  <td>{{$analysis->notes}}</td
            </tr>
        </table>
    </div>

    <div>
        <h2>Analysis Specification</h2>
        <br/>
  <pre>
    <code>
        {{$analysis->specification}}
    </code>
  </pre>
    </div>

@stop
