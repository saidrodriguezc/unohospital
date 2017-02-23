<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];


  /////////////////////////////////////////  
  if($opcion == "guardarfirma")
  {
     $terid = $_POST['terid'];
     $nombre_archivo = $HTTP_POST_FILES['userfile']['name']; 
     $tipo_archivo = $HTTP_POST_FILES['userfile']['type']; 
     $tamano_archivo = $HTTP_POST_FILES['userfile']['size']; 
	 
	 $nombrefinal = $terid.substr($nombre_archivo,(strlen($nombre_archivo)-4),strlen($nombre_archivo));
	 
     if (move_uploaded_file($HTTP_POST_FILES['userfile']['tmp_name'],'firmas/'.$nombrefinal))
	 { 
	     $clase->EjecutarSQL("UPDATE terceros SET rutafirma='".$nombrefinal."' WHERE terid=".$terid);	 
      	 $clase->Aviso(1,'Firma Asignada Exitosamente'); 		 
   	 }
	else
	{ 
         $clase->Aviso(2,'Error al Asignar la Firma'); 
   	} 
	header("Location: pacientes.php");
  } 

  /////////////////////////////////////////  
  if($opcion == "cargarfirma")
  {
    $id = $_GET['id'];
    echo'<form action="pacientes.php?opcion=guardarfirma" method="post" enctype="multipart/form-data"> 
   	 <b>Seleccione Archivo Firma</b> 
   	 <br> 
     <input name="userfile" type="file"> 
	 <br><br>
   	 <input type="hidden" name="MAX_FILE_SIZE" value="100000"> 
   	 <input type="hidden" name="terid" value="'.$id.'"> 	 
     <input type="submit" value="Cargar Firma">
	 </form>';
	exit();
  }	 

  /////////////////////////////////////////  
  if($opcion == "info")
  {
    $id = $_GET['id'];
    $vsql = "SELECT * FROM terceros WHERE terid=".$id;
	
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
    if($row = mysql_fetch_array($result))
	{
       $rutafoto = $row['rutafoto'];
	   if($rutafoto == "")
		 $rutafoto = "nofoto.png";

       $rutafirma = $row['rutafirma'];
	   if($rutafirma == "")
		 $rutafirma = "nofoto.png";

	
       if($row['genero'] == "M")
	     $genero = "Masculino";
       if($row['genero'] == "F")
	     $genero = "Femenino";
       if($row['genero'] == "O")
	     $genero = "Otro";
		 
	   $cont='<table width="800">
	           <tr class="CabezoteTabla"> 
				 <td align="center"> <b> Informaci&oacute;n del Paciente </b> <td> 
			   </tr> 
			</table>
		    <table width="800">
			  <tr class="BarraDocumentos">
			    <td> <img src="fotos/'.$rutafoto.'" border="0" width="180" height="120"> </td>
				<td width="20"> </td> 
				<td align="left" width="650"> 
				  <b> Cedula : </b>'.$row['nit'].' <br>
				  <b> Nombre : </b>'.$row['nombre'].' <br> 
				  <b> Genero : </b>'.$genero.' <br>  
				  <b> Fecha Nacim : </b>'.substr($row['fechanac'],8,2)."/".substr($row['fechanac'],5,2)."/".substr($row['fechanac'],0,4).'<br>
				  <b> Edad </b>'.$row['edad'].' a&ntilde;os <br>
				  <b> Telefono : </b>'.$row['telefono'].' '.$row['celular'].'<br>
			    </td>
			    <td> <img src="firmas/'.$rutafirma.'" border="0" width="160" height="120"> </td>				
			  </tr>
 			</table><br>'; 
	    
	}
    echo $cont;	
    exit();
  }
  
  
  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $terid         = $_POST['terid'];
    $cedula        = strtoupper($_POST['cedula']);	
    $nombre1       = strtoupper($_POST['nombre1']);
	$nombre2       = strtoupper($_POST['nombre2']);
	$apellido1     = strtoupper($_POST['apellido1']);
	$apellido2     = strtoupper($_POST['apellido2']);
	$nombre        = strtoupper($_POST['nombre1']).' '.strtoupper($_POST['nombre2']).' '.strtoupper($_POST['apellido1']).' '.strtoupper($_POST['apellido2']);	
    $direccion     = strtoupper($_POST['direccion']);		
    $ciudad        = trim($_POST['ciudad']);			
    $telefono      = strtoupper($_POST['telefono']);
	$celular       = strtoupper($_POST['celular']);		
	$email         = strtolower($_POST['email']);
	$cargo         = strtoupper($_POST['cargo']);	
	$clasificaid   = $clase->BDLockup("01","clasificater","codigo","clasificaterid");	
    $fechanac      = substr($_POST['fechanac'],6,4)."/".substr($_POST['fechanac'],3,2)."/".substr($_POST['fechanac'],0,2);
	$edad          = $_POST['edad'];
	$genero        = strtoupper($_POST['genero']);
	$estadocivilid = $_POST['estadocivilid'];
	$nivelid       = $_POST['nivelid'];
	$entidadid     = $_POST['entidadid'];	
	
		
	// Valido que el grupo y la linea esten en la base de datos
	
    if(($cedula == "")||($nombre1 == "")||($apellido1 == "")){
	  $clase->Aviso(2,"Los campos Cedula - Nombre - Apellido No pueden ser vacios &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: terceros.php");
	  exit();
	}

    $ciudadid = $clase->BDLockup($ciudad,"ciudades","codigo","ciudadid");
	if($ciudadid == ""){
	  $clase->Aviso(2,"Ciudad Incorrecta. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: terceros.php");
	  exit();
	}

	if($clasificaid == ""){
	  $clase->Aviso(2,"Clasificacion. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: terceros.php");
	  exit();
	}
 
	////////////////////////////////////////////////////////////////////////////////////
	if($opcion == "guardarnuevo")
	{
		$clientedesde  = date("y-m-d")." 00:00:00";
		$pacientedesde = date("y-m-d");
		$provdesde     = "0000-00-00 00:00:00";
		
		$vsql = "INSERT INTO terceros(codigo,nit,nombre,direccion,ciudadid,telefono,celular,email,cargo,zonaid,clasificaterid,nombre1,nombre2,
		         apellido1,apellido2,fechanac,edad,genero,estadocivilid,nivelid,entidadid,clientedesde,pacientedesde,provdesde,creador,momento)
		         values('".$cedula."','".$cedula."','".$nombre."','".$direccion."',".$ciudadid.",'".$telefono."','".$celular."','".$email."','".$cargo."',1,".
				 $clasificaid.",'".$nombre1."','".$nombre2."','".$apellido1."','".$apellido2."','".$fechanac."',".$edad.",'".$genero."','".$estadocivilid.
				 "',".$nivelid.",".$entidadid.",'".$clientedesde."','".$pacientedesde."','".$provdesde."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";    
			  
		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant >= 0)
		{
    	    $clase->Aviso(1,'Tercero Creado <a href="facturapida.php?opcion=eligecontrato&cliente='.$cedula.'" rel="facebox">Indicar Procedimientos para '.$nombre1.' '.$apellido1.'?</a>');  	
			echo'
			  <script language="javascript">			  
			    window.open(\'tomarfoto/\',\'Foto\',\'width=800,height=500\');
			    document.location.href=\'pacientes.php\';			  
			  </script>';
		}
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        if($nivelid == "")
		  $nivelid = 0;
		
		if($entidadid == "")
		  $entidadid = 0;
        
		if($estadocivilid == "")
		  $estadocivilid = '0';
		  
		$vsql = "UPDATE terceros SET codigo = '".$cedula."' , nit = '".$cedula."' , nombre = '".$nombre."' ,
		         direccion = '".$direccion."' , ciudadid = ".$ciudadid." , telefono = '".$telefono."' ,
		         celular = '".$celular."', email = '".$email."' , cargo = '".$cargo."' , fechanac = '".$fechanac."' , edad=".$edad." , 
				 nombre1 = '".$nombre1."',nombre2 = '".$nombre2."',apellido1 = '".$apellido1."',apellido2 = '".$apellido2."' , 
				 nivelid = ".$nivelid." , entidadid = ".$entidadid." , genero = '".$genero."' , estadocivilid = '".$estadocivilid."' , 
				 clasificaterid =".$clasificaid." , momento = CURRENT_TIMESTAMP				 				 
	             WHERE terid=".$terid;

        $clase->EjecutarSQL($vsql);
	
  		$clase->Aviso(1,'Tercero Guardado <a href="facturapida.php?opcion=eligecontrato&cliente='.$cedula.'" rel="facebox">Indicar Procedimientos para '.$nombre1.' '.$apellido1.'?</a>');  	
  		header("Location: pacientes.php");
    }		
  }

  /////////////////////////////////////////////////////////////////////////    
  if($opcion == "cargarfoto")
  {
  
   $id = $_GET['id'];
   $cedula = $clase->BDLockup($id,"terceros","terid","nit");   
  
   $tienefoto = $clase->BDLockup($id,"terceros","terid","rutafoto");   
   
   if(($tienefoto == "")||(strtoupper($_SESSION['USERNAME']) == "ADMINISTRADOR")){ 
     copy('tomarfoto/images/ultimafoto.jpg','fotos/'.$cedula.'.jpg');
     $vsql="UPDATE terceros SET rutafoto='".$cedula.".jpg' WHERE terid =".$id;
     $clase->EjecutarSQL($vsql);
   }
   else
     $clase->Aviso(2,'El Paciente YA TIENE una Fotografia. Solo el Usuario ADMINISTRADOR puede reemplazarle la Foto'); 
   
   $tienefirma = $clase->BDLockup($id,"terceros","terid","rutafirma");   
   if(($tienefirma == "")||(strtoupper($_SESSION['USERNAME']) == "ADMINISTRADOR")){ 
   copy('tomarfirma/images/'.$_SESSION['USERNAME'].'.png','firmas/'.$cedula.'.png');
   $vsql="UPDATE terceros SET rutafirma='".$cedula.".png' WHERE terid =".$id;
   $clase->EjecutarSQL($vsql);
   }
   else
     $clase->Aviso(2,'El Paciente YA TIENE una Firma. Solo el Usuario ADMINISTRADOR puede reemplazarle la Firma'); 
   
   header('Location: pacientes.php');
  } 
	 
  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////    
  if($opcion == "nuevo")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W");
	 $cont.='<script src="popcalendar.js" type="text/javascript"></script>
	          <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Paciente <td>
				 <td>  <a href="pacientes.php"> Listado de Pacientes </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table>';	
  	
	$cont.='<br><br>
	        <script language="javascript">

              function tipofecha()
			  {
			    var fecnaci = document.x.fechanac.value;
				if(fecnaci.length == 2) 
				  document.x.fechanac.value = document.x.fechanac.value + "/"; 
				if(fecnaci.length == 5) 
				  document.x.fechanac.value = document.x.fechanac.value + "/"; 
				return;
			  }

		 	  function calcular_edad(dia_nacim,mes_nacim,anio_nacim)
			  {
			    fecha_hoy = new Date();
			    ahora_anio = fecha_hoy.getYear();
			    ahora_mes = fecha_hoy.getMonth();
			    ahora_dia = fecha_hoy.getDate();
			    edad = (ahora_anio + 1900) - anio_nacim;
			    if ( ahora_mes < (mes_nacim - 1))
			    {
			      edad--;
			    }
			    if (((mes_nacim - 1) == ahora_mes) && (ahora_dia < dia_nacim))
			    { 
			      edad--;
			    }
			    if (edad > 1900)
			    {
			    edad -= 1900;
			    }
			    return edad;
			  }
			
			  function CalcularEdad()
			  {
			    var fecnaci = document.x.fechanac.value;
				if(fecnaci != "")
   				  document.x.edad.value = calcular_edad(fecnaci.substr(0,2),fecnaci.substr(3,2),fecnaci.substr(6,4)); 
			  }
			</script>

	        <center>
            <form action="?opcion=guardarnuevo" method="POST" name="x">
			<table width="700">
	         <tr> 
			  <td> <label class="Texto15"> Docum Identidad : </label> </td>
			  <td> <input type="text" name="cedula" class="Texto15" size="20" maxlength="15" tabindex="2"> 
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
			  <td> <label class="Texto15"> Fecha Nac : </td>
			  <td> 
			       <input type="text" name="fechanac" class="Texto15" maxlength="60" size="10" tabindex="7" value="'.$fechanac.'" Onkeypress="tipofecha();"> 
			       Edad
			       <input type="text" name="edad" class="Texto15" maxlength="3" size="1" tabindex="8" OnFocus="CalcularEdad();"> A&ntilde;os </td>
			 </tr>			
	         <tr> 
			  <td> <label class="Texto15"> Direccion : </td>
			  <td> <input type="text" name="direccion" class="Texto15" maxlength="60" size="45" tabindex="9"> 
			 </tr>			
	         <tr> 
			  <td> <label class="Texto15"> Telefono : </label> </td>
			  <td> <input type="text" name="telefono" class="Texto15" size="20" maxlength="20" tabindex="10"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Movil : </label> </td>
			  <td> <input type="text" name="celular" class="Texto15" size="20" maxlength="20" tabindex="11"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> E-mail: </label> </td>
			  <td> <input type="text" name="email" class="Texto15" size="30" maxlength="50" tabindex="12"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Cargo : </label> </td>
			  <td> <input type="text" name="cargo" class="Texto15" size="30" maxlength="50" tabindex="12"> 
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
<input name="ciudad" id="search-q4" type="text" onkeyup="javascript:autosuggest4();" maxlength="12" size="15" autocomplete="off" tabindex="13" value="54001"/>
<div id="res4"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td> 
			 </tr>			 
	         <tr> 
			  <td> <label class="Texto15"> Genero : </label> </td>
			  <td> 
			       <input type="radio" name="genero" value="F" checked> Femenino
			       <input type="radio" name="genero" value="M"> Masculino
			       <input type="radio" name="genero" value="O"> Otro			  
			  </td>
			 </tr>	
	         <tr> 
			  <td> <label class="Texto15"> Estado Civil : </label> </td>
			  <td> '.$clase->CrearCombo("estadocivilid","estadocivil","descripcion","codigo","","S","codigo").'</td>
			 </tr>	
	         <tr> 
			  <td> <label class="Texto15"> Nivel Educativo : </label> </td>
			  <td> '.$clase->CrearCombo("nivelid","niveledu","descripcion","nivelid","","S","codigo").' </td>
			 </tr>	
	         <tr> 
			  <td> <label class="Texto15"> Entidad EPS : </label> </td>
			  <td> '.$clase->CrearCombo("entidadid","entidades","descripcion","entidadid","","S","descripcion").' </td>
			 </tr>	
			</table>
			
			<br><br>
			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="20" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>';  //onclick="location.href=\'?opcion=eliminar&amp;id='.$row['ciudadid']	 	
  }

  /////////////////////////////////////////  
  if($opcion == "detalles")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<script src="popcalendar.js" type="text/javascript"></script>
	          <table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/pacientes.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Informacion de Pacientes <td>
				 <td>  <a href="pacientes.php"> Listado de Pacientes </a> </td>
    		     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>				 
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
  	$vsql = "SELECT * FROM terceros WHERE terid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	  $ciudad = $clase->BDLockup($row['ciudadid'],'ciudades','ciudadid','codigo');
	  $fechanac = substr($row['fechanac'],8,2)."/".substr($row['fechanac'],5,2)."/".substr($row['fechanac'],0,4);
	  
	$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST" name="x">
			<input type="hidden" name="terid" value="'.$id.'">
	        <script language="javascript">

		 	  function calcular_edad(dia_nacim,mes_nacim,anio_nacim)
			  {
			    fecha_hoy = new Date();
			    ahora_anio = fecha_hoy.getYear();
			    ahora_mes = fecha_hoy.getMonth();
			    ahora_dia = fecha_hoy.getDate();
			    edad = (ahora_anio + 1900) - anio_nacim;
			    if ( ahora_mes < (mes_nacim - 1))
			    {
			      edad--;
			    }
			    if (((mes_nacim - 1) == ahora_mes) && (ahora_dia < dia_nacim))
			    { 
			      edad--;
			    }
			    if (edad > 1900)
			    {
			    edad -= 1900;
			    }
			    return edad;
			  }
			
			  function CalcularEdad()
			  {
			    var fecnaci = document.x.fechanac.value;
				if(fecnaci != "")
   				  document.x.edad.value = calcular_edad(fecnaci.substr(0,2),fecnaci.substr(3,2),fecnaci.substr(6,4)); 
			  }
			</script>

	        <center>
            <form action="?opcion=guardarnuevo" method="POST" name="x">
			<table width="700">
	         <tr> 
			  <td> <label class="Texto15"> Docum Identidad : </label> </td>
			  <td> <input type="text" name="cedula" class="Texto15" size="20" maxlength="15" tabindex="2" value="'.$row['nit'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Nombres : </td>
			  <td> 
			       <input type="text" name="nombre1" class="Texto15" maxlength="60" size="20" tabindex="3" value="'.$row['nombre1'].'"> 
			       <input type="text" name="nombre2" class="Texto15" maxlength="60" size="20" tabindex="4" value="'.$row['nombre2'].'">
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Apellidos : </td>
			  <td> 
			       <input type="text" name="apellido1" class="Texto15" maxlength="60" size="20" tabindex="5" value="'.$row['apellido1'].'"> 
			       <input type="text" name="apellido2" class="Texto15" maxlength="60" size="20" tabindex="6" value="'.$row['apellido2'].'">
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Fecha Nac : </td>
			  <td> 
			       <input type="text" name="fechanac" class="Texto15" maxlength="60" size="10" tabindex="7" value="'.$fechanac.'" id="fechanac" onClick="popUpCalendar(this, x.fechanac,\'dd/mm/yyyy\');"> 
			       Edad
			       <input type="text" name="edad" class="Texto15" maxlength="3" size="1" tabindex="8" OnFocus="CalcularEdad();"  value="'.$row['edad'].'">  A&ntilde;os </td>
			 </tr>			
	         <tr> 
			  <td> <label class="Texto15"> Direccion : </td>
			  <td> <input type="text" name="direccion" class="Texto15" maxlength="60" size="45" tabindex="9" value="'.$row['direccion'].'"> 
			 </tr>			
	         <tr> 
			  <td> <label class="Texto15"> Telefono : </label> </td>
			  <td> <input type="text" name="telefono" class="Texto15" size="20" maxlength="20" tabindex="10" value="'.$row['telefono'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Movil : </label> </td>
			  <td> <input type="text" name="celular" class="Texto15" size="20" maxlength="20" tabindex="11" value="'.$row['celular'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Cargo : </label> </td>
			  <td> <input type="text" name="cargo" class="Texto15" size="30" maxlength="50" tabindex="12"  value="'.$row['cargo'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> E-mail: </label> </td>
			  <td> <input type="text" name="email" class="Texto15" size="30" maxlength="50" tabindex="12"  value="'.$row['email'].'"> 
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
<input name="ciudad" id="search-q4" type="text" onkeyup="javascript:autosuggest4();" maxlength="12" size="15" autocomplete="off" tabindex="13"  value="'.$ciudad.'"/>
<div id="res4"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
		  
			  </td> 
			 </tr>			 
	         <tr> 
			  <td> <label class="Texto15"> Genero : </label> </td>
			  <td>'; 
			       if($row['genero'] == "F")
				     $cont.='<input type="radio" name="genero" value="F" checked> Femenino';
				   else	 
				     $cont.='<input type="radio" name="genero" value="F" checked> Femenino';
				   
				   if($row['genero'] == "M")					 
			         $cont.='<input type="radio" name="genero" value="M" checked> Masculino';
			       else
				     $cont.='<input type="radio" name="genero" value="M"> Masculino';
				
                   if($row['genero'] == "O")					  				
				     $cont.='<input type="radio" name="genero" value="O" checked> Otro';
                   else
                     $cont.='<input type="radio" name="genero" value="O"> Otro';
					 
			 $cont.='</td>
			 </tr>	
	         <tr> 
			  <td> <label class="Texto15"> Estado Civil : </label> </td>
			  <td> '.$clase->CrearCombo("estadocivilid","estadocivil","descripcion","codigo",$row['estadocivilid'],"S","descripcion").'</td>
			 </tr>	
	         <tr> 
			  <td> <label class="Texto15"> Nivel Educativo : </label> </td>
			  <td> '.$clase->CrearCombo("nivelid","niveledu","descripcion","nivelid",$row['nivelid'],"S","codigo").' </td>
			 </tr>	
	         <tr> 
			  <td> <label class="Texto15"> Entidad EPS : </label> </td>
			  <td> '.$clase->CrearCombo("entidadid","entidades","descripcion","entidadid",$row['entidadid'],"S","descripcion").' </td>
			 </tr>	
			</table>
			
			<br><br>
			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="20" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>';  //onclick="location.href=\'?opcion=eliminar&amp;id='.$row['ciudadid']	 	
    }
  }
  
  /////////////////////////////////////////  
  if($opcion == "eliminar")
  {
    $id = $_GET['id'];
    $vsql = "DELETE FROM terceros WHERE terid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Tercero Eliminado Exitosamente");  		
	header("Location: pacientes.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: pacientes.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT T.* FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
		     WHERE CT.codigo = '01' AND nit <> 'CLI00001' AND (T.codigo like '%".$criterio."%' OR T.nit like '%".
			 $criterio."%' OR T.nombre like '%".$criterio."%') ORDER BY T.nombre ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
			 
    $_SESSION['SQL_PACIENTES'] = $vsql;
	header("Location: pacientes.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
   	$vsql = "SELECT T.* FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
             WHERE CT.codigo = '01' ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	$_SESSION['SQL_PACIENTES'] = "";
	header("Location: pacientes.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
    
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/pacientes.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Pacientes de la IPS<td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_PACIENTES'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_PACIENTES'];
	if($vsql == "")
	{
    	$vsql = "SELECT T.* FROM terceros T INNER JOIN clasificater CT ON (T.clasificaterid = CT.clasificaterid)
		         WHERE CT.codigo = '01' ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    }	
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<div style="overflow:auto; height:580px;width:796px;">
	          <table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="25">  Foto </td>				 
			     <td width="20">  Cedula </td>
				 <td width="170"> Nombres y Apellidos </td>
				 <td width="10" align="right">  Edad </td>			
				 <td width="10"> </td>			
				 <td width="25">  Celular </td>							 
				 <td width="25">  Telefono </td>
				 <td width="20"> </td>
				 <td width="20"> </td>
				 <td width="20"> </td>				 
				 <td width="20"> </td>
				 <td width="20"> </td>				 				 
				 <td width="5"> </td>				 				 
			   </tr>';	
    $i = 0;
    while($row = mysql_fetch_array($result)) 
	{
	     $rutafoto = $row['rutafoto'];
		 if($rutafoto == "")
		   $rutafoto = "nofoto.png";
		 
		 $i++;
		 if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		 else
		   $cont.='<tr class="TablaDocsImPar">';		 
		          
		 $cont.=' <td width="10"> </td>
				  <td width="25"> <a href="?opcion=info&amp;id='.$row['terid'].'" rel="facebox">
				                  <img src="fotos/'.$rutafoto.'" border="0" width="35" height="25"> </a> </td>		 
				  <td width="20"> '.$row['nit'].' </td>
				  <td width="170"> '.substr($row['nombre'],0,33).' </td>
				  <td width="10" align="right"> '.$row['edad'].' </td>
				  <td width="10"> </td>
				  <td width="25"> '.$row['celular'].'</td>
				  <td width="25"> '.$row['telefono'].'</td>				  
  			      <td width="20"> <a href="#" onClick="window.open(\'tomarfoto/index.php?id='.$row['terid'].'\',\'Foto\',\'width=720,height=430\');"> 
					              <img src="images/foto.png" border="0"> </td>				  
                  <td width="20"> <a href="#" onClick="window.open(\'tomarfirma/main_eng.swf\',\'Firma\',\'width=900,height=600\');"> 
				                   <img src="images/firma.png" border="0"> </a> </td>
				  <td width="20"> <a href="?opcion=cargarfoto&id='.$row['terid'].'"> <img src="images/iconorefrescar.png" border="0"> </td>
  				  <td width="20"> <a href="facturapida.php?opcion=eligecontrato&cliente='.$row['nit'].'"> 
				                    <img src="images/funciones.png" border="0"> </td>				  
                  <td width="20"> <a href="?opcion=detalles&amp;id='.$row['terid'].'"> <img src="images/seleccion.png" border="0"> </td>		  				  
				  <td width="10"> </td>				                   
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