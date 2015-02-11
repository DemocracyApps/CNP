
@extends('templates.default')

@section('content')
    <div class="row">
        <h1>Perspectives on {!! $project->name !!}!</h1>
        <p> {!! $project->description !!} </p>
        <br>
    </div>
    <?php
            $count = sizeof($perspectives);
            $rowCount = floor($count/3);
            $remainder = $count%3;
            $hasRemainder = false;
            if ($remainder > 0) {
                ++$rowCount;
                $hasRemainder = true;
            }
    ?>
    @for ($row = 0; $row < $rowCount; ++$row)

        <div class="row">
            <?php
                $cmax = ($row == $rowCount-1 && $hasRemainder)?$remainder:3;
            ?>
            @for ($column = 0; $column < $cmax; ++$column)
                <?php $perspective = $perspectives[$row*3 + $column]; ?>
                <div class="col-md-4">
                    <h3><a href="/{!! $project->id !!}/perspectives/{!! $perspective->id !!}">{!! $perspective->name !!}</a></h3>
                    <p>{!! $perspective->description !!}</p>

                    {!! $perspective->getContent(); !!}

                </div>
            @endfor
        </div>
        <br>
    @endfor
@stop

@section('footer_right')
    @if ($owner)
        <a href="/admin/projects/{!! $project->id !!}"> Project Page </a>
    @endif
@stop