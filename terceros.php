<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];


  /////////////////////////////////////////  
  if($opcion == "estadisticas")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Terceros <td>
				 <td>  <a href="terceros.php"> Listado de Terceros </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
    
//	$cont.= FuncionesEspeciales($id);
	
  	$vsql = "SELECT * FROM terceros WHERE terid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST" name="x">
			<input type="hidden" name="terid" value="'.$id.'">
			<table width="700">
	        <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="15" maxlength="15" id="default" value="'.$row['codigo'].'"> 
			 </tr>
			 </table>';
    }
  }


  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $terid        = $_POST['terid'];

	$codigo        = strtoupper($_POST['codigo']);
	$nit           = strtoupper($_POST['nit']);	
	$nombre        = strtoupper($_POST['nombre']);	
    $direccion     = strtoupper($_POST['direccion']);		
    $ciudad        = trim($_POST['ciudad']);			
    $zona          = trim($_POST['zona']);				
	$telefono      = strtoupper($_POST['telefono']);
	$celular       = strtoupper($_POST['celular']);		
	$email         = strtolower($_POST['email']);
    $clasificater  = trim($_POST['clasificater']);			
		
	// Valido que el grupo y la linea esten en la base de datos
	$ciudadid = $clase->BDLockup($ciudad,"ciudades","codigo","ciudadid");
    $zonaid   = $clase->BDLockup($zona,"zonater","codigo","zonaid"); 
    $clasificaterid = $clase->BDLockup($clasificater,"clasificater","codigo","clasificaterid"); 	

	$terid_este_codigo =  $clase->BDLockup($codigo,"terceros","codigo","terid"); 

	if(($codigo == "")||($nit == "")){
	  $clase->Aviso(2,"El Nit - Codigo No pueden ser vacios &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: terceros.php");
	  exit();
	}
	 	
	if($ciudadid == ""){
	  $clase->Aviso(2,"Ciudad Incorrecta. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: terceros.php");
	  exit();
	}
    
	if($zonaid == ""){	
	  $clase->Aviso(2,"Zona del Tercero Incorrecta. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: terceros.php");
	  exit();
	}	

	if($clasificaterid == ""){	
	  $clase->Aviso(2,"Clasificacion del Tercero Incorrecta. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: terceros.php");
	  exit();
	}	

	if($opcion == "guardarnuevo")
	{
		$vsql = "INSERT INTO terceros(codigo,nit,nombre,direccion,ciudadid,telefono,celular,email,zonaid,clasificaterid,creador,momento)
		          values('".$codigo."','".$nit."','".$nombre."','".$direccion."',".$ciudadid.",'".$telefono."','".$celular."','".$email."',".
				 $zonaid.",".$clasificaterid.",'".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";    
				  
		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Tercero creado Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE terceros SET codigo = '".$codigo."' , nit = '".$nit."' , nombre = '".$nombre."' ,
		         direccion = '".$direccion."' , ciudadid = ".$ciudadid." , telefono = '".$telefono."' ,
		         celular = '".$celular."' , email = '".$email."' , zonaid = ".$zonaid." ,				 
		         clasificaterid = ".$clasificaterid." , momento = CURRENT_TIMESTAMP				 				 
	             WHERE terid=".$terid;

        $clase->EjecutarSQL($vsql);
		
  		$clase->Aviso(1,"Tercero modificado Exitosamente");  			  
    }	
	
	header("Location: terceros.php");
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
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Tercero <td>
				 <td>  <a href="terceros.php"> Listado de Terceros </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <script language="javascript">
			  function CopiarCodigoaNit()
			  {
			    if(document.x.nit.value == "")
   				  document.x.nit.value=document.x.codigo.value; 
			  }
			</script>
			<form action="?opcion=guardarnuevo" method="POST" name="x">
			<table width="700">
	        <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="15" maxlength="15" id="default"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> CC o NIT : </label> </td>
			  <td> <input type="text" name="nit" class="Texto15" size="15" maxlength="15" tabindex="2" OnFocus="CopiarCodigoaNit();"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Nombre : </td>
			  <td> <input type="text" name="nombre" class="Texto15" maxlength="60" size="45" tabindex="3"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Direccion : </td>
			  <td> <input type="text" name="direccion" class="Texto15" maxlength="60" size="45" tabindex="4"> 
			 </tr>			
             <tr height="30"> 
			  <td> <label class="Texto15"> Ciudad : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework4.js"></script>
<style type="text/css">
#search-wrap4 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res4{width:150px; border:solid 1px #DEDEDE; display:none;}
#res4 ul, #res4 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res4 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res4 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res4 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res4 li a:hover{background:#FFFFFF;}
#res4 ul {padding:4px;}
</style>
<div id="search-wrap4">
<input name="ciudad" id="search-q4" type="text" onkeyup="javascript:autosuggest4();" maxlength="12" size="15" autocomplete="off" tabindex="5"/>
<div id="res4"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td> 
			 </tr>
             <tr height="30"> 
			  <td> <label class="Texto15"> Zona : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework5.js"></script>
<style type="text/css">
#search-wrap5 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res5{width:150px; border:solid 1px #DEDEDE; display:none;}
#res5 ul, #res5 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res5 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res5 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res5 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res5 li a:hover{background:#FFFFFF;}
#res5 ul {padding:4px;}
</style>
<div id="search-wrap5">
<input name="zona" id="search-q5" type="text" onkeyup="javascript:autosuggest5();" maxlength="12" size="15" autocomplete="off" tabindex="6"/>
<div id="res5"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td> 
			 </tr>			 			 
	         <tr> 
			  <td> <label class="Texto15"> Telefono : </label> </td>
			  <td> <input type="text" name="telefono" class="Texto15" size="25" maxlength="25" tabindex="7"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Movil : </label> </td>
			  <td> <input type="text" name="celular" class="Texto15" size="25" maxlength="25" tabindex="8"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> E-mail: </label> </td>
			  <td> <input type="text" name="email" class="Texto15" size="25" maxlength="50" tabindex="9"> 
			 </tr>
             <tr height="30"> 
			  <td> <label class="Texto15"> Clasificacion : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework6.js"></script>
<style type="text/css">
#search-wrap6 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res6{width:150px; border:solid 1px #DEDEDE; display:none;}
#res6 ul, #res6 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res6 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res6 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res6 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res6 li a:hover{background:#FFFFFF;}
#res6 ul {padding:4px;}
</style>
<div id="search-wrap6">
<input name="clasificater" id="search-q6" type="text" onkeyup="javascript:autosuggest6();" maxlength="12" size="15" autocomplete="off" tabindex="10"/>
<div id="res6"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
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
  if($opcion == "detalles")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Terceros <td>
				 <td>  <a href="terceros.php"> Listado de Terceros </a> </td>
			     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 				 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
    
	$cont.= FuncionesEspeciales($id);
	
  	$vsql = "SELECT * FROM terceros WHERE terid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST" name="x">
			<input type="hidden" name="terid" value="'.$id.'">
			<table width="700">
	        <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="15" maxlength="15" id="default" value="'.$row['codigo'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> CC o NIT : </label> </td>
			  <td> <input type="text" name="nit" class="Texto15" size="15" maxlength="15" tabindex="2" value="'.$row['nit'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Nombre : </td>
			  <td> <input type="text" name="nombre" class="Texto15" maxlength="60" size="45" tabindex="3" value="'.$row['nombre'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Direccion : </td>
			  <td> <input type="text" name="direccion" class="Texto15" maxlength="60" size="45" tabindex="4" value="'.$row['direccion'].'"> 
			 </tr>			
             <tr height="30"> 
			  <td> <label class="Texto15"> Ciudad : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework4.js"></script>
<style type="text/css">
#search-wrap4 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res4{width:150px; border:solid 1px #DEDEDE; display:none;}
#res4 ul, #res4 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res4 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res4 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res4 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res4 li a:hover{background:#FFFFFF;}
#res4 ul {padding:4px;}
</style>
<div id="search-wrap4">
<input name="ciudad" id="search-q4" type="text" onkeyup="javascript:autosuggest4();" maxlength="12" size="15" autocomplete="off" tabindex="5" value="'.$clase->BDLockup($row['ciudadid'],"ciudades","ciudadid","codigo").'"/>
<div id="res4"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td> 
			 </tr>
             <tr height="30"> 
			  <td> <label class="Texto15"> Zona : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework5.js"></script>
<style type="text/css">
#search-wrap5 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res5{width:150px; border:solid 1px #DEDEDE; display:none;}
#res5 ul, #res5 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res5 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res5 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res5 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res5 li a:hover{background:#FFFFFF;}
#res5 ul {padding:4px;}
</style>
<div id="search-wrap5">
<input name="zona" id="search-q5" type="text" onkeyup="javascript:autosuggest5();" maxlength="12" size="15" autocomplete="off" tabindex="6" value="'.$clase->BDLockup($row['zonaid'],"zonater","zonaid","codigo").'"/>
<div id="res5"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td> 
			 </tr>			 			 
	         <tr> 
			  <td> <label class="Texto15"> Telefono : </label> </td>
			  <td> <input type="text" name="telefono" class="Texto15" size="25" maxlength="25" tabindex="7" value="'.$row['telefono'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Movil : </label> </td>
			  <td> <input type="text" name="celular" class="Texto15" size="25" maxlength="25" tabindex="8" value="'.$row['celular'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> E-mail: </label> </td>
			  <td> <input type="text" name="email" class="Texto15" size="25" maxlength="50" tabindex="9" value="'.$row['email'].'"> 
			 </tr>
             <tr height="30"> 
			  <td> <label class="Texto15"> Clasificacion : </td>
			  <td> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework6.js"></script>
<style type="text/css">
#search-wrap6 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res6{width:150px; border:solid 1px #DEDEDE; display:none;}
#res6 ul, #res6 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res6 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res6 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res6 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res6 li a:hover{background:#FFFFFF;}
#res6 ul {padding:4px;}
</style>
<div id="search-wrap6">
<input name="clasificater" id="search-q6" type="text" onkeyup="javascript:autosuggest6();" maxlength="12" size="15" autocomplete="off" tabindex="10" value="'.$clase->BDLockup($row['clasificaterid'],"clasificater","clasificaterid","codigo").'"/>
<div id="res6"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
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
	  $clase->Aviso(3,"No se puede Eliminar este Tercero porque es un Tercero del Sistema");
	else
	{    			
       $asociadoadoc = $clase->SeleccionarUno("SELECT COUNT(*) FROM documentos WHERE terid1=".$id." OR terid2=".$id);
       if($asociadoadoc > 0)
	      $clase->Aviso(3,"No se puede Eliminar este Tercero porque esta Asociado a Documentos");  			
       else
       {
	     $vsql = "DELETE FROM terceros WHERE terid=".$id;
         $clase->EjecutarSQL($vsql);
		 $clase->Aviso(1,"Tercero eliminado Exitosamente");  			
	   }
	}       
	header("Location: terceros.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: terceros.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT * FROM terceros WHERE codigo like '%".$criterio."%' OR nit like '%".$criterio."%' OR nombre like '%".$criterio."%' ORDER BY nombre ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_TERCEROS'] = $vsql;
	header("Location: terceros.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM terceros ORDER BY nombre ASC limit 0,30";
	$_SESSION['SQL_TERCEROS'] = "";
	header("Location: terceros.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Terceros <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_TERCEROS'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_TERCEROS'];
	if($vsql == "")
    	$vsql = "SELECT * FROM terceros ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="20">  Codigo </td>
			     <td width="25">  NIT  </td>				 
				 <td width="220"> Nombres y Apellidos </td>
				 <td width="35"> Correo Electr&oacute;nico </td>			
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
				  <td> '.$row['nit'].' </td>
				  <td> '.$row['nombre'].' </td>
				  <td> '.$row['email'].'</td>
				  <td> <a href="?opcion=estadisticas&amp;id='.$row['terid'].'" rel="facebox"> <img src="images/funciones.png" border="0"> </td>				  
                  <td> <a href="?opcion=detalles&amp;id='.$row['terid'].'"> <img src="images/seleccion.png" border="0"> </td>				  				  
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
  function FuncionesEspeciales($id)
  {
       // Barra de Acciones Especiales
  	 $cont.='<table width="100%">	
	          <tr class="BarraDocumentos"> 
			     <td width="25%" align="center"> 
				    <a href="?opcion=estadisticas&id='.$id.'"><img src="images/estadisticas.png">Estadisticas Cliente</a> </td>
			     <td width="25%" align="center"> <img src="images/icononuevo.png"> Nuevo Contacto CRM  </td>
			     <td width="25%" align="center"> <img src="images/email.png"> Enviar Email </td>
			     <td width="25%" align="center"> <img src="images/funciones.png"> Funciones Especiales </td>				 
			   </tr>	 			   
			 </table> ';	
     return($cont);			 
  }
  
?> 