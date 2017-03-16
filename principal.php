<?PHP
  session_start(); 
  include("lib/Sistema.php");
  include("lib/libdocumentos.php");  

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];


  /////////////////////////////////////////  
  if($opcion == "")
  {
    $_SESSION["NUMREGISTROSXCONSULTA"] = 50;
	 $cont = $clase->Header("S","W");  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/principal.png" width="32" height="32" border="0"> </td>
				 <td width="400"> <b>Principal</b> :: Menu Principal<td>
				 <td width="370" align="right"> <a href="principal.php?opcion=dashboard">
				  <input type="button" value="Ir al Dashboard" style="border: none; background: #3a7999; color: #f2f2f2; padding: 10px; font-size: 16px; border-radius: 5px; position: relative; box-sizing: border-box; transition: all 500ms ease;"></a>  </td>
				  <td width="30">&nbsp;</td>
			   </tr>	 			   
			 </table><center><br><br><br><br>
             <table width="100%">
              <tr height="500" valign="top"> 
				<td width="500" align="center"> 
                   <br><br><br>
				      <table width="490">
						 <tr height="30"><td align="center">&nbsp;</td></tr>					
						 <tr height="100" bgcolor="white"><td align="center">
						 <center><img src="images/logoempresa2.jpg" border="0" width="500" heigth="400"> </td></tr>					
					  </table> 	
					  <br><br><br><br>
				  <center><font face="3" color="gray">Este Aplicativo de Software se encuentra licenciado a nombre de <br>
				          <b>Salud Empresarial IPS</b> y se autoriza su uso Total como CoAutor y <br>
				          productor del mismo. Informese a los entes de control y a quien corresponda.<br>
 				          Derechos Reservados. 
				</td></tr></table>';      
			    
   ////////////////////////////////////////////////////////////
   $cont.='</table>';			 
  }

  /////////////////////////////////////////  
  if($opcion == "dashboard")
  {
    $_SESSION["NUMREGISTROSXCONSULTA"] = 50;
	 $cont = $clase->Header("S","W");  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/principal.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Dashboard :: Estadisticas. Ultimos 20 Dias<td>
				 <td width="400"> </td>
			   </tr>	 			   
			 </table>';	

     $cont.= BarraTitulo("Estadisticas de Atenciones en el Periodo");	 
     $cont.= '<br> <center> <a href="?opcion=aumentargraf&nombre=graatencionesxdia" rel="facebox">
	          <img src="reportes/graatencionesxdia.php" border="0" class="Grafico"> </a>';      

     $cont.= BarraTitulo("Estadisticas de Atenciones por Prestador");	 
     $cont.= '<br> <center> <a href="?opcion=aumentargraf&nombre=graatencionesxmedico" rel="facebox">
	 <img src="reportes/graatencionesxmedico.php" border="0" class="Grafico"> </a>';      

     $cont.= BarraTitulo("Estadisticas de Atenciones por Especialidades de Servicio");	 
     $cont.= '<br> <center> <a href="?opcion=aumentargraf&nombre=graatencionesxdia" rel="facebox">
	          <img src="reportes/graventasxgrupo.php" border="0" class="Grafico"> </a>';      
			    
   ////////////////////////////////////////////////////////////
   $cont.='</table>';			 
  }
  
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  
  
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////    
  function BarraTitulo($titulo)
  {
  	 $cont.='<table width="100%">	
	          <tr class="BarraTituloPrincipal"> 
			     <td width="25%" align="center"> '.$titulo.' </td>				 
			   </tr>	 			   
			 </table> ';	
     return($cont);			 
  }
  
  /////////////////////////////////////////  
  if($opcion == "aumentargraf")
  {
     $nombre = $_GET['nombre'];
	 $cont ='<img src="reportes/'.$nombre.'.php" border="0" width="800" height="400">';
	 echo $cont;
	 exit();
  } 
  
  ///////////////////////////////////////////////////////////////////////    
  function BarraTabs($seleccionado)
  {
  	 $cont.='<table width="100%">	
	          <tr class="BarraPrincipal" height="50">';

	 if($seleccionado == 0)		   
		$cont.='<td width="14%" align="center" class="BarraPrincipalSel"><img src="images/iconos/general.png" width="36" height="36"><br>General </td>';
     else		
		$cont.='<td width="14%" align="center"> <a href="?"><img src="images/iconos/general.png" width="36" height="36"><br>General </a> </td>';	 

	 if($seleccionado == 1)		   
		$cont.='<td width="14%" align="center" class="BarraPrincipalSel"><img src="images/iconos/ventas.png" width="36" height="36"><br>Ventas </td>';
     else		
		$cont.='<td width="14%" align="center"> <a href="?opcion=ventas"><img src="images/iconos/ventas.png" width="36" height="36"><br>Ventas </a> </td>';	 

	 if($seleccionado == 2)		   
		$cont.='<td width="14%" align="center" class="BarraPrincipalSel"><img src="images/iconos/compras.png" width="36" height="36"><br>Compras </td>';		
     else		
		$cont.='<td width="14%" align="center"><a href="?opcion=compras"> <img src="images/iconos/compras.png" width="36" height="36"><br>Compras </a> </td>';

	 if($seleccionado == 3)		   
		$cont.='<td width="14%" align="center" class="BarraPrincipalSel"><img src="images/iconos/inventario.png" width="36" height="36"><br> Inventario </td>';     
	 else		
		$cont.='<td width="14%" align="center"><a href="?opcion=inventario"> <img src="images/iconos/inventario.png" width="36" height="36"><br> Inventario </a> </td>';

	 if($seleccionado == 4)		   
		$cont.='<td width="14%" align="center" class="BarraPrincipalSel"><img src="images/iconos/cobros.png" width="36" height="36"><br> Cobros </td>';		
     else		
		$cont.='<td width="14%" align="center"><a href="?opcion=cobros"><img src="images/iconos/cobros.png" width="36" height="36"><br> Cobros </a> </td>';

	 if($seleccionado == 5)		   
		$cont.='<td width="14%" align="center" class="BarraPrincipalSel"><img src="images/iconos/pagos.png" width="36" height="36"><br> Pagos </td>';					 
     else		
		$cont.='<td width="14%" align="center"><a href="?opcion=pagos"><img src="images/iconos/pagos.png" width="36" height="36"><br> Pagos </a> </td>';

	 if($seleccionado == 6)		   
		$cont.='<td width="14%" align="center" class="BarraPrincipalSel"><img src="images/iconos/alertas.png" width="36" height="36"><br> Alertas </td>';						 
     else		
		$cont.='<td width="14%" align="center"><a href="?opcion=alertas"><img src="images/iconos/alertas.png" width="36" height="36"><br> Alertas </a> </td>';				 

     $cont.='</tr></table> ';	
     return($cont);			 
  }


