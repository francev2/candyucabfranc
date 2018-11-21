@extends('layouts.master')

@section('title-head', 'Crear rol')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Crear Rol</h4>
            </div>
            <div class="form-body">
            <form method="post" action="{{ isset($rol->codigo) ? url('admin/rol/'.$rol->codigo) : url('admin/rol') }}"  > 
                {!! csrf_field() !!}
                @if (isset($rol->codigo))
                    <input name="_method" type="hidden" value="PUT">
                @endif
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
                        <input type="text" name="nombre" class="form-control" value="{{old('nombre',isset($rol->nombre) ? $rol->nombre : null) }}">
                    </div>
                    
                    <div class="form-group"> 
                        <label for="fechapub">Selecionar permisos</label>
                        <select name="permisos[]" multiple class="form-control" id="">
                            @foreach ($permisos as $permiso)
                                @if (isset($rol_priv))
                                    @if (in_array($permiso->codigo, $rol_priv, false))
                                        <option value="{{$permiso->codigo}}" selected >{{$permiso->nombre}}</option>
                                    @else
                                        <option value="{{$permiso->codigo}}" >{{$permiso->nombre}}</option>
                                    @endif
                                @else
                                    <option value="{{$permiso->codigo}}" >{{$permiso->nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-default">Submit</button> 
                </form> 
            </div>
        </div>
    </div>
</div>
@stop