<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];


  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $itemid        = $_POST['itemid'];

	$codigo        = strtoupper($_POST['codigo']);
	$descripcion   = strtoupper($_POST['descripcion']);	
    $gruposprod    = trim($_POST['gruposprod']);		
    $lineaprod     = trim($_POST['lineaprod']);			
    $tipoproducto  = trim($_POST['tipoproducto']);			    
	$unidad        = strtoupper($_POST['unidad']);
	$unimayor      = strtoupper($_POST['unimayor']);		
	$eximin        = $_POST['eximin'];
	$eximax        = $_POST['eximax'];		
	$codigobarra   = strtoupper($_POST['codigobarra']);
	$referencia    = strtoupper($_POST['referencia']);		
	$ubicacion     = strtoupper($_POST['ubicacion']);		
	$observaciones = $_POST['observaciones'];				
	$porciva       = $_POST['porciva'];					
	
	$precio1       = $_POST['precio1'];					
	$precio2       = $_POST['precio2'];					
	$precio3       = $_POST['precio3'];					
	$precio4       = $_POST['precio4'];					
	$precio5       = $_POST['precio5'];									
	
	// Valido que el grupo y la linea esten en la base de datos
	$gruposprodid = $clase->BDLockup($gruposprod,"gruposprod","codigo","gruposprodid");
    $lineaprodid = $clase->BDLockup($lineaprod,"lineasprod","codigo","lineaprodid"); 
	$item_este_codigo =  $clase->BDLockup($codigo,"item","codigo","itemid"); 
	
	if($item_este_codigo != $itemid){
	  $clase->Aviso(2,"Codigo Asociado a otro producto. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: servicios.php");
	  exit();
	}
	 	
	if($gruposprodid == ""){
	  $clase->Aviso(2,"Grupo de Producto Incorrecto. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: servicios.php");
	  exit();
	}
    
	if($lineaprodid == ""){	
	  $clase->Aviso(2,"Linea de Producto Incorrecta. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: servicios.php");
	  exit();
	}	

	if($opcion == "guardarnuevo")
	{
        $vsql = "INSERT INTO item(codigo,descripcion,tipoitemid,creador,momento) values('".$codigo."', 
	            '".$descripcion."',1,'".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";
		$cant = $clase->EjecutarSQL($vsql);
		
        $itemid = $clase->BDLockup($codigo,"item","codigo","itemid");		
				
		$vsql = "INSERT INTO productos(itemid,gruposprodid,lineaprodid,tipoproducto,codigobarra,porciva,unidad,unimayor,eximin,eximax,referencia,ubicacion,
		         observaciones,precio1,precio2,precio3,precio4,precio5) values(".$itemid.",".$gruposprodid.",".$lineaprodid.",'".$tipoproducto."','".
				 $codigobarra."',".$porciva.",'".$unidad."','".$unimayor."',".$eximin.",".$eximax.",'".$referencia."','".$ubicacion."','".
				 $observaciones."',".$precio1.",".$precio2.",".$precio3.",".$precio4.",".$precio5.")";    
	 
		$cant = $clase->EjecutarSQL($vsql);
	
	    /// Creo el Servicio en cada Detalle de Contrato
		$vsql='SELECT contratoid FROM contratos';		
	    $conex  = $clase->Conectar();
        $result = mysql_query($vsql,$conex);
	    while($row = mysql_fetch_array($result))
		{
           $vsql2 = 'INSERT INTO decontrato(contratoid,itemid,precio) values('.$row['contratoid'].','.$itemid.',0)';
           $clase->EjecutarSQL($vsql2);		   
		}   
	
		if($cant == 1)
    		$clase->Aviso(1,"Servicio creado Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
		
		if($tipoproducto == 'COM') // Si es una promocion - Digitar el Contenido de la Promo
		   header("Location: servicios.php?opcion=promo&id=".$itemid);
		else  
		   header("Location: servicios.php");
			
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE item SET codigo = '".$codigo."' , descripcion = '".$descripcion."' 
	             WHERE itemid=".$itemid;
        $clase->EjecutarSQL($vsql);
		
		$vsql = "UPDATE productos SET gruposprodid = ".$gruposprodid." , lineaprodid=".$lineaprodid." , codigobarra = '".$codigobarra."' , 
		         tipoproducto = '".$tipoproducto."' , unidad = '".$unidad."' , unimayor = '".$unimayor."' , porciva = ".$porciva." ,
     		     eximin = ".$eximin." , eximax = ".$eximax." , referencia = '".$referencia."' ,
				 ubicacion = '".$ubicacion."' , observaciones = '".$observaciones."' , precio1 = ".$precio1." , 
				 precio2 = ".$precio2." , precio3 = ".$precio3." , precio4 = ".$precio4." ,  precio5 = ".$precio5."   
	             WHERE itemid=".$itemid;
        $clase->EjecutarSQL($vsql);	
			
   		$clase->Aviso(1,"Servicio modificado Exitosamente");  			  
   		header("Location: servicios.php");
    }	
	
	
  }
  
  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////    
  if($opcion == "nuevo")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/productos.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Producto <td>
				 <td>  <a href="servicios.php"> Listado de Productos </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';		
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST" name="x">
			<table width="700">
	         <tr height="30"> 
			  <td width="150"> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" maxlength="10" id="default" autocomplete="off" tabindex="1" > 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="35" size="45" autocomplete="off" tabindex="2"> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Grupo Producto : </td>
			  <td> 