///////////////////////////////////////////////////////////////////////    
  function BarraMensaje($mensaje1,$mensaje2,$mensaje3,$estilo)
  {
  	 if(($mensaje1 != "")&&($mensaje2 != "")&&($mensaje3 != ""))
	 {
	    $cont.='<table width="100%">';
		if($estilo == 1)	
	       $cont.='<tr class="MensajePrincipal">';
		else
		   $cont.='<tr class="MensajePrincipalPar">';
		       
		$cont.='   <td width="2%"> &nbsp; </td>
			       <td width="32%" align="left"> '.$mensaje1.' </td>				 
			       <td width="32%" align="left"> '.$mensaje2.' </td>				 				 
			       <td width="32%" align="left"> '.$mensaje3.' </td>				 				 				   
			     </tr>	 			   
			    </table> ';	
     }
	 
  	 if(($mensaje1 != "")&&($mensaje2 != "")&&($mensaje3 == ""))
	 {
	    $cont.='<table width="100%">	
	             <tr class="MensajePrincipal"> 
		    	   <td width="2%"> &nbsp; </td>
			       <td width="49%" align="left"> '.$mensaje1.' </td>				 
			       <td width="49%" align="left"> '.$mensaje2.' </td>				 				 
			     </tr>	 			   
			    </table> ';	
     }
	 
  	 if(($mensaje1 != "")&&($mensaje2 == "")&&($mensaje3 == ""))
	 {
	    $cont.='<table width="100%">	
	             <tr class="MensajePrincipal"> 
		    	   <td width="2%"> &nbsp; </td>
			       <td width="98%" align="left"> '.$mensaje1.' </td>				 
			     </tr>	 			   
			    </table> ';	
     }
	  				
     return($cont);			 
  }

?> 