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
                <th>Publicado</th>
                <th>Vence</th>
                <th>productos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($diarios as $diario)
                <tr>
                    <td>{{ $diario->codigo }}</td>
                    <td>{{ $diario->publicado }}</td>
                    <td>{{ $diario->vence }}</td>
                    <td>@foreach ($publicados as $publicado)
                            @if($publicado->codigo == $diario->codigo)
                                {{$publicado->nombre}}: {{$publicado->descuento}}% de descuento <br>
                            @endif
                        @endforeach
                    </td>
                <td><button><a href="{{ url("admin/diariocandy/diario/".$diario->codigo."/add") }}">Agregar producto</a></button> <br><br>
                    <button><a href="{{ url("admin/diariocandy/diario/".$diario->codigo."/delete") }}">Borrar producto</a></button> <br><br>
                    <button><a href="{{ url("admin/diariocandy/".$diario->codigo."/edit") }}">Editar diario</a></button> <br><br>
                    <form method="POST" action="{{url('admin/diariocandy/diario/delete')}}" >
                        {!! csrf_field() !!}
                        <input type="hidden" name="codigo" value="{{$diario->codigo}}">
                        <button type="submit">Borrar</button>
                    </form>
                </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@stop