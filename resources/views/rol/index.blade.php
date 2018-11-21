@extends('layouts.master')

@section('title-head', 'Roles')

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
                <th>Permisos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $rol)
                <tr>
                    <td>{{ $rol->codigo }}</td>
                    <td>{{ $rol->nombre }}</td>
                    <td>
                        @foreach($permisos as $permiso)
                            @if ($permiso->codigo == $rol->codigo)
                                {{ $permiso->privilegio }} <br>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        <button><a href="{{ url("admin/rol/".$rol->codigo."/edit") }}">Editar Rol</a></button> <br><br>
                        <form method="POST" action="{{url('admin/rol/'.$rol->codigo)}}" >
                            {!! csrf_field() !!}
                            <input name="_method" type="hidden" value="DELETE">
                            <input type="hidden" name="codigo" value="{{$rol->codigo}}">
                            <button type="submit">Borrar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop