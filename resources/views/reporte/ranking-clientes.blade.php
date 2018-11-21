@extends('layouts.master')

@section('title-head', 'Ranking de clientes')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Ranking de clientes</h4>
            </div>

            <table class="table">
                    <thead>
                        <tr>
                            <th>Rif</th>
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