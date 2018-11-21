@extends('layouts.master', ['carrito'=> $carrito])

@section('title-head', 'Inicio')

@section('content')
<!-- start content -->

<div class="w_content">
		<div class="women">
			<a href="#"><h4>Productos - <span>{{count($productos)}} items</span> </h4></a>
			<ul class="w_nav">
						<li>Sort : </li>
		     			<li><a class="active" href="#">popular</a></li> |
		     			<li><a href="#">new </a></li> |
		     			<li><a href="#">discount</a></li> |
		     			<li><a href="#">price: Low High </a></li> 
		     			<div class="clear"></div>	
		     </ul>
		     <div class="clearfix"></div>	
		</div>
		<!-- grids_of_4 -->
		<div class="grids_of_4">
            @foreach( $productos as $producto)
		  <div class="grid1_of_4">
				<div class="content_box"><a href="details.html">
			   	   	 <img src="{{ url('images/'.$producto->imagen)}}" class="img-responsive" alt="">
				   	  </a>
				    <h4><a href="details.html"> {{$producto->nombre}}</a></h4>
				     <p>{{str_limit($producto->descripcion, $limit = 50)}}</p>
					 <div class="grid_1 simpleCart_shelfItem">
				    
					 <div class="item_add"><span class="item_price"><h6>Bs. {{$producto->precio}}</h6></span></div>
                     <div class="item_add"><span class="item_price add-to-cart"><button data-toggle="modal" pr-nombre="{{$producto->nombre}}   Bs:{{$producto->precio}}" p-id="{{$producto->codigo}}" data-target="#myModal" class="add-to-cart btn btn-danger" >add to cart</button></span></div>
					 </div>
			   	</div>
            </div>
            @endforeach
			<div class="clearfix"></div>
		</div>
		
		
        <!-- end grids_of_4 -->
        

        <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
      
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="forms">
                    <div class="form-grids widget-shadow" data-example-id="basic-forms"> 
                        <div class="form-title">
                            <h4>Agregar Al carrito</h4>
                        </div>
                        <div class="form-body">
                        <form method="post" action="{{url('store/add') }}"  > 
                            {!! csrf_field() !!}
                                
                                <div>
                                    <label for="nombre">Nombre: <b id="nombrep"></b></label>
                                    <input type="hidden" name="producto" class="form-control">
                                </div>
                                
                                <div>
                                    <label for="nombre">Cantidad</label>
                                    <input type="number" required name="cantidad" class="form-control">
                                </div>
                                
                                <button type="submit" class="btn btn-default">Agregar</button> 
                            </form> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
      
        </div>
      </div>

      <script>
            $(document).ready(function() {
                
                $( '.add-to-cart' ).on( 'click', function() {
                    $('#nombrep').text($(this).attr('pr-nombre'));
                    $('input[name=producto]').prop('value', $(this).attr('p-id'));
                });
            });
        </script>
@stop