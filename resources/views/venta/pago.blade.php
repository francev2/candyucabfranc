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
            <form method="post" action="{{ url('admin/venta/'.$num_factura.'/pago')}}"  > 
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
                    <p>Total: <b>@if(isset($total)){{$total}}@endif</p>
                        @if (!is_null($pagado))
                            <p>Pagado: <b>{{$pagado}}</p>
                            <p>Total a pagar: <b>{{$diferencia}}</p>
                        @else
                            <p>Total a pagar: <b>{{$total}}</p>
                        @endif 
                    <br>

                    <div class="form-group"> 
                        <select name="metodo" class="form-control" id="">
                            <option value="">Seleccionar método de pago</option>
                            @foreach($metodos as $metodo)
                                <option value="{{$metodo->codigo}}">{{$metodo->banco}} - {{$metodo->numero}} {{$metodo->tipo}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" >
                        <label for="all">Pagar Todo</label>
                        <input type="checkbox" name="all" style="display:inline; width:50px;" class="form-control" placeholder="cantidad" id="all">
                    </div>
                    <div class="form-group" >
                        <label for="all">Pagar en efectivo</label>
                        <input type="checkbox" name="efectivo" style="display:inline; width:50px;" class="form-control" placeholder="efectivo" id="all">
                    </div>
                    <div class="form-group" >
                        <input type="number" name="monto" class="form-control" class="form-control" placeholder="Monto" id="">
                    </div>
                    
                    <script>
                        $(document).ready(function() {
                            
                            $( '#all' ).on( 'change', function() {
                                if( $(this).is(':checked') ){
                                    // Hacer algo si el checkbox ha sido seleccionado
                                    $('input[name=monto]').prop('disabled', true);
                                    $('input[name=monto]').prop('value', '');
                                } else {
                                    // Hacer algo si el checkbox ha sido deseleccionado
                                    $('input[name=monto]').prop('disabled', false);
                                }
                            });
                        });
                    </script>
                    <button type="submit" class="btn btn-default">Agregar</button> 
            </form> 
            </div>
        </div>
    </div>
</div>
@stop