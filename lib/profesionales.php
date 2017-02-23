<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  /////////////////////////////////////////////////////////////////////////    
  if($opcion == "cargarfirma")
  {
    $id = $_GET['id'];
    $cedula = $clase->BDLockup($id,"terceros","terid","nit");   
  
    $tienefirma = $clase->BDLockup($id,"terceros","terid","rutafirma");   
    if(($tienefirma == "")||(strtoupper($_SESSION['USERNAME']) == "ADMINISTRADOR")){ 
      copy('tomarfirma/images/'.$_SESSION['USERNAME'].'.png','firmas/'.$cedula.'.png');
      $vsql="UPDATE terceros SET rutafirma='".$cedula.".png' WHERE terid =".$id;
      $clase->EjecutarSQL($vsql);
 	  $clase->Aviso(1,"Firma Asociada al Profesional Exitosamente");  			
    }
    else
     $clase->Aviso(2,'El Paciente YA TIENE una Firma. Solo el Usuario ADMINISTRADOR puede reemplazarle la Firma'); 
   
    header('Location: profesionales.php');
  } 

  /////////////////////////////////////////  
  if($opcion == "desasociar")
  {
     $vsql = "UPDATE terceros SET username='' WHERE terid=".$terid;    
	 $cant = $clase->EjecutarSQL($vsql);
	 $clase->Aviso(1,"Profesional Ya no esta vinculado a ningun usuario del Sistema");  	
	 header("Location: profesionales.php");   
  }
  
  /////////////////////////////////////////  
  if($opcion == "addusuario2")
  {
    $terid    = $_GET['terid'];
    $username = $_GET['username'];
	if(($terid != '')&&($username != ''))
	{
	   $vsql = "UPDATE terceros SET username='".$username."' WHERE terid=".$terid;    
	   $cant = $clase->EjecutarSQL($vsql);

	   $vsql = "UPDATE usuarios SET teridprof=".$terid." WHERE username='".$username."'";    
	   $cant = $clase->EjecutarSQL($vsql);
	   
	   $clase->Aviso(1,"Usuario <b>".$username."</b> Asociado Exitosamente al Profesional");  	
	}
	else
	   $clase->Aviso(3,"Error al Asociar el Usuario al profesional");  	
  
    header("Location: profesionales.php");    
  }
  
  /////////////////////////////////////////  
  if($opcion == "addusuario")
  {
    $terid = $_GET['id'];
    $cont.='<h3> Seleccionar Usuario del Sistema </h3><center>
			<table width="450">';
			
	$vsql   = "SELECT * FROM usuarios WHERE username NOT IN (select USERNAME from terceros)";
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex); 

    $i = 0;	
	while($row = mysql_fetch_array($result))
	{
	  $i++;
	  if($i%2 == 0)
	    $cont.='<tr class="TablaDocsImPar">';
	  else
	    $cont.='<tr class="TablaDocsPar">';		 

	  $cont.='<td width="15"> </td>
			    <td> <li>'.strtoupper($row['nombre']).'</td> 
				<td align="center"> <a href="?opcion=addusuario2&terid='.$terid.'&username='.$row['username'].'"> Asociar a este Usuario </a> </td> 
			    <td width="15"> </td>
			  </tr>';		 		
	}

    $cont.='<tr class="TablaDocsPar">';		 
    $cont.='<td width="15"> </td>
			    <td> <b>Eliminar Usuario Actual</td> 
				<td align="center"> <a href="?opcion=desasociar&terid='.$terid.'"> Eliminar Asociacion </a> </td> 
			    <td width="15"> </td>
			  </tr>';		 		

	$cont.='</table><br>';
	echo $cont;
	exit(0);		
  }


  /////////////////////////////////////////  
  if($opcion == "disponibilidad")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/profesionales.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Disponibilidad Agenda del Profesional <td>
				 <td>  <a href="profesionales.php"> Listado de Profesionales </a> </td>
			     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 				 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
    
	$cont.= FuncionesEspeciales(2);
	
  	$vsql = "SELECT * FROM terceros WHERE terid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	$cont.='<br><center>
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
	$nombre1       = strtoupper($_POST['nombre1']);	
	$nombre2       = strtoupper($_POST['nombre2']);	
	$apellido1     = strtoupper($_POST['apellido1']);	
	$apellido2     = strtoupper($_POST['apellido2']);	
	$nombrecomp    = $nombre1." ".$nombre2." ".$apellido1." ".$apellido2;
	$nombre        = strtoupper($_POST['nombre']);	
    $direccion     = strtoupper($_POST['direccion']);		
    $ciudad        = trim($_POST['ciudad']);			
    $zona          = trim($_POST['zona']);				
	$telefono      = strtoupper($_POST['telefono']);
	$celular       = strtoupper($_POST['celular']);		
	$email         = strtolower($_POST['email']);    
    $profesion     = strtoupper($_POST['profesion']);		
    $fechanac      = substr($_POST['fechanac'],6,4)."/".substr($_POST['fechanac'],3,2)."/".substr($_POST['fechanac'],0,2);
    $registropro   = strtoupper($_POST['registropro']);			
    $cuenta        = strtoupper($_POST['cuenta']);			
    $bancoid       = $_POST['bancoid'];				
	$especialidadid = $_POST['especialidadid'];				
		
	// Valido que el grupo y la linea esten en la base de datos
	$ciudadid = $clase->BDLockup($ciudad,"ciudades","codigo","ciudadid");
    $zonaid   = $clase->BDLockup($zona,"zonater","codigo","zonaid"); 
    $clasificaterid = $clase->BDLockup("03","clasificater","codigo","clasificaterid");
 	
	if($ciudadid == ""){
	  $clase->Aviso(2,"Ciudad Incorrecta. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: profesionales.php");
	  exit();
	}
    
	if($zonaid == ""){	
	  $clase->Aviso(2,"Zona del Tercero Incorrecta. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: profesionales.php");
	  exit();
	}	
    if($bancoid == "")
	  $bancoid = 1;
	  
	if($opcion == "guardarnuevo")
	{		
		$vsql = "INSERT INTO terceros(codigo,nit,nombre,direccion,ciudadid,telefono,celular,email,zonaid,clasificaterid,
		         profesion,registropro,fechanac,cuenta,bancoid,especialidadid,creador,momento)
		         values('".$codigo."','".$nit."','".$nombrecomp."','".$direccion."',".$ciudadid.",'".$telefono."','".$celular.
				 "','".$email."',".$zonaid.",".$clasificaterid.",'".$profesion."','".$registropro."','".$fechanac.
				 "','".$cuenta."',".$bancoid.",".$especialidadid.",'".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";    

		$cant = $clase->EjecutarSQL($vsql);

		if($cant == 1)
    		$clase->Aviso(1,"Profesional creado Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE terceros SET codigo = '".$codigo."' , nit = '".$nit."' , nombre = '".$nombre."' ,
		         direccion = '".$direccion."' , ciudadid = ".$ciudadid." , telefono = '".$telefono."' ,
		         celular = '".$celular."' , email = '".$email."' , zonaid = ".$zonaid." ,				 
		         clasificaterid = ".$clasificaterid.", profesion='".$profesion."' , registropro ='".$registropro."' ,   
				 fechanac ='".$fechanac."' , cuenta ='".$cuenta."' , bancoid =".$bancoid.", especialidadid =".$especialidadid." ,
				 creador = '".$_SESSION['USERNAME']."', momento = CURRENT_TIMESTAMP WHERE terid=".$terid;

        $clase->EjecutarSQL($vsql);
		
  		$clase->Aviso(1,"Profesional modificado Exitosamente");  			  
    }	
	
	header("Location: profesionales.php");
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
			     <td width="37"> <img src="images/iconos/profesionales.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Profesional de la Salud <td>
				 <td>  <a href="profesionales.php"> Listado de Profesionales </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';
	
	$cont.='<br><center>
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
			  <td> <label class="Texto15"> Nombres : </td>
			  <td> 
			       <input type="text" name="nombre1" class="Texto15" maxlength="60" size="20" tabindex="3"> 
			       <input type="text" name="nombre2" class="Texto15" maxlength="60" size="20" tabindex="4">
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Apellidos : </td>
			  <td> 
			       <input type="text" name="apellido1" class="Texto15" maxlength="60" size="20" tabindex="5"> 
			       <input type="text" name="apellido2" class="Texto15" maxlength="60" size="20" tabindex="6">
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
			  <td> <input type="text" name="telefono" class="Texto15" size="25" maxlength="25" tabindex="7"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Movil : </label> </td>
			  <td> <input type="text" name="celular" class="Texto15" size="25" maxlength="25" tabindex="8"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> E-mail: </label> </td>
			  <td> <input type="text" name="email" class="Texto15" size="25" maxlength="50" tabindex="9"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Fecha Nacimiento: </label> </td>
			  <td> <input type="text" name="fechanac" class="Texto15" size="10" maxlength="10" tabindex="9" value="'.date("d/m/Y").'"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Profesion : </label> </td>
			  <td> <input type="text" name="profesion" class="Texto15" size="25" maxlength="50" tabindex="9"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Registro Prof : </label> </td>
			  <td> <input type="text" name="registropro" class="Texto15" size="25" maxlength="50" tabindex="9"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Especialidad : </label> </td>
			  <td> '.$clase->CrearCombo("especialidadid","especialidades","descripcion","especialidadid","","S","descripcion").'</td> 
			 </tr>         
	         <tr> 
			  <td> <label class="Texto15"> Cuenta Bancaria No. </label> </td>
			  <td> <input type="text" name="cuenta" class="Texto15" size="25" maxlength="50" tabindex="9"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Banco : </label> </td>
			  <td> '.$clase->CrearCombo("bancoid","bancos","descripcion","codigo","","S","descripcion").' </td> 
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
			     <td width="37"> <img src="images/iconos/profesionales.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Profesionales de la Salud <td>
				 <td>  <a href="profesionales.php"> Listado de Profesionales </a> </td>
			     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 				 				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
			 
    $cont.= FuncionesEspeciales(1);
  	
	$vsql = "SELECT * FROM terceros WHERE terid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	 $fecha= substr($row['fechanac'],8,2)."/".substr($row['fechanac'],5,2)."/".substr($row['fechanac'],0,4); 
	 $cont.='<br><center>
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
	         <tr> 
			  <td> <label class="Texto15"> Fecha Nacimiento: </label> </td>
			  <td> <input type="text" name="fechanac" class="Texto15" size="12" maxlength="10" tabindex="9" value="'.$fecha.'"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Profesion : </label> </td>
			  <td> <input type="text" name="profesion" class="Texto15" size="25" maxlength="50" tabindex="9" value="'.$row['profesion'].'"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Registro Prof : </label> </td>
			  <td> <input type="text" name="registropro" class="Texto15" size="25" maxlength="50" tabindex="9" value="'.$row['registropro'].'"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Especialidad : </label> </td>
			  <td> '.$clase->CrearCombo("especialidadid","especialidades","descripcion","codigo",$row['especialidadid'],"S","descripcion").'</td> 
			 </tr>         
	         <tr> 
			  <td> <label class="Texto15"> Cuenta Bancaria No. </label> </td>
			  <td> <input type="text" name="cuenta" class="Texto15" size="25" maxlength="50" tabindex="9" value="'.$row['cuenta'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Banco : </label> </td>
			  <td> '.$clase->CrearCombo("bancoid","bancos","descripcion","codigo",$row['bancoid'],"S","descripcion").' </td> 
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
		 $clase->Aviso(1,"Profesional eliminado Exitosamente");  			
	   }
	}       
	header("Location: profesionales.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: profesionales.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
   	$vsql = "SELECT T.* , E.descripcion espe
	         FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
			 LEFT JOIN especialidades E ON (E.especialidadid = T.especialidadid)
		     WHERE CT.codigo = '03' AND (codigo like '%".$criterio."%' OR nit like '%".$criterio."%' OR nombre like '%".$criterio."%') ORDER BY nombre ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_PROFESIONALES'] = $vsql;
	header("Location: profesionales.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
   	$vsql = "SELECT T.* , E.descripcion espe
	         FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
			 LEFT JOIN especialidades E ON (E.especialidadid = T.especialidadid)
             WHERE CT.codigo = '03' ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	$_SESSION['SQL_PROFESIONALES'] = "";
	header("Location: profesionales.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/profesionales.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Profesionales de la Salud <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_PROFESIONALES'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_TERCEROS'];
	if($vsql == "")
	{
     	$vsql = "SELECT T.* , E.descripcion espe
		         FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
				 LEFT JOIN especialidades E ON (E.especialidadid = T.especialidadid)
     	         WHERE CT.codigo = '03' ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];				 
    }

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="20">  Codigo </td>
			     <td width="50">  NIT  </td>				 
				 <td width="290"> Nombres y Apellidos </td>
				 <td width="190"> Especialidad </td>				 
				 <td width="60"> Correo Electr&oacute;nico </td>			
				 <td width="20"> </td>
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
		          
		 $cont.=' <td width="10"> </td>
				  <td width="20"> '.$row['codigo'].' </td>
				  <td width="50"> '.$row['nit'].' </td>
				  <td width="290"> '.$row['nombre'].' </td>
				  <td width="190">  '.$row['espe'].' </td>				  
				  <td width="60"> '.$row['email'].'</td>
				  <td width="20"> <a href="?opcion=addusuario&amp;id='.$row['terid'].'" rel="facebox"> 
				                   <img src="images/usuarios.png" border="0" width="18" height="18"> </td>
                  <td width="20"> <a href="#" onClick="window.open(\'tomarfirma/main_eng.swf\',\'Firma\',\'width=900,height=600\');"> 
				                   <img src="images/firma.png" border="0"> </a> </td>
				  <td width="20"> <a href="?opcion=cargarfirma&id='.$row['terid'].'"> <img src="images/iconorefrescar.png" border="0"> </td>						   
                  <td width="20"> <a href="?opcion=detalles&amp;id='.$row['terid'].'"> <img src="images/seleccion.png" border="0"> </td>				  				  
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

  ////////////////////////////////
  ////////////////////////////////  
  function FuncionesEspeciales($item)
  {
     $id = $_GET['id'];
	 $clase = new Sistema();
	 $nombre = $clase->BDLockup($id,"terceros","terid","nombre");

	 // Barra de Acciones Especiales
  	 $cont.='<table width="100%">	
	          <tr class="BarraDocumentos">';
     if($item == 1)			  
	    $cont.='<td width="25%" class="BarraDocumentosSel" align="center"> <img src="images/estadisticas.png">Datos Generales</td>';
	 else	
        $cont.='<td width="25%" align="center"> <a href="?opcion=detalles&id='.$id.'"><img src="images/estadisticas.png">Datos Generales</a> </td>';	 

     if($item == 2)			  
	    $cont.='<td width="25%" class="BarraDocumentosSel" align="center"> <img src="images/estadisticas.png">Disponibilidad Agenda </td>';
	 else	
        $cont.='<td width="25%" align="center"> <a href="?opcion=disponibilidad&id='.$id.'"><img src="images/estadisticas.png">Disponibilidad Agenda</a> </td>';	 

	 $cont.='   <td width="5%"> </td>				 
	            <td width="45%"> <b> Profesional : </b>'.$nombre.'</td>				 
			  </tr>	 			   
			 </table>';	
     return($cont);			 
  }
  
  
?> 