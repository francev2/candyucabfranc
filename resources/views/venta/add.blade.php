@extends('layouts.master')

@section('title-head', 'Venta')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Venta</h4>
            </div>
            <div class="form-body">
            <form method="post" action="{{ url('admin/venta/'.$num_factura.'/add')}}"  > 
                {!! csrf_field() !!}
                @if($errors->any())
                    <div class="error alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{$error}}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                    <p>N° factura: {{$num_factura}}</p>
                    <p style="al">Cajero: {{Auth::user()->name}}</p> 
                    <p >Nombre cliente: {{$cliente->nombre}}</p>  <p >RIF cliente: {{$cliente->rif}}</p> 
                    
                    <br> 
                    <br>
                    <div class="form-group"> 
                        <select name="producto" class="form-control" id="">
                            <option value="">Seleccionar producto</option>
                            @foreach($productos as $producto)
                                <option value="{{$producto->codigo}}">{{$producto->nombre}}: {{$producto->precio}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" >
                        <input type="number" name="cantidad" class="form-control" placeholder="cantidad" id="">
                    </div>
                    
                    <button type="submit" class="btn btn-default">Agregar</button> 
            </form> 
            <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Acción</th>
                            <th style="text-align: right" >Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detallados as $d)
                            <tr>
                                <td>{{ $d->nombre }}</td>
                                <td>{{ $d->cantidad }}</td>
                                <td>
                                    <form method="POST" action="{{url('admin/venta/'.$num_factura.'/delete')}}" >
                                        {!! csrf_field() !!}
                                        <input type="hidden" name="codigo_detalle" value="{{$d->codigo}}">
                                        <button type="submit">Borrar</button>
                                    </form>
                                </td>
                                <td style="text-align: right">{{ $d->precio }} x {{ $d->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>
                    <p style="text-align: right" >Total: <b>@if(isset($total)){{$total}}@endif</b></p>
                </div>
                
                <div>
                <p style="text-align: right" ><a href="{{url('admin/venta/'.$num_factura.'/pago')}}">Pagar</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop