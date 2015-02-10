@extends('templates.default')

@section('content')
<ul class="nav nav-tabs">
  <li role="presentation"><a href="/system/settings">Settings</a></li>
  <li role="presentation"><a href="/system/users">Users</a></li>
  <li role="presentation" class="active"><a href="/system/elementtypes">Element Types</a></li>
  <li role="presentation"><a href="/system/relationtypes">Relation Types</a></li>
  <li role="presentation"><a href="/system/projects">Projects</a></li>
</ul>
  <h1>Available Element Types</h1>

  <table class="table">
    <tr>
      <th>ID</th><th>Name</th>
    </tr>
    @foreach($elementTypes as $elementType)
      <tr>
        <td> {!!  $elementType->id  !!} </td>
        <td> {!!  $elementType->name !!} </td>
      </tr>
    @endforeach
  </table>
  <br/>
  {!!  Form::open(['route' => 'system.elementtypes.create', 'method' => 'get'])  !!}
     <div>
       {!!  Form::submit('Add an Element Type')  !!}
     </div>
  {!!  Form::close()  !!}
@stop
