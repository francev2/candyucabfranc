@extends('layouts.master')

@section('title-head', 'Productos mas vendidos efectivos')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Productos mas vendidos</h4>
            </div>
            
            
            <div class="form-body">
                <form method="post" action="{{url('admin/reporte/mas-vendido') }}"  > 
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
                            <label for="nombre">Seleccionar Tienda</label>
                            <select name="tienda" id="">
                                <option ></option>
                                @foreach($tiendas as $t)
                                    <option value="{{$t->codigo}}">{{$t->nombre}}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-default">Submit</button> 
                    </form> 
                </div>

            <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th style="text-align: center;">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data))
                            @foreach ($data as $d)
                                <tr>
                                    <td style="text-align: left">{{ $d->nombre }}</td>
                                    <td style="text-align: center">{{ $d->can }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
        </div>
    </div>
</div>
@stop