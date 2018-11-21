@extends('layouts.master')

@section('title-head', 'Mejores Clientes')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Mejores Clientes</h4>
            </div>
            
            
            <div class="form-body">
                <form method="post" action="{{url('admin/reporte/mejor-cliente') }}"  > 
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
                            <label for="nombre">Fecha Hasta</label>
                            <input type="date" name="desde" class="form-control" value="{{old('desde') }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="nombre">Fecha desde</label>
                            <input type="date" name="hasta" class="form-control" value="{{old('hasta') }}">
                        </div>
                        
                        <button type="submit" class="btn btn-default">Submit</button> 
                    </form> 
                </div>

            <table class="table">
                    <thead>
                        <tr>
                            <th>Rif cliente</th>
                            <th style="text-align: center;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data))
                            @foreach ($data as $d)
                                <tr>
                                    <td style="text-align: left">{{ $d->rif }}</td>
                                    <td style="text-align: center">{{ $d->cant }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
        </div>
    </div>
</div>
@stop