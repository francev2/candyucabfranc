@extends('layouts.master', ['carrito'=> null])

@section('title-head', 'Carrito')

@section('content')
<!-- start content -->
<div class="check">	 
        <div class="col-md-3 cart-total">
        <a class="continue" href="{{url('store/index')}}">Seguir comprando</a>
        <div class="price-details">
            <h3>Price Details</h3>
            <span>Total</span>
            <span class="total1">Bs. {{$total}}</span>
            <span>Discount</span>
            <span class="total1">---</span>
            <span>Delivery Charges</span>
            <span class="total1">150.00</span>
            <div class="clearfix"></div>				 
        </div>	
        <ul class="total_price">
          <li class="last_price"> <h4>TOTAL</h4></li>	
          <li class="last_price"><span>Bs. {{$total}}</span></li>
          <div class="clearfix"> </div>
        </ul>
       
        
        <div class="clearfix"></div>
            <a class="order" href="{{url('store/pago')}}">Realizar pedido</a>
       </div>
    <div class="col-md-9 cart-items">
        <h1>Mi carrito de compras ({{$cant}})</h1>
        @foreach($detallados as $p)
        <div class="cart-header">
            <form method="POST" action="{{url('store/delete')}}" >
                {!! csrf_field() !!}
                <input type="hidden" name="codigo_detalle" value="{{$p->codigo}}">
                <button class="close1" type="submit"></button>
            </form>
            
            <div class="cart-sec simpleCart_shelfItem">
                    <div class="cart-item cyc">
                        <img src="{{url('images/'.$p->imagen)}}" class="img-responsive" alt="">
                    </div>
                <div class="cart-item-info">
                    <h3><a href="#">{{$p->nombre}}</a></h3>
                    <ul class="qty">
                        <li><p>Cantidad: {{$p->cantidad}}</p></li>
                    </ul>
                   
                        <div class="delivery">
                        <p>Bs. {{$p->precio}}</p>
                        <div class="clearfix"></div>
                   </div>	
                </div>
                <div class="clearfix"></div>
                                       
             </div>
        </div>	
        @endforeach
    </div>
    
   
       <div class="clearfix"> </div>
</div>
@stop