<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework2.js"></script>
<style type="text/css">
#search-wrap2 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res2{width:150px; border:solid 1px #DEDEDE; display:none;}
#res2 ul, #res2 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res2 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res2 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res2 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res2 li a:hover{background:#FFFFFF;}
#res2 ul {padding:4px;}
</style>
<div id="search-wrap2">
<input name="gruposprod" id="search-q2" type="text" onkeyup="javascript:autosuggest2();" maxlength="12" size="12" autocomplete="off" tabindex="3"/>
<div id="res2"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->

			  
			  </td> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Linea Producto : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework3.js"></script>
<style type="text/css">
#search-wrap3 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res3{width:150px; border:solid 1px #DEDEDE; display:none;}
#res3 ul, #res3 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res3 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res3 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res3 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res3 li a:hover{background:#FFFFFF;}
#res3 ul {padding:4px;}
</style>
<div id="search-wrap3">
<input name="lineaprod" id="search-q3" type="text" onkeyup="javascript:autosuggest3();" maxlength="12" size="12" autocomplete="off" tabindex="4"/>
<div id="res3"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			  
			  
			  </td> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Tipo de Producto : </td>
			  <td> '.$clase->CrearCombo("tipoproducto","tipoproducto","descripcion","codigo","NOR","N","descripcion").' 
			 </tr>			 
	         <tr height="30"> 
			  <td> <label class="Texto15"> Unidad </label> </td>
			  <td> 
  			      <b> Detal  : </b> <input type="text" name="unidad" class="Texto15"  maxlength="5" size="5" autocomplete="off" tabindex="5"> 
				  <b> Mayor  : </b> <input type="text" name="unimayor" class="Texto15"  maxlength="5" size="5" autocomplete="off" tabindex="6"> 			  
			  </td>
			 </tr> 
	         <tr height="30"> 
			  <td> <label class="Texto15"> Existencias </label> </td>
			  <td> 
			      <b> M&iacute;nima : </b> <input type="text" name="eximin" class="Texto15"  maxlength="10" size="5" autocomplete="off" tabindex="7" value="0"> 
				  <b> M&aacute;xima : </b> <input type="text" name="eximax" class="Texto15"  maxlength="10" size="5" autocomplete="off" tabindex="8" value="0"> 			  
			  </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> C&oacute;digo Barras: </label> </td>
			  <td> <input type="text" name="codigobarra" class="Texto15"  maxlength="30" size="30" autocomplete="off" tabindex="9"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Porc. IVA </label> </td>
			  <td> <input type="text" name="porciva" class="Texto15"  maxlength="2" size="2" autocomplete="off" tabindex="10" value="16"> % </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Referencia : </label> </td>
			  <td> <input type="text" name="referencia" class="Texto15"  maxlength="20" size="30" autocomplete="off" tabindex="11"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Ubicaci&oacute;n: </label> </td>
			  <td> <input type="text" name="ubicacion" class="Texto15"  maxlength="20" size="30" autocomplete="off" tabindex="12"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Observaci&oacute;nes : </label> </td>
			  <td> <textarea name="observaciones" class="Texto15" cols="45" rows="3" autocomplete="off" tabindex="13"></textarea> </td>
			 </tr>

	         <tr height="30"> 
			  <td> <label class="Texto15"> Lista de Precios : </label> </td>
			  <td> 
			      <b> 1 : </b> <input type="text" name="precio1" value="0" class="Texto13"  maxlength="10" size="8" autocomplete="off" tabindex="14"> 
				  <b> 2 : </b> <input type="text" name="precio2" value="0" class="Texto13"  maxlength="10" size="8" autocomplete="off" tabindex="15"> 
				  <b> 3 : </b> <input type="text" name="precio3" value="0" class="Texto13"  maxlength="10" size="8" autocomplete="off" tabindex="16"> 
				  <b> 4 : </b> <input type="text" name="precio4" value="0" class="Texto13"  maxlength="10" size="8" autocomplete="off" tabindex="17"> 
				  <b> 5 : </b> <input type="text" name="precio5" value="0" class="Texto13"  maxlength="10" size="8" autocomplete="off" tabindex="18"> 
			  </td>
			 </tr>
			</table>
			
			<br><br>
			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>';  //onclick="location.href=\'?opcion=eliminar&amp;id='.$row['ciudadid']	 	
  }


  /////////////////////////////////////////  
  if($opcion == "estadisticas")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/productos.png" width="32" height="32" border="0"> </td>
				 <td width="500"> Productos <td>
				 <td>  <a href="servicios.php"> Listado de Productos </a> </td>
			     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	

	$cont.= FuncionesEspeciales(5);
	$cont.= DatosProducto();
  }
  
  /////////////////////////////////////////  
  if($opcion == "funciones")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/productos.png" width="32" height="32" border="0"> </td>
				 <td width="500"> Productos <td>
				 <td>  <a href="servicios.php"> Listado de Productos </a> </td>
			     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	

	$cont.= FuncionesEspeciales(4);
	$cont.= DatosProducto();
  }
  
  /////////////////////////////////////////  
  if($opcion == "codigosbarra")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/productos.png" width="32" height="32" border="0"> </td>
				 <td width="500"> Productos <td>
				 <td>  <a href="servicios.php"> Listado de Productos </a> </td>
			     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	

	$cont.= FuncionesEspeciales(3);
	$cont.= DatosProducto();
  }
  
  
  /////////////////////////////////////////  
  if($opcion == "combos")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/productos.png" width="32" height="32" border="0"> </td>
				 <td width="500"> Productos <td>
				 <td>  <a href="servicios.php"> Listado de Productos </a> </td>
			     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	

	$cont.= FuncionesEspeciales(2);	
	$cont.= DatosProducto();
    
    $cont.= '<table width="100%"><tr class="BarraDocumentos">
	           <td align="center" width="93%"> <b> Consumo del Producto por Combos (Formula - Receta) </b> </td> 
	           <td align="center" width="7%"> <a href="combos.php?opcion=nuevo&id='.$id.'" rel="facebox"><img src="images/icononuevo.png"></a> </td> 
			   </tr> </table>'; 
	///////////////////		   
	$vsql = "SELECT CP.comboprodid , I2.codigo , I2.descripcion , CP.cantidad , P2.unidad FROM comboproductos CP INNER JOIN productos P ON (CP.productoid = P.itemid) 
			 INNER JOIN item I2 ON (CP.materiapid = I2.itemid) INNER JOIN productos P2 ON (I2.itemid = P2.itemid) WHERE P.itemid=".$id;

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

    $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
				 <td width="70"> Codigo </td>
				 <td width="140"> Materia Prima  </td>			
				 <td width="30" align="center"> Cantidad </td>							 
				 <td width="30"> Unidad </td>							 				 
   			     <td width="20"> </td>
			   </tr>';	
    $i = 0;
    while($row = mysql_fetch_array($result)) 
	{
	     $i++;
		 if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		 else
		   $cont.='<tr class="TablaDocsImPar">';		 
		          
		 $cont.=' <td> </td>
				  <td> '.$row['codigo'].' </td>
				  <td> '.$row['descripcion'].' </td>
				  <td align="center"> '.$row['cantidad']. '</td>
				  <td> '.$row['unidad']. '</td>				  
				  <td> <a href="combos.php?opcion=preeliminar&amp;comboprodid='.$row['comboprodid'].'" rel="facebox"> <img src="images/iconoborrar.png" border="0"> </td>				  				  
				 </tr>';		   
	}			 
 }
  
  /////////////////////////////////////////  
  if($opcion == "detalles")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/productos.png" width="32" height="32" border="0"> </td>
				 <td width="500"> Productos <td>
				 <td>  <a href="servicios.php"> Listado de Productos </a> </td>
			     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	

	$EsMateriaPrima = $clase->BDLockup($id,"productos","itemid","tipoproducto");
	if($EsMateriaPrima != 'MAP')
	   $cont.= FuncionesEspeciales(1);
	
  	$vsql = "SELECT * FROM item I INNER JOIN productos P ON (I.itemid = P.itemid) WHERE I.itemid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
		$cont.='<br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="itemid" value="'.$id.'">
			<table width="700">
	         <tr height="30"> 
			  <td width="150"> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" maxlength="5" id="default" tabindex="1" value="'.$row['codigo'].'"> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="35" size="45" tabindex="2" value="'.$row['descripcion'].'"> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Grupo Producto : </td>
			  <td> 
			  
