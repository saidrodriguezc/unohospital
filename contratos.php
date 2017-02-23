<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardartarifas")
  {
    $contratoid   = $_POST['contratoid'];
    $vsql='SELECT itemid FROM decontrato WHERE contratoid='.$contratoid;
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);	
	while($row = mysql_fetch_array($result))
	{
        $itemid = $row['itemid'];
		$variable = 'precio_'.$itemid;
		$precio = $_POST[$variable];
		$vsql2  = 'UPDATE decontrato SET precio='.$precio.' WHERE contratoid='.$contratoid.' AND itemid='.$row['itemid'];
		$clase->EjecutarSQL($vsql2);		   
	}
		
	$clase->Aviso(1,"Tarifas Actualizadas Exitosamente");  	
	header("Location: contratos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "tarifas")
  {
     $id      = $_GET['id'];
	 $numero  = $clase->BDLockup($id,"contratos","contratoid","numero");
	 $empresa = substr($clase->SeleccionarUno("SELECT T.nombre FROM contratos C INNER JOIN terceros T ON (C.terid = T.terid) WHERE C.contratoid=".$id),0,38);
	 
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/conceptos.png" width="32" height="32" border="0"> </td>
				 <td width="580"> Tarifas del Contrato No. <b>'.$numero.' - '.$empresa.'</b> <td>
				 <td>  <a href="contratos.php"> Listado de Contratos </a> </td>    		     
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> 
			 <form action="?opcion=guardartarifas" method="POST">
			 <input type="hidden" name="contratoid" value="'.$id.'">';	
	
  	$vsql = "SELECT DC.itemid , DC.contratoid , C.numero , T.nombre , I.descripcion , GP.descripcion grupo , DC.precio
	         FROM contratos C 
			 INNER JOIN terceros T ON (C.terid = T.terid)
			 INNER JOIN decontrato DC ON (C.contratoid = DC.contratoid )
			 INNER JOIN item I ON (I.itemid = DC.itemid )
			 INNER JOIN productos P ON (P.itemid = I.itemid)
			 INNER JOIN gruposprod GP ON (GP.gruposprodid = P.gruposprodid)
			 WHERE C.contratoid = ".$id."
			 ORDER BY GP.descripcion ASC , I.descripcion ASC";

	$cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="25"> Contrato</td>				 
				 <td width="200"> Servicio </td>
				 <td width="150"> Grupo </td>				 
				 <td width="50" align="right"> Precio Contrato</td>			
				 <td width="20"> </td>			 
			   </tr>';	
    $i = 0;
		          		 
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))
	{
	     $i++;	 
		 
		 if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		 else
		   $cont.='<tr class="TablaDocsImPar">';		 
	
		 $cont.=' <td width="10"> </td>
				  <td width="25"> '.$row['numero'].' </td>
				  <td width="200"> '.$row['descripcion'].' </td>
				  <td width="150"> '.$row['grupo'].' </td>				  
				  <td width="50" align="right"> <input type="text" style="text-align:right;" name="precio_'.$row['itemid'].'" size="7" value="'.$row['precio'].'"> </td>
				  <td width="20"> </td>
				</tr>';
	 }
	 $cont.='</table> <br>
	         <center> <input type="submit" value="Guardar Precios">';
  }
  
  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $contratoid   = $_POST['contratoid'];
	$numero       = strtoupper($_POST['numero']);
	$descripcion  = strtoupper($_POST['descripcion']);	
	$terid        = $clase->BDLockup($_POST['empresa'],"terceros","codigo","terid");		
    $fecdesde     = substr($_POST['fecdesde'],6,4)."-".substr($_POST['fecdesde'],3,2)."-".substr($_POST['fecdesde'],0,2);
	$fechasta     = substr($_POST['fechasta'],6,4)."-".substr($_POST['fechasta'],3,2)."-".substr($_POST['fechasta'],0,2);
    $tipodoc      = strtoupper($_POST['tipodoc']);	
	
	if($opcion == "guardarnuevo")
	{
        $vsql = "INSERT INTO contratos(numero,descripcion,terid,fecdesde,fechasta,tipodocgenera,creador,momento) values('".$numero."', 
	            '".$descripcion."',".$terid.",'".$fecdesde."','".$fechasta."','".$tipodoc."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";
        $cant = $clase->EjecutarSQL($vsql);
        $contratoid = $clase->SeleccionarUno("SELECT contratoid FROM contratos WHERE numero='".$numero."'");
		
		$vsql='SELECT * FROM item';		
	    $conex  = $clase->Conectar();
        $result = mysql_query($vsql,$conex);
	    while($row = mysql_fetch_array($result))
		{
           $vsql2 = 'INSERT INTO decontrato(contratoid,itemid,precio) values('.$contratoid.','.$row['itemid'].',0)';
           $clase->EjecutarSQL($vsql2);		   
		}
		
		if($cant == 1)
    		$clase->Aviso(1,"Contrato creado Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE contratos SET numero = '".$numero."' , descripcion = '".$descripcion."' , terid = ".$terid." ,
                 fecdesde = '".$fecdesde."' , fechasta = '".$fechasta."' , tipodocgenera = '".$tipodoc."'  		
	             WHERE contratoid=".$contratoid;
    
	    $clase->EjecutarSQL($vsql);
	
   		$clase->Aviso(1,"Contrato modificado Exitosamente");  			  
    }	
	
	header("Location: contratos.php");
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
			     <td width="37"> <img src="images/iconos/conceptos.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Contrato <td>
				 <td>  <a href="contratos.php"> Listado de Contratos </a> </td> 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST">	        
			<table width="650">
	         <tr> 
			  <td> <label class="Texto15"> Numero : </label> </td>
			  <td> <input type="text" name="numero" class="Texto15" size="12" maxlength="5"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="35" size="40"> 
			 </tr>
			 <tr> 
			  <td> <label class="Texto15"> Empresa : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework9.js"></script>
<style type="text/css">
#search-wrap7 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res7{width:150px; border:solid 1px #DEDEDE; display:none;}
#res7 ul, #res7 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res7 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res7 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res7 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res7 li a:hover{background:#FFFFFF;}
#res7 ul {padding:4px;}
</style>
<div id="search-wrap7">
<input name="empresa" id="search-q7" type="text" onkeyup="javascript:autosuggest7();" maxlength="12" size="15" tabindex="5" value="'.$cliente.'"/>
<div id="res7"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			   </td>			   
			  </tr>
	         <tr> 
			  <td> <label class="Texto15"> Fecha Inicio : </td>
			  <td> <input type="text" name="fecdesde" class="Texto15"  maxlength="10" size="12" value="'.date("d/m/Y").'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Fecha Terminacion : </td>
			  <td> <input type="text" name="fechasta" class="Texto15"  maxlength="10" size="12" value="'.date("d/m/Y").'"> 
			 </tr>			  
			 <tr> 
			  <td> <label class="Texto15"> Generar Prestacion como : </td>
			  <td> <select name="tipodoc">
			         <option value=""> </option>
					 <option value="PSE"> PRESTACION DE SERVICIO </option>
					 <option value="FVE"> FACTURA DE VENTA </option>
				   </select> </td>
			 </tr>			  			 
			</table>
			
			<br><br>
			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>'; 	
  }

  /////////////////////////////////////////  
  if($opcion == "detalles")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/conceptos.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Modificar Contratos <td>
				 <td>  <a href="contratos.php"> Listado de Contratos </a> </td>
    		     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>								 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
  	$vsql = "SELECT * FROM contratos WHERE contratoid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	    $terid    = $clase->BDLockup($row['terid'],'terceros','terid','nit');
	    $fecdesde = substr($row['fecdesde'],8,2)."/".substr($row['fecdesde'],5,2)."/".substr($row['fecdesde'],0,4);
	    $fechasta = substr($row['fechasta'],8,2)."/".substr($row['fechasta'],5,2)."/".substr($row['fechasta'],0,4);		
		
		$cont.='<br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="contratoid" value="'.$id.'">
            <table width="550">
	         <tr> 
			  <td> <label class="Texto15"> Numero : </label> </td>
			  <td> <input type="text" name="numero" class="Texto15" size="12" maxlength="5" value="'.$row['numero'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="35" size="40" value="'.$row['descripcion'].'"> 
			 </tr>
			 <tr> 
			  <td> <label class="Texto15"> Empresa : </td>
			  <td> 

				<!-- ************************************************************************************************ -->
				<!-- AJAX AUTOSUGGEST SCRIPT -->
				<script type="text/javascript" src="lib/ajax_framework9.js"></script>
				<style type="text/css">
				#search-wrap7 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
				#res7{width:150px; border:solid 1px #DEDEDE; display:none;}
				#res7 ul, #res7 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
				#res7 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
				#res7 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
				#res7 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
				#res7 li a:hover{background:#FFFFFF;}
				#res7 ul {padding:4px;}
				</style>
				<div id="search-wrap7">
				<input name="empresa" id="search-q7" type="text" onkeyup="javascript:autosuggest7();" maxlength="12" size="15" tabindex="5" value="'.$terid.'"/>
				<div id="res7"></div>
				</div>
				<!-- AJAX AUTOSUGGEST SCRIPT -->
				<!-- ************************************************************************************************ -->
			   
			   </td>			   
			  </tr>
	         <tr> 
			  <td> <label class="Texto15"> Fecha Inicio : </td>
			  <td> <input type="text" name="fecdesde" class="Texto15"  maxlength="10" size="12"  value="'.$fecdesde.'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Fecha Terminacion : </td>
			  <td> <input type="text" name="fechasta" class="Texto15"  maxlength="10" size="12"  value="'.$fechasta.'"> 
			 </tr>			  
             <tr> 
			  <td> <label class="Texto15"> Generar a Facturacion : </td>
			  <td>'.$clase->CrearCombo("tipodoc","tipodoc","descripcion","codigo",$row['tipodocgenera'],"S","descripcion").' </td>
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
    $vsql = "DELETE FROM decontratos WHERE contratoid=".$id;
	$clase->EjecutarSQL($vsql);
    $vsql = "DELETE FROM contratos WHERE contratoid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Contrato Eliminado Exitosamente");  		
	header("Location: contratos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: contratos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT * FROM contratos C INNER JOIN terceros T ON (C.terid = T.terid) WHERE C.numero like '%".$criterio."%' OR C.descripcion like '%".$criterio."%' ORDER BY numero ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_CONTRATOS'] = $vsql;
	header("Location: contratos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM contratos C INNER JOIN terceros T ON (C.terid = T.terid) ORDER BY C.numero ASC limit 0,30";
	$_SESSION['SQL_CONTRATOS'] = "";
	header("Location: contratos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/conceptos.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Gestion de Contratos <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_CONTRATOS'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_CONTRATOS'];
	if($vsql == "")
    	$vsql = "SELECT * FROM contratos C INNER JOIN terceros T ON (C.terid = T.terid)
		         ORDER BY C.numero ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="35">  Numero </td>
				 <td width="130"> Entidad </td>
				 <td width="50">  Inicio </td>			
				 <td width="50">  Fin </td>							 
				 <td width="20"> </td>
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
				  <td width="35"> '.$row['numero'].' </td>
				  <td width="130"> '.$row['nombre'].' </td>
				  <td width="50"> '.$row['fecdesde'].' </td>
                  <td width="50"> '.$row['fechasta'].' </td>
				  <td width="20"> <a href="?opcion=tarifas&amp;id='.$row['contratoid'].'"> <img src="images/funciones.png" border="0"> </td>				  
				  <td width="20"> <a href="?opcion=detalles&amp;id='.$row['contratoid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
				  <td width="20"> </td>
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
?> 