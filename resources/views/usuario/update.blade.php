@extends('layouts.master')

@section('title-head', 'Actualizar usuario')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Actualizar usuario :</h4>
            </div>
            <div class="form-body">
            <form method="post" action="{{ url('admin/usuarios/'.$usuario->id.'/edit') }}" > 
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
                        <label for="email">Email</label> 
                        <input type="email" class="form-control" id="email" name="email" placeholder="Correo electrónico" value="{{ old('email',  $usuario->email) }}"> 
                    </div> 
                    <div class="form-group"> 
                        <label for="name">Nombre de usuario</label> 
                        <input type="text" class="form-control" id="name" name="name" placeholder="nombre de usuario" value="{{ old('name',  $usuario->name) }}" > 
                    </div> 
                    <div class="form-group"> 
                        <label for="name">Nombre de usuario</label> 
                        <select name="rol" id="rol" class="form-control" >
                            @foreach ($roles as $rol)
                                <option value="{{$rol->codigo}}" {{$rol->codigo==$usuario->rol ? 'selected' : ''}} >{{$rol->nombre}}</option>
                            @endforeach    
                        </select>    
                    </div> 
                    <div class="form-group"> 
                        <label for="password">Cambiar clave</label> 
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" value="" > 
                    </div>
                    
                    <button type="submit" class="btn btn-default">Submit</button> 
                </form> 
            </div>
        </div>
    </div>
</div>
@stop