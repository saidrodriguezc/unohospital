<?PHP

  //////////////////////////////////////////////////////////////////////////////
  function IconoAsentarDoc($fecanulado,$fecasentado,$tipodoc,$id)
  {
	 if($fecanulado == "0000-00-00 00:00:00")
	 {
	 
	   if($tipodoc == "FVE")
	   {
          if($fecasentado == "0000-00-00 00:00:00")
	          $cont.='<td width="10" align="left"> <a href="ventas.php?opcion=preasentarventa&amp;id='.$id.'" rel="facebox"> <img src="images/iconoasentar.png" border="0"> </a> </td>';
           else
           {
	         if(strtoupper($_SESSION['USUARIO']) == "ADMINISTRADOR")
			   $cont.='<td width="10" align="left"> <a href="ventas.php?opcion=reversarventa&amp;id='.$id.'"> <img src="images/iconoreversar.png" border="0"> </a> </td>';	
			 else  
			   $cont.='<td width="10" align="left"> <img src="images/iconoreversar.png" border="0"> </td>';	
		   }		 				  	 
	   }

       if($tipodoc == "FCO")
	   {
		  if($fecasentado == "0000-00-00 00:00:00")
	         $cont.='<td width="10" align="left"> <a href="compras.php?opcion=asentarcompra&amp;id='.$id.'"> <img src="images/iconoasentar.png" border="0"> </a> </td>';
          else
	         $cont.='<td width="10" align="left"> <a href="compras.php?opcion=reversarcompra&amp;id='.$id.'"> <img src="images/iconoreversar.png" border="0"> </a> </td>';			 				  	 
	   }
	   
	   if($tipodoc == "TRB")
	   {
		  if($fecasentado == "0000-00-00 00:00:00")
	         $cont.='<td width="10" align="left"> <a href="traslados.php?opcion=asentartraslado&amp;id='.$id.'"> <img src="images/iconoasentar.png" border="0"> </a> </td>';
          else
	         $cont.='<td width="10" align="left"> <a href="traslados.php?opcion=reversartraslado&amp;id='.$id.'"> <img src="images/iconoreversar.png" border="0"> </a> </td>';			 				  	 
	   }

	   if($tipodoc == "CON")
	   {
		  if($fecasentado == "0000-00-00 00:00:00")
	         $cont.='<td width="10" align="left"> <a href="consumos.php?opcion=asentarconsumo&amp;id='.$id.'"> <img src="images/iconoasentar.png" border="0"> </a> </td>';
          else
	         $cont.='<td width="10" align="left"> <a href="consumos.php?opcion=reversarconsumo&amp;id='.$id.'"> <img src="images/iconoreversar.png" border="0"> </a> </td>';			 				  	 
	   }
	   
     }
	 else
       $cont.='<td width="10" align="left"> <img src="images/iconoanular.png" border="0"> </a> </td>';	 

     return($cont);
  }

  //////////////////////////////////////////////////////////////////////////////
  function IconoImprimirDoc($fecanulado,$fecasentado,$tipodoc,$id)
  {
	   if($tipodoc == "FVE"){
          $cont = '<a href="#" OnClick="window.open(\'ventas.php?opcion=imprimirventa&amp;id='.$id.'\',\'ImpFV\',\'width=600,height=650,left=100,top=100\')">';	   
       }

       if($tipodoc == "FCO"){
          $cont = '<a href="#" OnClick="window.open(\'compras.php?opcion=imprimircompra&amp;id='.$id.'\',\'ImpFC\',\'width=600,height=650,left=100,top=100\')">';	   	   
       }
	   
	    if($tipodoc == "TRB"){
          $cont = '<a href="#" OnClick="window.open(\'traslados.php?opcion=imprimirtraslado&amp;id='.$id.'\',\'ImpTB\',\'width=600,height=650,left=100,top=100\')">';	   	   
       }

	    if($tipodoc == "CON"){
          $cont = '<a href="#" OnClick="window.open(\'consumos.php?opcion=imprimirconsumo&amp;id='.$id.'\',\'ImpCO\',\'width=600,height=650,left=100,top=100\')">';	   	   
       }

      return($cont);
  }

  //////////////////////////////////////////////////////////////////////////////
  function BarraDocumentos()
  {
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla" valign="top"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/documentos.png" width="32" height="32" border="0"> </td>
				 <td width="500"> <a href="documentos.php"> Documentos  </a> <td>
				 <td width="200"> <label class="Link11">B&uacute;squeda en </label> 
				                  <select name="fecharango" class="Link11" disabled>';

				                  if($_SESSION['FILTRO_DOCUMENTOS'] == "DA")  
									 $cont.='<option value="DA" selected> Dia Actual </option>';
								  else	
                                     $cont.='<option value="DA"> Dia Actual </option>';
									
				                  if($_SESSION['FILTRO_DOCUMENTOS'] == "MA")  
									  $cont.='<option value="MA" selected> Mes Actual </option>';
								  else	  
									  $cont.='<option value="MA"> Mes Actual </option>';								  
                                  
								  if($_SESSION['FILTRO_DOCUMENTOS'] == "AA")  
									  $cont.='<option value="AA" selected> Año Actual </option>';	
								  else
									  $cont.='<option value="AA"> Año Actual </option>';									   	  

								  if($_SESSION['FILTRO_DOCUMENTOS'] == "TH")  
				          			  $cont.='<option value="TH" selected> Historico </option>';									
								  else
									  $cont.='<option value="TH"> Historico </option>';	

								$cont.='</select> 								 
								  <td>
				 <form action="documentos.php?opcion=encontrar" method="POST" name="y">
				 <td width="27">  <a href="documentos.php?opcion=busqavanzada" rel="facebox"> <img src="images/iconobusqueda.png" border="0"> </a> </td>				     			 
				 <td width="27">  <a href="documentos.php?opcion=encontrar"> <img src="images/iconorefrescar.png" border="0"> </a> </td>				     			 
				 <td width="150"> <input type="text" name="criterio" size="18" placeholder="Buscar" tabindex="1"></td>  			    
				 <td width="27"> <input type="image" src="images/iconobuscar.png" alt="Submit" /> </td>
				 </form>
				</tr>
			   </table>';
    return($cont);
  }

  //////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////
  function MenuAccesoLateral()
  {
  	 $cont.='<table width="950"> <tr> 
	         <td width="40" align="center" class="MenuLateralDocumentos">
			    <table>
				  <tr class="MenuLateralDocumentos"> <td> &nbsp; </td> </tr>
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="principal.php"> 
				                                            <img src="images/iconos/principal.png" border="0" width="28" height="28"> 
													        <span> <b>Menu Principal</b> </span> 
													      </a> </td> </tr>				  
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="productos.php">
				                                       <img src="images/iconos/productos.png" border="0" width="28" height="28"> 
													   <span> <b>Productos</b> <br> .Cambiar Precios <br> </span>
													 </a> </td> </tr>				  
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="observaciones.php">
				                                       <img src="images/iconos/observaciones.png" border="0" width="28" height="28"> 
													   <span> Observaciones <br> a Productos </span>
													 </a> </td> </tr>
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="gruposprod.php">
				    								   <img src="images/iconos/gruposprod.png" border="0" width="28" height="28">
													   <span> <b>Grupos</b> <br> de Productos </span>
													  </a> </td> </tr>
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="lineas.php">
				  								       <img src="images/iconos/lineasprod.png" border="0" width="28" height="28"> 
													   <span> <b>Lineas</b> <br> de Productos </span>													   
													   </a> </td> </tr>				  
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="bodegas.php">
				  			 						   <img src="images/iconos/bodegas.png" border="0" width="28" height="28"> 
													   <span> <b>Bodegas</b> <br> de Almacenamiento </span>													   
													   </a> </td> </tr>				  				  
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="promociones.php">
				  									   <img src="images/iconos/promociones.png" border="0" width="28" height="28"> 
													   <span> <b>Promociones</b> <br> de Productos </span>													   
													   </a> </td> </tr>				  				  
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="mediospago.php">
				  									    <img src="images/iconos/mediospago.png" border="0" width="28" height="28"> 
													    <span> Medios de Pago </span>														
														</a> </td> </tr>				  				  				  
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="cajas.php">
				  									   <img src="images/iconos/cajas.png" border="0" width="28" height="28"> 
													   <span> Cajas de Recaudo </span>																											   
													   </a> </td> </tr>				  				  				  				  
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="terceros.php">
				  									   <img src="images/iconos/terceros.png" border="0" width="28" height="28"> 
													   <span> <b>Terceros</b> <br>. Clientes <br>. Proveedores <br>. Fiadores <br>. Empleados </span>																											   
													   </a> </td> </tr>
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="conceptos.php">
				  									   <img src="images/iconos/conceptos.png" border="0" width="28" height="28"> 
													   <span> <b>Conceptos</b> </span>																											   
													   </a> </td> </tr>
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="informes.php">
				  									   <img src="images/iconos/informes.png" border="0" width="30" height="30"> 
													   <span> <b>Informes</b> </span>																											   													   
													   </a> </td> </tr>				  
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="sucursales.php">
				  									   <img src="images/iconos/sucursales.png" border="0" width="28" height="28"> 
													   <span> <b>Sucursales</b> </span>
													   </a> </td> </tr>
				  <tr class="MenuLateralDocumentos"> <td> <a class="Ntooltip" href="config.php">
				  									   <img src="images/iconos/configuracion.png" border="0" width="30" height="30"> 
													   <span> <b>Configuracion</b> </span>													   
													   </a> </td> </tr>
                </table>					
			 </td> <td>';	
     return($cont);
  }
  //////////////////////////////////////////////////////////////////////////////
  function SegundaBarraDocumentos()
  {
  	 $cont.='<table width="100%" valign="top">	
	          <tr class="BarraDocumentos" valign="top"> 
			   <td width="12"> </td>
			   <td width="22" align="left"> <input type="checkbox" name="seleccionar"> </td>
			   <td width="22"> <a href="javascript:mostrar();"> <img src="images/iconoasentar.png"/> </a> </td>			 
			   <td width="22"> <a href="javascript:mostrar();"> <img src="images/iconoimprimir.png"/> </a> </td>
   			   <td width="60">  </td>
		   	   <td width="70" align="left"> 
			      <a href="ventas.php?opcion=nuevaventa"><img src="images/icononuevo.png" border="0"></a> 
				  <a href="documentos.php?opcion=soloventas"> <label class="Link11"> Ventas  </label> </a>  </td>
			   <td width="70" align="left"> 
			      <a href="compras.php?opcion=nuevacompra"><img src="images/icononuevo.png" border="0"></a> 
				  <a href="documentos.php?opcion=solocompras"> <label class="Link11"> Compras  </label> </a> </td>
			   <td width="70" align="left"> 
			      <a href="traslados.php?opcion=nuevotraslado"><img src="images/icononuevo.png" border="0"></a> 
                  <a href="documentos.php?opcion=solotraslados">  <label class="Link11"> Traslados </label> </a></td>
			   <td width="70" align="left"> 
			      <a href="ajustesinv.php?opcion=nuevanota"><img src="images/icononuevo.png" border="0"></a> 
                  <a href="documentos.php?opcion=solonotas"> <label class="Link11"> Nota Inven </label> </td>
			   <td width="70" align="left"> 
			      <a href="consumos.php?opcion=nuevoconsumo"><img src="images/icononuevo.png" border="0"></a> 
				  <a href="documentos.php?opcion=soloconsumos"> <label class="Link11"> Consumos </label> </td>
			   <td width="70" align="left"> 
			      <a href="?opcion=nuevo"><img src="images/icononuevo.png" border="0"></a> 
				  <label class="Link11"> CxC o CxP </label> </td>
			   <td width="70" align="left"> 
			      <a href="?opcion=nuevo"><img src="images/icononuevo.png" border="0"></a> 
				  <label class="Link11"> Rec Caja</label>  </td>
			   <td width="70" align="left"> 
				 <a href="?opcion=nuevo"><img src="images/icononuevo.png" border="0"></a> 
				 <label class="Link11"> C. Egreso </label> </td>
			   </tr>
			 </table>';
    return($cont);
  }   
  
  //////////////////////////////////////////////////////////////////////////////
  function SegundaBarraEditarDoc($id)
  {
  	 $cont.='<table width="950">	
	          <tr class="BarraDocumentos"> 
			   <td width="15"> </td>
			   <td width="120" align="left"> <a href="#" class="Ntooltip2" OnClick="x.submit();"><img src="images/iconoguardar.png"/>Guardar Venta</td>
			   <td width="120" align="left"> <a href="ventas.php?opcion=preasentarventa&amp;id='.$id.'" class="Ntooltip2" rel="facebox"><img src="images/iconoasentar.png"/>Finalizar Venta</a></td>			   
			   <td width="120" align="left"> <a href="#" class="Ntooltip2" OnClick="window.open(\'ventas.php?opcion=imprimirventa&amp;id='.$id.'\',\'ImpFV\',\'width=600,height=650,left=100,top=100\')"><img src="images/iconoimprimir.png"/>Imprimir Factura</a></td>			   			   
			   <td width="120" align="left"> <a href="ventas.php?opcion=preanularventa&amp;id='.$id.'" rel="facebox" class="Ntooltip2"><img src="images/iconoanular.png"/>Anular Venta</a></td>			   			   			   
			   <td width="120" align="left"> <a href="ventas.php?opcion=preeliminarventa&amp;id='.$id.'" rel="facebox" class="Ntooltip2"><img src="images/iconoeliminar.png"/>Eliminar Venta</a></td>			   
			   <td width="100"> &nbsp; </td>
			   </tr>
			 </table>';
    return($cont);
  }   

  //////////////////////////////////////////////////////////////////////////////
  function SegundaBarraEditarDocCompra($id)
  {
  	 $cont.='<table width="950">	
	          <tr class="BarraDocumentos"> 
			   <td width="15"> </td>
			   <td width="120" align="left"> <a href="#" class="Ntooltip2" OnClick="x.submit();"><img src="images/iconoguardar.png"/>Guardar Compra</a></td>
			   <td width="120" align="left"> <a href="compras.php?opcion=asentarcompra&amp;id='.$id.'" class="Ntooltip2"><img src="images/iconoasentar.png"/>Bloquear Compra</a></td>			   
			   <td width="120" align="left"> <a href="#" class="Ntooltip2" OnClick="window.open(\'compras.php?opcion=imprimircompra&amp;id='.$id.'\',\'ImpFC\',\'width=600,height=650,left=100,top=100\')"><img src="images/iconoimprimir.png"/>Imprimir Compra</a></td>			   			   
			   <td width="120" align="left"> <a href="compras.php?opcion=preanularcompra&amp;id='.$id.'" class="Ntooltip2" rel="facebox"><img src="images/iconoanular.png"/>Anular Compra</a></td>			   			   			   
			   <td width="120" align="left"> <a href="compras.php?opcion=preeliminarcompra&amp;id='.$id.'" class="Ntooltip2" rel="facebox"><img src="images/iconoeliminar.png"/>Eliminar Compra</a></td>			   			   			   			   
			   <td width="100"> &nbsp; </td>
			   </tr>
			 </table>';
    return($cont);
  }   

  //////////////////////////////////////////////////////////////
  function FechaMySQL($Fecha)
  {
    $cont = substr($Fecha,6,4).'-'.substr($Fecha,3,2).'-'.substr($Fecha,0,2); 
    return($cont);
  }  
  //////////////////////////////////////////////////////////////
  function FormatoFecha($FechaSinF)
  {
    $cont = substr($FechaSinF,8,2).'/'.substr($FechaSinF,5,2).'/'.substr($FechaSinF,0,4); 
    return($cont);
  }

  //////////////////////////////////////////////////////////////
  function FormatoHora($FechaSinF)
  {
    $hora = substr($FechaSinF,11,2);
	if($hora <= 12)
    	$cont = $hora.substr($FechaSinF,13,3).' am';
    else{
	  $hora = $hora - 12;
	  $cont = $hora.substr($FechaSinF,13,3).' pm'; 
	}
    return($cont);
  }

  //////////////////////////////////////////////////////////////
  function FormatoNumero($Numero)
  {
    $cont = number_format($Numero);
    return($cont);
  }

  //////////////////////////////////////////////////////////////
  function RutaActual()
  {
    $desde = strlen($clase->URLSitio);
	$RutaActual = substr($_SERVER["REQUEST_URI"],13);
	return($RutaActual);  
  }

  //////////////////////////////////////////////////////////////
  function ExistenciaGrafica($Existencia,$Minima,$Maxima)
  {    
    if($Maxima == 0)
      $imagen = "existenciax.png";
	else
	{
	  $porcen = ($Existencia * 100) / $Maxima;	
	
		if($Existencia < $Minima)
	       $imagen = "existencia0.png";
		else
		{
		   if($Existencia >= $Maxima)
   	       $imagen = "existenciamaxima.png";
		   else
		   {
		      if(($porcen >= 0)&&($porcen <= 25))
			    $imagen = "existencia25.png";
			  else
			  {
			    if(($porcen > 25)&&($porcen <= 50))
			      $imagen = "existencia50.png";
			    else
       	     {
    			   if(($porcen > 25)&&($porcen <= 50))
			   	      $imagen = "existencia50.png";
			   	    else
	           	    {
	           	      if(($porcen > 50)&&($porcen <= 75))
	   		            $imagen = "existencia75.png";
				     else
					    $imagen = "existencia90.png";
				   }
				} 				
			  }		   
		   } 
		}
	}   
	$cont = '<img src="images/'.$imagen.'" border="0">';
    return($cont);
  }  
  
?>