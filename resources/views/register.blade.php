@extends('layouts.master')

@section('title-head', 'Crear una cuenta')

@section('content')
<div class="registration">
		<div class="registration_left">
		<h2>new user? <span> create an account </span></h2>
		<!-- [if IE] 
		    < link rel='stylesheet' type='text/css' href='ie.css'/>  
		 [endif] -->  
		  
		<!-- [if lt IE 7]>  
		    < link rel='stylesheet' type='text/css' href='ie6.css'/>  
		<! [endif] -->  
		<script>
			(function() {
		
			// Create input element for testing
			var inputs = document.createElement('input');
			
			// Create the supports object
			var supports = {};
			
			supports.autofocus   = 'autofocus' in inputs;
			supports.required    = 'required' in inputs;
			supports.placeholder = 'placeholder' in inputs;
		
			// Fallback for autofocus attribute
			if(!supports.autofocus) {
				
			}
			
			// Fallback for required attribute
			if(!supports.required) {
				
			}
		
			// Fallback for placeholder attribute
			if(!supports.placeholder) {
				
			}
			
			// Change text inside send button on submit
			var send = document.getElementById('register-submit');
			if(send) {
				send.onclick = function () {
					this.innerHTML = '...Sending';
				}
			}
		
		})();
		</script>
		 <div class="registration_form">
		 <!-- Form -->
			<form id="register" method="POST" action="{{ url('registro-cliente') }}">
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
					<label>
                        Codigo carnet de cliente:
                        <br>
                        <input  type="number" name="tienda" tabindex="1" autofocus=""> <b> - </b> <input  type="number" name="consecutivo" tabindex="1" autofocus="">
					</label>
                </div>
				<div>
					<label>
						<input placeholder="RIF" name="rif" type="text" tabindex="3" >
					</label>
				</div>
				<div>
					<label>
						<input placeholder="email" name="emailRegister" type="email" tabindex="3" >
					</label>
				</div>
				<div>
					<label>
						<input placeholder="password" name="passwordRegister" type="password" tabindex="4" >
					</label>
				</div>		
				<div>
					<input type="submit" value="Registrarse">
				</div>
			</form>
			<!-- /Form -->
		</div>
	</div>
	<div class="registration_left">
		<h2>existing user</h2>
		 <div class="registration_form">
		 <!-- Form -->
		 <form id="login" method="POST" action="{{ url('login') }}">
					{{ csrf_field() }}
					
					
				<div>
					<label>
						<input placeholder="email" name="email" id="email" type="email" tabindex="3" required="">
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </label>
				</div>
				<div>
					<label>
						<input placeholder="password" name="password" type="password" tabindex="4" required="">
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </label>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                        </label>
                    </div>
				</div>						
				<div>
					<input type="submit" value="Iniciar sesión">
				</div>
			</form>
			<!-- /Form -->
			</div>
	</div>
	<div class="clearfix"></div>
    </div>
    
@stop
