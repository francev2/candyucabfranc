<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title-head')</title>


        <!-- Bootstrap Core CSS -->
    <link href="{!! asset("css/bootstrap.min.css") !!}" rel='stylesheet' type='text/css' />
    <!-- Custom CSS -->
    <link href="{!! asset("css/style.css") !!}" rel='stylesheet' type='text/css' />
    <!-- Graph CSS -->
    <link href="{!! asset("css/font-awesome.css") !!}" rel="stylesheet"> 
    <!-- jQuery -->
    <link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'/>
    <link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <!-- lined-icons -->
    <link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
    <script src="{!! asset("js/simpleCart.min.js") !!}" > </script>
    <script src="{!! asset("js/amcharts.js") !!}"></script>	
    <script src="{!! asset("js/serial.js") !!}"></script>	
    <script src="{!! asset("js/light.js") !!}"></script>	
    <!-- //lined-icons -->
    <script src="{!! asset("js/jquery-1.10.2.min.js") !!}"></script>
</head>
<body>
        <div class="left-content">
                <div class="inner-content">
                 <!-- header-starts -->
                     <div class="header-section">
                     <!-- top_bg -->
                                 <div class="top_bg">
                                     
                                         <div class="header_top">
                                             <div class="top_right">
                                                 <ul>
                                                     <li><a href="contact.html">help</a></li>|
                                                     <li><a href="contact.html">Contact</a></li>|
                                                     <li><a href="checkout.html">Delivery information</a></li>
                                                 </ul>
                                             </div>
                                             <div class="top_left">
                                                 <h2><span></span> Call us : 032 2352 782</h2>
                                             </div>
                                                 <div class="clearfix"> </div>
                                         </div>
                                     
                                 </div>
                             <div class="clearfix"></div>
                         <!-- /top_bg -->
                         </div>
                         <div class="header_bg">
                                 
                                     <div class="header">
                                         <div class="head-t">
                                             <div class="logo">
                                                 <a href="index.html"><img src="images/logo.png" class="img-responsive" alt=""> </a>
                                             </div>
                                                 <!-- start header_right -->
                                             <div class="header_right">
                                                 <div class="rgt-bottom">
                                                     <div class="log">
                                                         <div class="login">
                                                             @if(!Auth::check())
                                                             <div id="loginContainer"><a id="loginButton" class=""><span>Login</span></a>
                                                                 <div id="loginBox" style="display: none;">                
                                                                     <form id="loginForm" method="POST" action="{{url('login')}}">
                                                                            {!! csrf_field() !!}
                                                                            <fieldset id="body">
                                                                                <fieldset>
                                                                                    <label for="email">Email Address</label>
                                                                                    <input type="text" name="email" id="email">
                                                                                </fieldset>
                                                                                <fieldset>
                                                                                        <label for="password">Password</label>
                                                                                        <input type="password" name="password" id="password">
                                                                                </fieldset>
                                                                                <input type="submit" id="login" value="Sign in">
                                                                                <label for="checkbox"><input type="checkbox" id="checkbox"> <i>Remember me</i></label>
                                                                            </fieldset>
                                                                         <span><a href="#">Forgot your password?</a></span>
                                                                     </form>
                                                                 </div>
                                                             </div>
                                                             @endif
                                                         </div>
                                                     </div>
                                                     @if(!Auth::check())
                                                     <div class="reg">
                                                         <a href="/registro-cliente">REGISTER</a>
                                                     </div>
                                                     @endif
                                                @if(Auth::check() )
                                                <div class="reg">
                                                    <form action="{{url('logout')}}" method="POST">
                                                        {!! csrf_field() !!}
                                                        <button type="submit" class="btn" >Salir</button>
                                                    </form>
                                                    </div>
                                                @if(Auth::user()->fk_empleado == null)
                                                <div class="cart box_1">
                                                    <a href="checkout.html">
                                                        <h3> <span class="">@if(isset($carrito)){{!is_null($carrito) ? 'Bs. '.$carrito->total :''}}@endif</span> (<span id="" class="">@if(isset($carrito)){{!is_null($carrito) ? ''.$carrito->items :'0'}}@else 0 @endif</span> items)<img src="images/bag.png" alt=""></h3>
                                                    </a>	
                                                    <div class="clearfix"> </div>
                                                </div>
                                                <div class="create_btn">
                                                    <a href="{{url('store/cart')}}">CHECKOUT</a>
                                                </div>
                                                @endif
                                                @endif
                                                <div class="clearfix"> </div>
                                                 
                                             </div>
                                             @if(Auth::check() )
                                             @if(Auth::user()->fk_empleado == null)
                                             <div class="search">
                                                 <form id="login-form">
                                                     <input type="text" value="" placeholder="search...">
                                                     <input type="submit" value="">
                                                 </form>
                                             </div>
                                             @endif
                                             @endif
                                             <div class="clearfix"> </div>
                                         </div>
                                         <div class="clearfix"> </div>
                                     </div>
                                 </div>
                             
                         </div>
                             <!-- //header-ends -->
                <!--content-->
			    <div class="content">
                    <div class="women_main">
                        <!-- start content -->
                    
                       <div class="w_content">
                            
                            @section('content')
                            @show
                            
                        </div>
                       <div class="clearfix"></div>
                        <!-- end content -->
                        
                    </div>

                    <!-- footer -->
                    <div class="footer">
                        <div class="col-md-3 cust">
                            <h4>CUSTOMER CARE</h4>
                                <li><a href="contact.html">Help Center</a></li>
                                <li><a href="faq.html">FAQ</a></li>
                                <li><a href="details.html">How To Buy</a></li>
                                <li><a href="checkout.html">Delivery</a></li>
                        </div>
                        <div class="col-md-2 abt">
                            <h4>ABOUT US</h4>
                                <li><a href="products.html">Our Stories</a></li>
                                <li><a href="products.html">Press</a></li>
                                <li><a href="faq.html">Career</a></li>
                                <li><a href="contact.html">Contact</a></li>
                        </div>
                        <div class="col-md-2 myac">
                            <h4>MY ACCOUNT</h4>
                                <li><a href="register.html">Register</a></li>
                                <li><a href="checkout.html">My Cart</a></li>
                                <li><a href="checkout.html">Order History</a></li>
                                <li><a href="details.html">Payment</a></li>
                        </div>
                        <div class="col-md-5 our-st">
                            <div class="our-left">
                                <h4>OUR STORES</h4>
                            </div>
                            
                                <li><i class="add"> </i>Mark peter</li>
                                <li><i class="phone"> </i>012-586987</li>
                                <li><a href="mailto:info@example.com"><i class="mail"> </i>info@sitename.com </a></li>
                        </div>
                        <div class="clearfix"> </div>
                            <p>© 2016 Gretong. All Rights Reserved | Design by <a href="http://w3layouts.com/">W3layouts</a></p>
                    </div>

                                <!-- end footer -->
                </div>
                    
            </div>
        </div>

        <!--/sidebar-menu-->
				<div class="sidebar-menu">
                        <header class="logo1">
                            <a href="#" class="sidebar-icon"> <span class="fa fa-bars"></span> </a> 
                        </header>
                            <div style="border-top:1px ridge rgba(255, 255, 255, 0.15)"></div>
                               <div class="menu">
                                        <ul id="menu" >
                                            <li><a href="{{url('/')}}"><i class="fa fa-tachometer"></i> <span>Home</span></a></li>
                                            @if(Auth::check())
                                                @if(is_null(Auth::user()->fk_empleado))
                                        <li id="menu-academico" ><a href="{{url('store/index')}}"><i class="fa fa-file-text-o"></i> <span>Tienda</span></a></li>
                                                @else
                                             <li id="menu-academico" ><a href="#"><i class="fa fa-table"></i> <span> Rol</span> <span class="fa fa-angle-right" style="float: right"></span></a>
                                               <ul id="menu-academico-sub" >
                                               <li id="menu-academico-avaliacoes" ><a href="{{url('admin/rol')}}">index</a></li>
                                                <li id="menu-academico-avaliacoes" ><a href="{{url('admin/rol/create')}}">crear</a></li>
                                              </ul>
                                            </li>
                                            <li id="menu-academico" ><a href="#"><i class="fa fa-table"></i> <span> Privilegio</span> <span class="fa fa-angle-right" style="float: right"></span></a>
                                                <ul id="menu-academico-sub" >
                                                <li id="menu-academico-avaliacoes" ><a href="{{url('admin/privilegio')}}">index</a></li>
                                                 <li id="menu-academico-avaliacoes" ><a href="{{url('admin/privilegio/create')}}">crear</a></li>
                                               </ul>
                                            </li>
                                            <li id="menu-academico" ><a href="#"><i class="fa fa-table"></i> <span> Usuarios Empleados</span> <span class="fa fa-angle-right" style="float: right"></span></a>
                                                <ul id="menu-academico-sub" >
                                                <li id="menu-academico-avaliacoes" ><a href="{{url('admin/usuarios')}}">index</a></li>
                                                 <li id="menu-academico-avaliacoes" ><a href="{{url('admin/usuarios/create')}}">crear</a></li>
                                               </ul>
                                            </li>
                                            <li id="menu-academico" ><a href="#"><i class="fa fa-table"></i> <span> Diario Candy</span> <span class="fa fa-angle-right" style="float: right"></span></a>
                                                <ul id="menu-academico-sub" >
                                                <li id="menu-academico-avaliacoes" ><a href="{{url('admin/diariocandy')}}">index</a></li>
                                                 <li id="menu-academico-avaliacoes" ><a href="{{url('admin/diariocandy/create')}}">crear</a></li>
                                               </ul>
                                            </li>
                                            
                                            <li id="menu-academico" ><a href="#"><i class="fa fa-table"></i> <span> reportes</span> <span class="fa fa-angle-right" style="float: right"></span></a>
                                                <ul id="menu-academico-sub" >
                                                <li id="menu-academico-avaliacoes" ><a href="{{url('admin/reporte/cliente-frecuente')}}">Clientes frecuentes</a></li>
                                                 <li id="menu-academico-avaliacoes" ><a href="{{url('admin/reporte/mejor-cliente')}}">Mejores clientes</a></li>
                                                 <li id="menu-academico-avaliacoes" ><a href="{{url('admin/reporte/presupuesto-efectivo')}}">Presupuestos efectivos</a></li>
                                                 <li id="menu-academico-avaliacoes" ><a href="{{url('admin/reporte/mas-vendido')}}">Productos más vendidos</a></li>
                                                 <li id="menu-academico-avaliacoes" ><a href="{{url('admin/puntos-clientes')}}">Puntos de clientes</a></li>
                                                 <li id="menu-academico-avaliacoes" ><a href="{{url('admin/reporte/ranking-clientes')}}">Ranking de clientes</a></li>
                                                 <li id="menu-academico-avaliacoes" ><a href="{{url('admin/reporte/tiendas-puntos')}}">Tiendas con puntos pagados</a></li>
                                                </ul>
                                            </li>
                                             <li id="menu-academico" ><a href="{{url('admin/venta')}}"><i class="fa fa-file-text-o"></i> <span>Facturar</span></a></li>
                                        
                                            @endif
                                        @else
                                        <li><a href="{{url('registro-cliente')}}"><i class="fa fa-tachometer"></i> <span>Registrase/Iniciar sesión</span></a></li>
                                            
                                        @endif
                                      </ul>
                                    </div>
                                  </div>
                                  <div class="clearfix"></div>
    
    <!--js -->
<script src="{!! asset("js/jquery.nicescroll.js") !!}"></script>
<script src="{!! asset("js/scripts.js") !!}"></script>
<!-- Bootstrap Core JavaScript -->
   <script src="{!! asset("js/bootstrap.min.js") !!}"></script>
   <!-- /Bootstrap Core JavaScript -->
   	
</body>
</html>