@extends('layouts.master')

@section('title-head', 'Agregar producto Diario Candy')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Agregar producto Diario Candy :</h4>
            </div>
            <div class="form-body">
            <form method="POST" action="{{ url('admin/diariocandy/diario/add') }}" enctype="multipart/form-data" > 
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
                <div class="form-group"> 
                <label for="fechapub">Fecha de Públicación: <b>{{$diario[0]->publicado}}</b></label> 
                <label for="fechapub">Fecha de vencimiento: <b>{{$diario[0]->vence}}</b></label>
                <input type="hidden" class="form-control" id="diariocod" name="diariocod" placeholder="diariocod" value="{{$diario[0]->codigo}}"> 
                </div>
                    <div class="form-group"> 
                        <label for="producto">Producto</label> 
                        <select name="producto" id="producto">
                            @foreach ($productos as $producto)
                            <option value="{{$producto->codigo}}">{{$producto->nombre}}</option>
                            @endforeach
                        </select>
                    </div> 
                    <div class="form-group"> 
                        <label for="fechaven">descuento</label> 
                        <input type="number" class="form-control" id="fechaven" name="descuento" placeholder="descuento" value="{{old('descuento')}}" > 
                    </div>
                    
                    <button type="submit" class="btn btn-default">Submit</button> 
                </form> 
            </div>
        </div>
    </div>
</div>
@stop