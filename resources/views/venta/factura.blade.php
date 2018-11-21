@extends('layouts.master')

@section('title-head', 'Pagar Pedido')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Factura</h4>
            </div>
            <div style="text-align: center;">
                <p>Factura N°:{{$num_factura}}</p>
                <p>tienda N°:{{$factura->tienda}}</p>
                <p>{{$factura->dir}}</p>
            </div>
            <hr>
            <div style="align:left">
                <p>Datos del cliente</p>
                <p>Rif:{{$cliente->rif}}</p>
                <p>{{$cliente->nombre}}</p>
                <p>{{$cliente->dir}}</p>
            </div>
            @if (!is_null($factura->empleado))
            <hr>
            <p>Nombre cajero: {{$factura->empleado}}</p>
            @endif
            <hr>
            <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th style="text-align: center;">Cantidad</th>
                            <th style="text-align: right" >Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detallados as $d)
                            <tr>
                                <td style="text-align: left">{{ $d->nombre }}</td>
                                <td style="text-align: center">{{ $d->cantidad }}</td>
                                <td style="text-align: right">{{ $d->precio }} x {{ $d->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>
                    <p style="text-align: right" >Total: <b>{{$total}}</b></p>
                </div>
        </div>
    </div>
</div>
@stop