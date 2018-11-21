@extends('layouts.master')

@section('title-head', 'Crear privilegios')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Crear Privilegio</h4>
            </div>
            <div class="form-body">
            <form method="post" action="{{ url('admin/privilegio')}}"  > 
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
                    
                    <div>
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{old('nombre')}}">
                    </div>
                    
                    <button type="submit" class="btn btn-default">Submit</button> 
                </form> 
            </div>
        </div>
    </div>
</div>
@stop