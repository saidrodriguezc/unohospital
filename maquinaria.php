<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];


  /////////////////////////////////////////  
  if($opcion == "eliminarhisto")
  {
    $histoid = $_GET['historicoid'];
    $maquinariaid = $clase->BDLockup($histoid,"histomaquina","histoid","maquinariaid");
    $vsql = "DELETE FROM histomaquina WHERE histoid=".$histoid;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Historico eliminado Exitosamente");  		
	header("Location: maquinaria.php?opcion=historico&id=".$maquinariaid);
  }
  
  /////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardarhisto")
  {
    $maquinariaid  = $_POST['maquinariaid'];
	$fecha         = $_POST['fecha'];
	$hora          = $_POST['hora'];	
	$descripcion   = strtoupper($_POST['descripcion']);		
	
	$fechaEvento = substr($fecha,6,4)."/".substr($fecha,3,2)."/".substr($fecha,0,2);
	
	$vsql = "INSERT INTO histomaquina(maquinariaid,fecha,hora,descripcion,creador) ".
            "values('".$maquinariaid."','".$fechaEvento."','".$hora."','".$descripcion."','".$_SESSION['USUARIO']."')";

    $cant = $clase->EjecutarSQL($vsql);

	if($cant == 1)
 		$clase->Aviso(1,"Historico creado Exitosamente");  	

	header("Location: maquinaria.php?opcion=historico&id=".$maquinariaid);
  }
  
  /////////////////////////////////////////  
  if($opcion == "nuevohisto")
  {
    $maquinariaid = $_GET['maquinariaid'];
    $cont.='<center><h3> Adicionar Historico </h3></center>
            <form action="?opcion=guardarhisto" method="POST">
	        <input type="hidden" name="maquinariaid" value="'.$maquinariaid.'">
			<table width="500">
	         <tr> 
			  <td width="120"> <label class="Texto15"> Fecha : </label> </td>
  			  <td> <input type="text" name="fecha" class="Texto15" size="12" maxlength="12" value="'.date("d/m/Y").'"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Hora : </td>
			  <td> <input type="text" name="hora" class="Texto15" size="10" maxlength="12" value="'.date("H:m:s").'"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <textarea name="descripcion" cols="40" rows="4"></textarea> </td>
			 </tr>			 
			</table>			
			<br>		
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table><br>';
	echo $cont;
	exit(0);		
  }

  /////////////////////////////////////////  
  if($opcion == "historico")
  {
    $id = $_GET['id'];
    $cont = $clase->Header("S","W"); ;  	 
	$cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/productos.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Historico de la Maquinaria <td>
				 <td>  <a href="maquinaria.php"> Listado de Maquinaria </a> </td>
    		     <td width="27"> <a href="?opcion=nuevohisto&maquinariaid='.$id.'" rel="facebox"> <img src="images/icononuevo.png" border="0"> </a> </td>				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table>';	
			 
    $nombremaquina = $clase->BDLockup($id,"maquinaria","maquinariaid","descripcion");		 
	 
    $vsql = "SELECT * FROM histomaquina WHERE maquinariaid = '.$id.' ORDER BY fecha DESC";

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="30">  Fecha </td>
				 <td width="20"> Hora </td>
				 <td width="230"> Descripcion Evento </td>			
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
				  <td> '.$row['fecha'].' </td>
				  <td> '.$row['hora'].' </td>
				  <td width="300"> '.$row['descripcion']. '</td>
				  <td> <a href="maquinaria.php?opcion=eliminarhisto&amp;historicoid='.$row['histoid'].'"> <img src="images/iconoborrar.png" border="0"> </td>		
				  <td>  </td>				  
				 </tr>';
	} 
  }
  
  
  //////////////////////////////////////////////////////////////////
  if($opcion == "guardaract")
  {
    $maquinariaid  = $_POST['id'];		
	$clase->EjecutarSQL("DELETE FROM actividadxmaquina WHERE maquinariaid=".$maquinariaid);
	
	$vsql = "SELECT actividadid FROM actividades ORDER BY codigo ASC";
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))
    {
        $actividadid = $row['actividadid'];
		$valor = $_POST[$actividadid];		
		
		if($valor == "S")
		{
		  $vsql ="INSERT INTO actividadxmaquina(actividadid,maquinariaid) values(".$actividadid.",".$maquinariaid.")";
		  $clase->EjecutarSQL($vsql);	
		}  
		
		$clase->Aviso(1,"Actividades Asociadas a la Maquinaria con Exito");  	
	}	
	
	header("Location: maquinaria.php");
  }

  ////////////////////////////////////////////////////////////////////////
  if($opcion == "actividadesmaq")
  {
    $id     = $_GET['id'];
	$vsql   = "SELECT * FROM actividades ORDER BY codigo ASC"; 
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	
	$cont='<form action="maquinaria.php?opcion=guardaract" method="POST"> 
	       <input type="hidden" name="id" value="'.$id.'">
	       <center>
	       <h3> Actividades a realizar </h3>
	       <table width="400">';
	
	while($row = mysql_fetch_array($result))
	{
      $asignada = $clase->SeleccionarUno("SELECT COUNT(*) FROM actividadxmaquina WHERE maquinariaid = ".$id." AND actividadid = ".$row['actividadid']);
	  $cont.='<tr>'; 
	  
	  if($asignada == 1)
	      $cont.='<td> <input type="checkbox" name="'.$row['actividadid'].'" value="S" checked> </td>';
	  else
	  	  $cont.='<td> <input type="checkbox" name="'.$row['actividadid'].'" value="S"> </td>'; 
	  
	  $cont.='   <td>'.$row['codigo'].'</td> 
	             <td>'.$row['descripcion'].'</td> 				 				 
	             <td> Cada '.$row['realizarcada'].' dias</td> 				 				 
		     </tr>'; 
	  		 
    }

	$cont.='</table> <br> <table width="350"> <tr> <td align="center"> <input type="submit" value="Guardar Actividades">  </td> </tr> </table>
	        <br> </form>';	
	echo $cont;
	exit();
  }


  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $maquinariaid  = $_POST['id'];
	$codigo        = strtoupper($_POST['codigo']);
	$descripcion   = strtoupper($_POST['descripcion']);	
	$marcamaq      = trim($_POST['marcamaq']);		
    $tipomaq       = trim($_POST['tipomaq']);			
	$modelo        = strtoupper($_POST['modelo']);
	$serie         = strtoupper($_POST['serie']);		
	$ano           = $_POST['ano'];
	$color         = strtoupper($_POST['color']);		
	$placa         = strtoupper($_POST['placa']);
	$feccompraX    = $_POST['feccompra'];		
	
	$feccompra = substr($feccompraX,6,4)."/".substr($feccompraX,3,2)."/".substr($feccompraX,0,2);
	$feccompra2= substr($feccompraX,6,4)."-".substr($feccompraX,3,2)."-".substr($feccompraX,0,2)." 00:00:00";	
	
	// Valido que el grupo y la linea esten en la base de datos
	$marcamaqid = $clase->BDLockup($marcamaq,"marcamaq","codigo","marcaid");
    $tipomaqid = $clase->BDLockup($tipomaq,"tipomaq","codigo","tipoid"); 
	
	if($marcamaqid == ""){
	  $clase->Aviso(2,"Marca de Maquina Incorrecto. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: maquinaria.php");
	  exit();
	}
    
	if($tipomaqid == ""){	
	  $clase->Aviso(2,"Tipo de Maquina Incorrecto. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: maquinaria.php");
	  exit();
	}	

	if($opcion == "guardarnuevo")
	{
		$vsql = "INSERT INTO maquinaria(codigo,descripcion,marca,tipo,modelo,serie,ano,color,placa,feccompra,foto) ".
		        "values('".$codigo."','".$descripcion."',".$marcamaqid.",".$tipomaqid.",'".
				 $modelo."','".$serie."','".$ano."','".$color."','".$placa."','".$feccompra."','".$foto."')";    				 

	    $cant = $clase->EjecutarSQL($vsql);

		if($cant == 1)
    		$clase->Aviso(1,"Producto creado Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE maquinaria SET codigo = '".$codigo."' , descripcion = '".$descripcion."' , 
		         marca=".$marcamaqid.", tipo=".$tipomaqid." , modelo = '".$modelo."' ,
				 serie = '".$serie."' , ano = '".$ano."' , placa = '".$placa."' ,
				 feccompra = '".$feccompra2."' WHERE maquinariaid=".$maquinariaid;

		$clase->EjecutarSQL($vsql);
		
   		$clase->Aviso(1,"Maquinaria modificada Exitosamente");  			  
    }	
	
	header("Location: maquinaria.php");
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
				 <td width="520"> Nueva Maquinaria <td>
				 <td>  <a href="maquinaria.php"> Listado de Maquinaria </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST" name="x">
			<table width="700">
	         <tr height="30"> 
			  <td width="150"> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" maxlength="5" id="default" autocomplete="off" tabindex="1" > 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="35" size="45" autocomplete="off" tabindex="2"> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Marca : </td>
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
<input name="marcamaq" id="search-q2" type="text" onkeyup="javascript:autosuggest2();" maxlength="12" size="12" autocomplete="off" tabindex="3"/>
<div id="res2"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->

			  
			  </td> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Tipo : </td>
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
<input name="tipomaq" id="search-q3" type="text" onkeyup="javascript:autosuggest3();" maxlength="12" size="12" autocomplete="off" tabindex="4"/>
<div id="res3"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			  
			  
			  </td> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Modelo : </label> </td>
			  <td> <input type="text" name="modelo" class="Texto15"  maxlength="15" size="10" autocomplete="off" tabindex="9"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> A&ntilde;o : </label> </td>
			  <td> <input type="text" name="ano" class="Texto15"  maxlength="4" size="4" autocomplete="off" tabindex="9"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Serie Chasis: </label> </td>
			  <td> <input type="text" name="serie" class="Texto15"  maxlength="30" size="30" autocomplete="off" tabindex="9"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Fecha Compra : </label> </td>
			  <td> <input type="text" name="feccompra" class="Texto15"  maxlength="10" size="10" autocomplete="off" tabindex="10" value="'.date("d/m/Y").'"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Placa : </label> </td>
			  <td> <input type="text" name="placa" class="Texto15"  maxlength="20" size="10" autocomplete="off" tabindex="11"> </td>
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
  if($opcion == "detalles")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/productos.png" width="32" height="32" border="0"> </td>
				 <td width="590"> Maquinaria <td>
				 <td>  <a href="maquinaria.php"> Listado de Maquinaria </a> </td>
    		     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
  	$vsql = "SELECT MM.codigo codmarca , TM.codigo codtipo , M.* 
	         FROM maquinaria M 
			 INNER JOIN marcamaq MM on (M.marca = MM.marcaid) 
			 INNER JOIN tipomaq TM on (M.tipo = TM.tipoid) 
	         WHERE maquinariaid=".$id;
			 
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	    $feccompra = substr($row['feccompra'],8,2)."/".substr($row['feccompra'],5,2)."/".substr($row['feccompra'],0,4);
		
		$cont.='<br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="id" value="'.$id.'">
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
			  <td> <label class="Texto15"> Marca : </td>
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
<input name="marcamaq" id="search-q2" type="text" onkeyup="javascript:autosuggest2();" maxlength="12" size="12" autocomplete="off" tabindex="3" value="'.$row['codmarca'].'"/>
<div id="res2"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->

			  
			  </td> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Tipo : </td>
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
<input name="tipomaq" id="search-q3" type="text" onkeyup="javascript:autosuggest3();" maxlength="12" size="12" autocomplete="off" tabindex="4" value="'.$row['codtipo'].'"/>
<div id="res3"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			  
			  
			  </td> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Modelo : </label> </td>
			  <td> <input type="text" name="modelo" class="Texto15"  maxlength="15" size="10" autocomplete="off" tabindex="9" value="'.$row['modelo'].'"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> A&ntilde;o : </label> </td>
			  <td> <input type="text" name="ano" class="Texto15"  maxlength="4" size="4" autocomplete="off" tabindex="9" value="'.$row['ano'].'"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Serie Chasis: </label> </td>
			  <td> <input type="text" name="serie" class="Texto15"  maxlength="30" size="30" autocomplete="off" tabindex="9" value="'.$row['serie'].'"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Fecha Compra : </label> </td>
			  <td> <input type="text" name="feccompra" class="Texto15"  maxlength="10" size="10" autocomplete="off" tabindex="10" value="'.$feccompra.'"> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Placa : </label> </td>
			  <td> <input type="text" name="placa" class="Texto15"  maxlength="20" size="10" autocomplete="off" tabindex="11" value="'.$row['placa'].'"> </td>
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
    $vsql = "DELETE FROM maquinaria WHERE maquinariaid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Maquinaria eliminada Exitosamente");  		
	header("Location: maquinaria.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: maquinaria.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT M.* , MM.descripcion marcamaquina FROM maquinaria M INNER JOIN marcamaq MM ON (M.marca = MM.marcaid)
	         WHERE M.codigo like '%".$criterio."%' OR M.descripcion like '%".$criterio."%' OR MM.descripcion like '%".$criterio."%'  
             ORDER BY M.codigo ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_MAQUINARIA'] = $vsql;
	header("Location: maquinaria.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT M.* , MM.descripcion marcamaquina FROM maquinaria M INNER JOIN marcamaq MM ON (M.marca = MM.marcaid)
    	     ORDER BY M.codigo ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	$_SESSION['SQL_MAQUINARIA'] = "";
	header("Location: maquinaria.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/productos.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Maquinaria <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_MAQUINARIA'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_MAQUINARIA'];
	if($vsql == "")
    	$vsql = "SELECT M.* , MM.descripcion marcamaquina FROM maquinaria M INNER JOIN marcamaq MM ON (M.marca = MM.marcaid)
			     ORDER BY M.codigo ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="35">  Codigo </td>
				 <td width="100"> Descripcion </td>
				 <td width="100"> Marca </td>			
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
				  <td> '.$row['codigo'].' </td>
				  <td> '.$row['descripcion'].' </td>
				  <td> '.$row['marcamaquina']. '</td>
				  <td> <a href="maquinaria.php?opcion=actividadesmaq&amp;id='.$row['maquinariaid'].'" rel="facebox"> <img src="images/funciones.png" border="0"> </td>		
			      <td> <a href="maquinaria.php?opcion=historico&amp;id='.$row['maquinariaid'].'"> <img src="images/iconoaccionesm.png" border="0"> </td>						  		  				  
				  <td> <a href="?opcion=detalles&amp;id='.$row['maquinariaid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
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
  function FuncionesEspeciales()
  {
       // Barra de Acciones Especiales
  	 $cont.='<table width="100%">	
	          <tr class="BarraDocumentos" valign="middle"> 
			     <td width="25%" align="center"> <img src="images/estadisticas.png"> Estadisticas Producto </td>
			     <td width="25%" align="center"> <img src="images/variaciones.png"> Crear Variaciones  </td>
			     <td width="25%" align="center"> <img src="images/codigobarras.png"> Codigos de Barra </td>
			     <td width="25%" align="center"> <img src="images/funciones.png"> Funciones Especiales </td>				 
			   </tr>	 			   
			 </table> ';	
     return($cont);			 
  }  
?> 