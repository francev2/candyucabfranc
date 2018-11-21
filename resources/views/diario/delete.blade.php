@extends('layouts.master')

@section('title-head', 'Descartar producto Diario Candy')

@section('content')
<div class="panel panel-widget forms-panel">
    <h3>Fecha de Públicación: <b>{{$diario[0]->publicado}}</b><br>
        Fecha de vencimiento: <b>{{$diario[0]->vence}}</b>
    </h3>
    <table class="table">
        <thead>
            <tr>
                
                <th>producto</th>
                <th>descuento</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->descuento }}</td>
                    <td>
                        <form method="POST" action="{{url('admin/diariocandy/diario/delete')}}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="pubcod" value="{{$producto->codigo}}">
                            <input type="hidden" name="diacod" value="{{$diario[0]->codigo}}">
                            <button type="submit">Descartar Producto</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <button><a href="{{ url('admin/diariocandy') }}">Volver</a></button>
</div>
@stop