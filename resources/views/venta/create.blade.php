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
            <form method="post" action="{{ url('admin/venta')}}"  > 
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
                    <label for="">Cajero: {{Auth::user()->name}}</label> 
                    <br> 
                    <br>
                    <div class="form-group"> 
                        <label for="fechaven">Tipo de cliente</label> 
                        <select name="tipo" class="form-control" id="">
                            <option value="N" {{old('tipo')=='N' ? 'selected' : '' }} >Natural</option>
                            <option value="J" {{old('tipo')=='J' ? 'selected' : '' }} >Juridico</option>
                        </select>
                        <br>
                        <label for="rif_cliente">Rif del cliente</label>
                        <input type="number" class="form-control" placeholder="ingrese rif" name="rif" value="{{old('rif')}}">
                    </div>
                    
                    <button type="submit" class="btn btn-default">Submit</button> 
                </form> 
            </div>
        </div>
    </div>
</div>
@stop