<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework2.js"></script>
<style type="text/css">
#search-wrap2 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res2{width:150px; border:solid 1px #DEDEDE; display:none;}
#res2 ul, #res2 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res2 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res2 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res2 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res2 li a:hover{background:#FFFFFF;}
#res2 ul {padding:4px;}
</style>
<div id="search-wrap2">
<input name="gruposprod" id="search-q2" type="text" onkeyup="javascript:autosuggest2();" maxlength="12" size="12" autocomplete="off" tabindex="3" value="'.$clase->BDLockup($row['gruposprodid'],"gruposprod","gruposprodid","codigo").'"/>
<div id="res2"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->

			  </td> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Linea Producto : </td>
			  <td> 
			  
<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework3.js"></script>
<style type="text/css">
#search-wrap3 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res3{width:150px; border:solid 1px #DEDEDE; display:none;}
#res3 ul, #res3 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res3 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res3 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res3 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res3 li a:hover{background:#FFFFFF;}
#res3 ul {padding:4px;}
</style>
<div id="search-wrap3">
<input name="lineaprod" id="search-q3" type="text" onkeyup="javascript:autosuggest3();" maxlength="12" size="12" autocomplete="off" tabindex="4" value="'.$clase->BDLockup($row['lineaprodid'],"lineasprod","lineaprodid","codigo").'"/>
<div id="res3"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->

              </td>			   
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Tipo de Producto : </td>
			  <td> '.$clase->CrearCombo("tipoproducto","tipoproducto","descripcion","codigo",$row['tipoproducto'],"N","codigo").' 
			 </tr>			 
	         <tr height="30"> 
			  <td> <label class="Texto15"> Unidad </label> </td>
			  <td> 
  			      <b> Detal  : </b> <input type="text" name="unidad" class="Texto15"  maxlength="5" size="5" tabindex="5"  value="'.$row['unidad'].'"> 
				  <b> Mayor  : </b> <input type="text" name="unimayor" class="Texto15"  maxlength="5" size="5" tabindex="6" value="'.$row['unimayor'].'"> 			  
			  </td>
			 </tr> 
	         <tr height="30"> 
			  <td> <label class="Texto15"> Existencias </label> </td>
			  <td> 
			      <b> M&iacute;nima : </b> <input type="text" name="eximin" class="Texto15"  maxlength="10" size="5" tabindex="7" value="'.$row['eximin'].'"> 
				  <b> M&aacute;xima : </b> <input type="text" name="eximax" class="Texto15"  maxlength="10" size="5" tabindex="8" value="'.$row['eximax'].'"> 			  
			  </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> C&oacute;digo Barras: </label> </td>
			  <td> <input type="text" name="codigobarra" class="Texto15"  maxlength="30" size="30" tabindex="9" value="'.$row['codigobarra'].'"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Porc. IVA </label> </td>
			  <td> <input type="text" name="porciva" class="Texto15"  maxlength="2" size="2" autocomplete="off" tabindex="10" value="'.$row['porciva'].'"> % </td>
			 </tr>			 
	         <tr height="30"> 
			  <td> <label class="Texto15"> Referencia : </label> </td>
			  <td> <input type="text" name="referencia" class="Texto15"  maxlength="20" size="30" tabindex="10" value="'.$row['referencia'].'"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Ubicaci&oacute;n: </label> </td>
			  <td> <input type="text" name="ubicacion" class="Texto15"  maxlength="20" size="30" tabindex="11" value="'.$row['ubicacion'].'"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Observaci&oacute;nes : </label> </td>
			  <td> <textarea name="observaciones" class="Texto15" cols="45" rows="3" tabindex="12">'.$row['observaciones'].'</textarea> </td>
			 </tr>

	         <tr height="30"> 
			  <td> <label class="Texto15"> Lista de Precios : </label> </td>
			  <td> 
			      <b> 1 : </b> <input type="text" name="precio1" class="Texto13"  maxlength="10" size="8" tabindex="13" value="'.$row['precio1'].'"> 
				  <b> 2 : </b> <input type="text" name="precio2" class="Texto13"  maxlength="10" size="8" tabindex="14" value="'.$row['precio2'].'"> 
				  <b> 3 : </b> <input type="text" name="precio3" class="Texto13"  maxlength="10" size="8" tabindex="15" value="'.$row['precio3'].'"> 
				  <b> 4 : </b> <input type="text" name="precio4" class="Texto13"  maxlength="10" size="8" tabindex="16" value="'.$row['precio4'].'"> 
				  <b> 5 : </b> <input type="text" name="precio5" class="Texto13"  maxlength="10" size="8" tabindex="17" value="'.$row['precio5'].'"> 
			  </td>
			 </tr>
			</table>
			
			<br><br>
			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>';  //onclick="location.href=\'?opcion=eliminar&amp;id='.$row['ciudadid']
	 }
  }
  
  /////////////////////////////////////////  
  if($opcion == "eliminar")
  {
    $id = $_GET['id'];

    if($id == 1)
	  $clase->Aviso(3,"No se puede Eliminar este Producto porque es un Producto del Sistema");
	else
	{    			
       $asociadoadoc = $clase->SeleccionarUno("SELECT COUNT(*) FROM dedocumentos WHERE itemid=".$id);
       if($asociadoadoc > 0)
	      $clase->Aviso(3,"No se puede Eliminar este Producto porque esta Asociado a Documentos");  			
       else
       {
	     $vsql = "DELETE FROM productos WHERE itemid=".$id;
         $clase->EjecutarSQL($vsql);
	     $vsql = "DELETE FROM item WHERE itemid=".$id;
         $clase->EjecutarSQL($vsql);
         $clase->Aviso(3,"Producto eliminado Exitosamente");  			
	   }
	}       
	header("Location: servicios.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: servicios.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT I.itemid , I.codigo , I.descripcion , GP.descripcion grupo 
	         FROM item I inner join productos P ON(I.itemid = P.itemid)
	         INNER JOIN gruposprod GP on (P.gruposprodid = GP.gruposprodid)
	         WHERE I.tipoitemid = 1 AND P.tipoproducto <> 'PRM' AND (I.codigo like '%".$criterio."%' OR I.descripcion like '%".$criterio."%' OR 
			 GP.descripcion like '%".$criterio."%') ORDER BY I.codigo ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

    $_SESSION['SQL_PRODUCTOS'] = $vsql;
	header("Location: servicios.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT I.itemid , I.codigo , I.descripcion , GP.descripcion grupo , P.existenciatotal
	         FROM item I inner join productos P ON(I.itemid = P.itemid)
	         INNER JOIN gruposprod GP on (P.gruposprodid = GP.gruposprodid)
	         WHERE I.tipoitemid = 1 AND P.tipoproducto <> 'PRM'
	         ORDER BY I.codigo ASC limit 0,30";

	$_SESSION['SQL_PRODUCTOS'] = "";
	header("Location: servicios.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/servicios.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Servicios Medicos <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_PRODUCTOS'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_PRODUCTOS'];
	if($vsql == "")
    	$vsql = "SELECT I.itemid , I.codigo , I.descripcion , GP.descripcion grupo , P.existenciatotal 
		         FROM item I inner join productos P ON(I.itemid = P.itemid)
		         INNER JOIN gruposprod GP on (P.gruposprodid = GP.gruposprodid)
		         WHERE I.tipoitemid = 1 AND P.tipoproducto <> 'PRM'
		         ORDER BY I.codigo ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="35">  Codigo </td>
				 <td width="130"> Descripcion </td>
				 <td width="70"> Grupo Product </td>			
				 <td width="20"> </td>
   			     <td width="20"> </td>
			   </tr>';	
    $i = 0;
    while($row = mysql_fetch_array($result)) 
	{
	     $i++;
		 if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		 else
		   $cont.='<tr class="TablaDocsImPar">';		 
		          
		 $cont.=' <td> </td>
				  <td width="35"> '.$row['codigo'].' </td>
				  <td width="130"> '.$row['descripcion'].' </td>
				  <td width="70"> '.$row['grupo']. '</td>
				  <td> <a href="ayudadocumentos.php?opcion=detallesprod&amp;itemid='.$row['itemid'].'" rel="facebox"> <img src="images/funciones.png" border="0"> </td>				  				  
				  <td> <a href="?opcion=detalles&amp;id='.$row['itemid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
				 </tr>';
	}
	$cont.='</table>
	        <table width="100%">
	           <tr class="PieTabla"> 
			     <td width="10"> </td>
			     <td width="100"> <a href="?opcion=masregistros"> Mas Registros </a> </td>
			     <td width="100"> </td>
				 <td width="100"> <a href="#arriba"> Arriba </a> </td>
			   </tr>
			</table>';
			
    mysql_free_result($result); 
    mysql_close($conex);			  
  }
  
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  
  
  
  
  ///////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////      
  function DatosProducto()
  {
    $id = $_GET['id'];
    $clase = new Sistema();
	$conex  = $clase->Conectar();
    $vsql = "SELECT I.codigo , I.descripcion , P.unidad , P.referencia , TP.descripcion TIPO
	         FROM item I INNER JOIN productos P ON (I.itemid = P.itemid) 
			 INNER JOIN tipoproducto TP ON (TP.codigo = P.tipoproducto)
			 WHERE I.itemid=".$id;

	$result = mysql_query($vsql,$conex);	
	if($row = mysql_fetch_array($result))
	{
	  $cont='<table width="100%">
	            <tr class="BarraDocumentosSel"> 
			     <td width="10"> </td>
			     <td width="18"> Codigo : </td>
			     <td width="30"> <b>'.$row['codigo'].'</b> </td>
			     <td width="18"> Producto : </td>			     
			     <td width="160"> <b>'.$row['descripcion'].'</b> </td>
			     <td width="18"> Medida : </td>			     			     
				 <td width="30"> <b>'.$row['unidad'].'</b> </td>
			     <td width="18"> Tipo : </td>			     			     				 
				 <td width="85"> <b>'.$row['TIPO'].'</b> </td>
		 	   </tr>
			</table>';
	}
	return($cont);
  }  
  
  ///////////////////////////////////////////////////////////////////////    
  function FuncionesEspeciales($item)
  {
  	 $id = $_GET['id'];
	 $cont.='<table width="100%">	
	          <tr class="BarraDocumentos">';
     if($item == 1)			  
	    $cont.='<td width="20%" class="BarraDocumentosSel" align="center"> <img src="images/productos.png" width="16" height="16"> Datos Basicos </td>';
     else
	    $cont.='<td width="20%" align="center"> <a href="?opcion=detalles&id='.$id.'"> <img src="images/productos.png" width="16" height="16"> Datos Basicos </a> </td>';

     if($item == 2)			  
	    $cont.='<td width="20%" class="BarraDocumentosSel" align="center"> <img src="images/variaciones.png"> Consumo x Combo </td>';
     else
	    $cont.='<td width="20%" align="center"> <a href="?opcion=combos&id='.$id.'"> <img src="images/variaciones.png"> Consumo x Combo</a> </td>';

     if($item == 3)			  
	    $cont.='<td width="20%" class="BarraDocumentosSel" align="center"> <img src="images/codigobarras.png"> Codigos de Barra </td>';
     else
	    $cont.='<td width="20%" align="center"> <a href="?opcion=codigosbarra&id='.$id.'"> <img src="images/codigobarras.png"> Codigos de Barra </a> </td>';

     if($item == 4)			  
	    $cont.='<td width="20%" class="BarraDocumentosSel" align="center"> <img src="images/funciones.png"> Func Especiales </td>';
     else
	    $cont.='<td width="20%" align="center"> <a href="?opcion=funciones&id='.$id.'"> <img src="images/funciones.png"> Func Especiales </a> </td>';

     if($item == 5)			  
	    $cont.='<td width="20%" class="BarraDocumentosSel" align="center"> <img src="images/estadisticas.png"> Estadisticas </td>';
     else
	    $cont.='<td width="20%" align="center"> <a href="?opcion=estadisticas&id='.$id.'"> <img src="images/estadisticas.png"> Estadisticas </a> </td>';

	 $cont.='</tr> </table> ';	
     return($cont);			 
  }
?> 