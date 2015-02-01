@extends('layouts.default')

@section('content')
    <h1>{{ $perspective->name }} </h1>

    <!-- Edit Composer Button -->
    {{ Form::open(array('route' => array('admin.perspectives.edit', $perspective->id), 'method' => 'get',
                                                'style' => 'display:inline-block')) }}
    <button type="submit" class="btn btn-info btn-mini">Edit</button>
    {{ Form::close() }}


    <!-- Delete Composer Button -->
    {{ Form::open(array('route' => array('admin.perspectives.edit', $perspective->id), 'method' => 'delete',
                                                'style' => 'display:inline-block')) }}
    <button type="submit" class="btn btn-danger btn-mini">Delete</button>
    {{ Form::close() }}

    {{ link_to("admin/projects/".$perspective->project, "Return to Project Page") }}
    <br/>

    <div>
        <h2>General Information</h2>
        <table class="table">
            <tr>
                <th>Analysis Name:</th>  <td>{{$perspective->name}}</td>
            </tr>
            <tr>
                <th>Project:</th>  <td>{{$perspective->project}}</td>
            </tr>
            <tr>
                <th>Notes:</th>  <td>{{$perspective->notes}}</td
            </tr>
        </table>
    </div>

    <div>
        <h2>Analysis Specification</h2>
        <br/>
  <pre>
    <code>
        {{$perspective->specification}}
    </code>
  </pre>
    </div>

@stop
