@extends('templates.default')

@section('content')

    <form method="POST" action="http://cnp.dev/admin/perspectives/{!! $perspective->id !!}"
          accept-charset="UTF-8" enctype="multipart/form-data">
        <input name="_method" type="hidden" value="PUT">
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
        <input type="hidden" name="project" value="{!! $perspective->project !!}">

        <h1>New Analysis</h1>

        <br>
        <div class="form-group">
            {!!  Form::label('name', 'Name: ')  !!}
            {!!  Form::text('name', $perspective->name, ['class' => 'form-control'])  !!}
            <br>
            <span class="error">{!!  $errors->first('name')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::label('specification', 'Specification') !!}
            {!!  Form::file('specification') !!}

            <span class="error">{!!  $errors->first('fileerror')  !!}</span>
            <br>
        </div>

        <div class="form-group">
            {!!  Form::label('description', 'Description: ')  !!}
            {!!  Form::textarea('description', $perspective->description, ['class' => 'form-control'])  !!}
            <br>
        </div>

        <div class="form-group">
            {!!  Form::submit('Update', ['class' => 'btn btn-primary'])  !!}
        </div>

    </form>
@stop
