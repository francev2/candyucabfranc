@extends('layouts.master')

@section('title-head', 'Crear Diario Candy')

@section('content')
<div class="panel panel-widget forms-panel">
    <div class="forms">
        <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
            <div class="form-title">
                <h4>Diario Candy :</h4>
            </div>
            <div class="form-body">
            <form method="post" action="{{isset($diario->publicado) ? url('admin/diariocandy/'.$diario->codigo) : url('admin/diariocandy')}}" enctype="multipart/form-data" > 
                {!! csrf_field() !!}
                @if (isset($diario->publicado))
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
                    <div class="form-group"> 
                        <label for="fechapub">Fecha de Públicación</label> 
                    <input type="date" class="form-control" id="fechapub" name="fechapub" placeholder="Fecha de publicacion" value="{{ old('fechapub',  isset($diario->publicado) ? $diario->publicado : null) }}"> 
                    </div> 
                    <div class="form-group"> 
                        <label for="fechaven">Fecha de Vencimiento</label> 
                        <input type="date" class="form-control" id="fechaven" name="fechaven" placeholder="Fecha de cencimiento" value="{{ old('fechaven',  isset($diario->vence) ? $diario->vence : null) }}" > 
                    </div>
                    
                    <button type="submit" class="btn btn-default">Submit</button> 
                </form> 
            </div>
        </div>
    </div>
</div>
@stop