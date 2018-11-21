@extends('layouts.master')

@section('title-head', '10 clientes con más puntos')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>10 clientes con más puntos</h4>
            </div>

            <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Rif</th>
                            <th style="text-align: center;">Cantidad de puntos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($data))
                            @foreach ($data as $d)
                                <tr>
                                    <td style="text-align: left">{{ $d->nombre }}</td>
                                    <td style="text-align: center">{{ $d->rif }}</td>
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