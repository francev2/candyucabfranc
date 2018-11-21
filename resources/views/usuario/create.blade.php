@extends('layouts.master')

@section('title-head', 'Crear Usuario empledo')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Crear usuario</h4>
            </div>
            <div class="form-body">
            <form method="post" action="{{ url('admin/usuarios')}}"  > 
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
                        <label for="fechapub">Seleccionar empleado</label>
                        <select name="empleado" class="form-control" id="">
                            @foreach ($empleados as $empleado)
                                <option value="{{$empleado->cedula}}">{{$empleado->cedula}} - {{$empleado->nombre}} - {{$empleado->departamento}}</option>
                            @endforeach
                        </select>
                    </div> 
                    <div class="form-group"> 
                        <label for="fechaven">Seleccionar Rol</label> 
                        <select name="rol" class="form-control" id="">
                            @foreach ($roles as $rol)
                                <option value="{{$rol->codigo}}" {{ old('rol')==$rol->codigo ? 'selected' : ''}} >{{$rol->nombre}}</option>
                            @endforeach
                        </select> 
                    </div>
                    <div>
                        <label for="email">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{old('email')}}">
                    </div>
                    <div>
                        <label for="password">Contraseña</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    
                    <button type="submit" class="btn btn-default">Submit</button> 
                </form> 
            </div>
        </div>
    </div>
</div>
@stop