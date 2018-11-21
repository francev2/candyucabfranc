@extends('layouts.master')

@section('title-head', 'Diarios publicados')

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
                <th>Email</th>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->rol }}</td>
                    <td>
                        <button><a href="{{ url("admin/usuarios/".$usuario->id."/edit") }}">Editar usuario</a></button> <br><br>
                        <form method="POST" action="{{url('admin/usuarios/delete')}}" >
                            {!! csrf_field() !!}
                            <input type="hidden" name="codigo" value="{{$usuario->id}}">
                            <button type="submit">Borrar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop