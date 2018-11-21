@extends('layouts.master')

@section('title-head', 'Privilegios')

@section('content')
<div class="panel panel-widget forms-panel">
    @if (session('status'))
        <div class="alert alert-danger">
            {{ session('status') }}
        </div>
    @endif
    <table class="table">
        <thead>
            <tr>                
                <th>id</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($privilegios as $privilegio)
                <tr>
                    <td>{{ $privilegio->codigo }}</td>
                    <td>{{ $privilegio->nombre }}</td>
                    <td>
                        <button><a href="{{ url("admin/privilegio/".$privilegio->codigo."/edit") }}">Editar privilegio</a></button> <br><br>
                        <form method="POST" action="{{url('admin/privilegio/'.$privilegio->codigo)}}" >
                            {!! csrf_field() !!}
                            <input name="_method" type="hidden" value="DELETE">
                            <input type="hidden" name="codigo" value="{{$privilegio->codigo}}">
                            <button type="submit">Borrar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop