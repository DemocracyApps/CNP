@extends('templates.default')

@section('content')
    <h1>{!!  $perspective->name  !!} </h1>

    <!-- Edit Perspective Button -->
    {!!  Form::open(array('route' => array('admin.perspectives.edit', $perspective->id), 'method' => 'get',
                                                'style' => 'display:inline-block'))  !!}
    <button type="submit" class="btn btn-info btn-mini">Edit</button>
    {!!  Form::close()  !!}


    <!-- Delete Perspective Button -->
    {!!  Form::open(array('route' => array('admin.perspectives.destroy', $perspective->id), 'method' => 'delete',
                                                'style' => 'display:inline-block'))  !!}
    <button type="submit" class="btn btn-danger btn-mini">Delete</button>
    {!!  Form::close()  !!}

    {!!  link_to("admin/projects/".$perspective->project, "Return to Project Page")  !!}
    <br/>

    <div>
        <h2>General Information</h2>
        <table class="table">
            <tr>
                <th>Perspective Name:</th>  <td>{!! $perspective->name !!}</td>
            </tr>
            <tr>
                <th>Perspective Type:</th>  <td>{!! $perspective->type !!}</td>
            </tr>
            <tr>
                <th>Requires Analysis?</th>  <td>{!! $perspective->requires_analysis?'Yes':'No' !!}</td>
            </tr>
            <tr>
                <th>Project:</th>  <td>{!! $perspective->project !!}</td>
            </tr>
            <tr>
                <th>Description:</th>  <td>{!! $perspective->description !!}</td
            </tr>
        </table>
    </div>

    <div>
        <h2>Analysis Specification</h2>
        <br/>
  <pre>
    <code>
        {!! $perspective->specification !!}
    </code>
  </pre>
    </div>

@stop
