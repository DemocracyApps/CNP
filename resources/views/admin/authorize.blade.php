
@extends('templates.default')

@section('title')
    {!!  $project->name  !!} Participation Agreement
@stop

@section('content')
    <p> You must review and agree to the terms and conditions below in order to participate in this project. </p>

    <div>
        <h2>Project Terms & Conditions</h2>
        <br>
        @if ($project->terms != null)
            <?php
            $pd = new Parsedown();
            echo $pd->text($project->terms);
            ?>
            <br>
            <p>By clicking on the <em>I Agree</em> button below, you assent to the terms and conditions above,
                as well as to all terms and conditions of the Community Narratives Platform, which you may
                review <a href="#">here</a>.</p>
        @else
            <p>By clicking on the <em>I Agree</em> button below, you assent to the terms and conditions
                of the Community Narratives Platform, which you may review <a href="#">here</a>.</p>
        @endif

        <br>
        <form method="POST" action="/{!! $project->id !!}/authorize" accept-charset="UTF-8">
            <input type="hidden" name="_token" value="{!! csrf_token() !!}">

            @if ($project->hasProperty('secret') && $project->getProperty('secret') != null)
                <div class="form-group">
                    {!!  Form::label('secret', 'Please enter the project secret: ')  !!}
                    {!!  Form::text('secret', null, ['class' => 'form-control'])  !!}
                    <br/>
                    <span class="error">{!!  $errors->first('secret')  !!}</span>
                </div>
                <br>
            @endif
            <div>
                {!!  Form::submit('I Agree', ['class' => 'btn btn-primary'])  !!}
            </div>
        </form>

    </div>

@stop
