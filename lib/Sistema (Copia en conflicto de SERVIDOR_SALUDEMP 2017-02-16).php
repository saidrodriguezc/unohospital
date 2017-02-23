<?PHP 
 session_start();
 class Sistema
 {

	// Configuracion de la Base de Datos
    var $UsuarioBD;
    var $ClaveBD;
	var $ServidorBD;
    var $NombreBD;		
    var $URLSitio;			
    
	// ************************************************************************
    // FUNCIONES DE CONEXION A LA BASE DE DATOS 
    // ************************************************
	// Establece una Conexion a la Base de Datos Activa
	// Devuelve : El Enlace a la conexion

    function Conectar()
	{
	  $this->UsuarioBD  = "root";
	  $this->ClaveBD    = "123";
	  $this->ServidorBD = "localhost";
  	  $this->NombreBD   = "unohospital";
	  
	  if (!($conex = mysql_connect($this->ServidorBD,$this->UsuarioBD,$this->ClaveBD))) 
      { 
       echo "Error conectando a la base de datos."; 
       exit(); 
      } 
      
	  if (!mysql_select_db($this->NombreBD,$conex)) 
      { 
       echo "Error seleccionando la base de datos."; 
       exit(); 
      } 
      
	  return $conex; 
	}
	
	// ************************************************************
	// Ejecuta una consulta SQL DE ACCION (INSERT - UPDATE - DELETE)
    // Recibe : La Consulta SQL a Ejecutar	
	function EjecutarSQL($SQL)
	{
	   $conex  = $this->Conectar();
	   $result = mysql_query($SQL,$conex);
	   return($result);
       mysql_close($conex);
	}
	
	// ************************************************************
	// Busca el Campo RETORNO en la tabla TABLA por el campo CAMPO 
	// segun un CRITERIO
    // Recibe : CRITERIO - TABLA - CAMPO A BUSCAR - CAMPO A RETORNAR
	function BDLockup($criterio,$tabla,$campo,$retorno)
	{
	   $campoEnc="";
	   $conex  = $this->Conectar();
	   $consulta = "SELECT " . $retorno . " AS RET FROM " . $tabla . " WHERE " . $campo . " ='" . $criterio . "'";
	   $result = mysql_query($consulta,$conex);
	   if($row = mysql_fetch_array($result)) 
	     $campoEnc = $row['RET'];
	 
       mysql_free_result($result); 
       mysql_close($conex);
	   
       return($campoEnc);	  
	}
	
	// ************************************************************
	// Ejecuta una consulta SQL y Devuelve el UNICO CAMPO econtrado
	// Si la consulta genera mas campos solo devuelve el 1,1
    // Recibe : La Consulta SQL a Ejecutar
	// Devuelve : El Campo encontrado
	
	function SeleccionarUno($SQL)
	{
	   $campoEnc ="";
	   $conex  = $this->Conectar();
	   $result = mysql_query($SQL,$conex);
	   if($row = mysql_fetch_array($result)) 
	     $campoEnc = $row[0];
	 
       mysql_free_result($result); 
       mysql_close($conex);
	   return($campoEnc);	   
	}		
		
	
////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////	
// $vermenu :    S -> Si ver menu    N-> No ver menu
// $tipomenu :   W -> menu Web       D-> Menu Documentos     T-> menu Tablet    N -> Sin Entorno
////////////////////////////////////////////////////////////////
function Header($vermenu,$tipomenu)
{
  if(($_SESSION['ESTADO'] == "OUT")||($_SESSION['ESTADO'] == ""))
  {
     $_SESSION["ESTADO"] = "OUT";
	 session_unset();
	 session_destroy();
	 header("Location: index.php");
	 exit();
  }
  else
  {
	$cont='
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="css/estilo.css" type="text/css">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<title> Salud Empresarial IPS </title>
    <script src="popcalendar.js" type="text/javascript"></script>	
	<link href="facebox/src/facebox.css" media="screen" rel="stylesheet" type="text/css" />
	<script src="facebox/lib/jquery.js" type="text/javascript"></script>
	<script src="facebox/src/facebox.js" type="text/javascript"></script>
	<script type="text/javascript">
	    jQuery(document).ready(function($) {
	    $(\'a[rel*=facebox]\').facebox({
        loadingImage : \'facebox/src/loading.gif\',
	    closeImage   : \'facebox/src/closelabel.png\'
		      })
	    })
	</script>				   
	<script src="lib/jscal/js/jscal2.js"></script>
	<script src="lib/jscal/js/lang/en.js"></script>
	<link rel="stylesheet" type="text/css" href="lib/jscal/css/jscal2.css" />
	<link rel="stylesheet" type="text/css" href="lib/jscal/css/border-radius.css" />
	<link rel="stylesheet" type="text/css" href="lib/jscal/css/steel/steel.css" />
	</head>
	<body leftmargin="0" topmargin="0" rightmargin="0" bottonmargin="0" OnLoad="document.x.default.focus();"> 
<style>
   img.Grafico {
        border: 0px solid #000;
        background: url("images/cargando.gif") no-repeat center center;
	   }
</style>
	<a name="arriba"></a>
	<script type="text/javascript">
		var pagina = \'usuarios.php?opcion=logoutcaducada\';
		var segundos = 1500000;
		function redireccion() {
		document.location.href=pagina;							   
					       }
		setTimeout("redireccion()",segundos);
	</script>

<!-- Encabezado Logo y Datos de Session -->
<table class="Encabezado" align="center">
 <tr valign="top">
   <td width="500"> </td>
   <td class="MenuEncabezado">
      <b>'.$_SESSION['NOMBREUSUARIO'].'</b> ( '.$_SESSION['EMPRESAACTUAL'] .' )<br>
      <a href="users.php?opcion=detalles&id='.$_SESSION['USUARIO'].'"> Menu del Usuario </a> | 
	  <a href="usuarios.php?opcion=salir"> Salir del Sistema </a> | 
   </td>      
 </tr>
</table>

<!-- Contenido Central del Sistema -->
<table class="Contenido" align="center">
<tr>
  <td valign="top">';

$menu='<table border="0" width="996">
 <tr valign="top">
  <td width="200" bgcolor="#F6F6F6" valign="top"> 
    <!-- Menu Lateral -->	
	<table border="0">
	  <tr>
	   <td class="MenuP"> <a href="principal.php"><font color="#FFFFFF">Principal</a> </td>
	  </tr><tr>
	   <td class="SubMenuP"> <a href="pacientes.php"><img src="images/pacientes.png" class="IconoSubMenu"> Pacientes </a> </td>	   
	   </tr><tr>
	   <td class="SubMenuP"> <a href="historiacli.php"><img src="images/historias.png" class="IconoSubMenu"> Historia Clinica </a> </td>	   
      </tr><tr>
	   <td class="SubMenuP"> <a href="documentos.php"><img src="images/documentos.png" class="IconoSubMenu"> Documentos </a> </td>	   
      </tr><tr>
	   <td class="SubMenuP"> <a href="contratos.php"><img src="images/conceptos.png" class="IconoSubMenu"> Contratos </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="servicios.php"><img src="images/servicios.png" class="IconoSubMenu"> Servicios </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="empresas.php"><img src="images/terceros.png" class="IconoSubMenu"> Empresas </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="profesionales.php"><img src="images/profesionales.png" class="IconoSubMenu"> Profesionales </a> </td>	   
      </tr><tr>
	   <td class="SubMenuP"> <a href="examenes.php"><img src="images/examenes.png" class="IconoSubMenu"> Examenes </a> </td>	   
	   </tr><tr>
	   <td class="SubMenuP"> <a href="basicas.php"><img src="images/tablasbase.png" class="IconoSubMenu"> Tablas Basicas </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="informes.php"><img src="images/informes.png" class="IconoSubMenu"> Informes </a> </td>	   
	  </tr><tr>
	   <td class="MenuP"> Configuraciones </td>
  	  </tr><tr>
       <td class="SubMenuP"> <a href="config.php"><img src="images/configurar.png" class="IconoSubMenu"> Configuraciones </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="users.php"><img src="images/usuarios.png" class="IconoSubMenu"> Usuarios y Roles </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="auditoria.php"><img src="images/auditoria.png" class="IconoSubMenu"> Auditor de Sucesos </a> </td>	   	   
	  </tr><tr>
	   <td class="MenuP"> Soporte </td>
	  </tr><tr>
	   <td class="SubMenuP"> <a href="ayuda.php"><img src="images/ayuda.png" class="IconoSubMenu"> Ayuda en Linea </a> </td>
	  </tr>
	</table>';
  
    // Segun los parametros de la funcion muestra o no el menu con el tipo de Menu
    if(($vermenu == "S")&&($tipomenu=="W"))
      $cont.= $menu;

   if(($vermenu == "S")&&($tipomenu=="T"))
     $cont.= $menutablet;

   $cont.='</td><td valign="top">';

   /// Muestro los avisos del Sistema	
   $avisos = $_SESSION["SYSAVISO"];

   if($avisos != ""){
     $cont.= '<center>'.$avisos.'</center>';
     $_SESSION["SYSAVISO"]="";
   }  
  return($cont);
 } // IF Session es Valida
} // Fin de la Funcion

////////////////////////////////////////////////////////////////////
function HeaderReportes()
{
$vermenu  = "S";
$tipomenu = "S";
$cont='
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../css/estilo.css" type="text/css">
<title>Salud Empresarial IPS</title>
<script src="popcalendar.js" type="text/javascript"></script>
<link href="../facebox/src/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<script src="../facebox/lib/jquery.js" type="text/javascript"></script>
<script src="../facebox/src/facebox.js" type="text/javascript"></script>
<script type="text/javascript">
	    jQuery(document).ready(function($) {
	    $(\'a[rel*=facebox]\').facebox({
        loadingImage : \'facebox/src/loading.gif\',
	    closeImage   : \'facebox/src/closelabel.png\'
		      })
	    })
</script>				   
<script src="lib/jscal/js/jscal2.js"></script>
<script src="lib/jscal/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="../lib/jscal/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="../lib/jscal/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="../lib/jscal/css/steel/steel.css" />
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottonmargin="0" OnLoad="document.x.default.focus();"> 
<a name="arriba"></a>
<script type="text/javascript">
    var pagina = \'usuarios.php?opcion=logoutcaducada\';
    var segundos = 1500000;
    function redireccion() {
     document.location.href=pagina;							   
					       }
    setTimeout("redireccion()",segundos);
</script>
<style>
   img.Grafico {
        border: 1px solid #000;
        background: url("cargando.gif") no-repeat center center;
	   }
</style>

<!-- Encabezado Logo y Datos de Session -->
<table class="Encabezado" align="center">
 <tr valign="top">
   <td width="500"> </td>
   <td class="MenuEncabezado">
      <b>'.$_SESSION['NOMBREUSUARIO'].'</b> ( '.$_SESSION['EMPRESAACTUAL'] .' )<br>
	  <a href="usuarios.php?opcion=micuenta"> Menu del Usuario </a> | 
	  <a href="usuarios.php?opcion=salir"> Salir del Sistema </a>  
   </td>      
 </tr>
</table>

<!-- Contenido Central del Sistema -->
<table class="Contenido" align="center">
<tr>
  <td valign="top">';

$menu='<table border="0" width="996">
 <tr valign="top">
  <td width="200" bgcolor="#F6F6F6" valign="top"> 
    <!-- Menu Lateral -->	
	<table border="0">
	  <tr>
	   <td class="MenuP"> Principal </td>
	  </tr><tr>
	   <td class="SubMenuP"> <a href="documentos.php"><img src="images/documentos.png" class="IconoSubMenu"> Documentos </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="productos.php"><img src="images/productos.png" class="IconoSubMenu"> Productos </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="terceros.php"><img src="images/terceros.png" class="IconoSubMenu"> Terceros </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="conceptos.php"><img src="images/conceptos.png" class="IconoSubMenu"> Conceptos </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="basicas.php"><img src="images/tablasbase.png" class="IconoSubMenu"> Tablas Basicas </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="informes.php"><img src="images/informes.png" class="IconoSubMenu"> Informes </a> </td>	   
	  </tr><tr>
	   <td class="MenuP"> Configuraciones </td>
	  </tr><tr>
	   <td class="SubMenuP"> <a href="config.php"><img src="images/configurar.png" class="IconoSubMenu"> Configuraciones </a> </td>	   
	  </tr><tr>
	   <td class="SubMenuP"> <a href="users.php"><img src="images/usuarios.png" class="IconoSubMenu"> Usuarios y Roles </a> </td>	   
	  </tr><tr>
	   <td class="MenuP"> Soporte </td>
	  </tr><tr>
	   <td class="SubMenuP"> <a href="ayuda.php"><img src="images/ayuda.png" class="IconoSubMenu"> Ayuda en Linea </a> </td>
	  </tr>
	</table>';
  
  // Segun los parametros de la funcion muestra o no el menu con el tipo de Menu
  if(($vermenu == "S")&&($tipomenu=="W"))
    $cont.= $menu;

  if(($vermenu == "S")&&($tipomenu=="T"))
    $cont.= $menutablet;

  $cont.='</td><td valign="top">';


  /// Muestro los avisos del Sistema	
  $avisos = $_SESSION["SYSAVISO"];

  if($avisos != ""){
    $cont.= '<center>'.$avisos.'</center>';
    $_SESSION["SYSAVISO"]="";
  } 
 return($cont);
}

////////////////////////////////////////////////////////////////////
function SoloCSS()
{
  return('<link rel="stylesheet" href="css/estilo.css" type="text/css">');
}  

////////////////////////////////////////////////////////////////////
function HeaderImpresion()
{
  $cont='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
         <html>
           <head>
             <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
             <link rel="stylesheet" href="css/imprimir.css" type="text/css">
             <link rel="stylesheet" href="css/imprimir.css" type="text/css" media="print">			 
             <title>1Uno.co</title>
             <script src="popcalendar.js" type="text/javascript"></script>			 
		   </head> 
           <body leftmargin="0" topmargin="0" rightmargin="0" bottonmargin="0">
		   <div id="contenedor">';
  return($cont);
}

		
////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////	
	function PiePagina()
	{
	 $cont='</td>  
 </tr>
</table>
</td>
</tr>
</table>
<!-- Pie de pagina -->
<table class="Pie" align="center">
 <tr>
   <td align="center"> 
   
     <b>1Uno.co</b>
     Derechos Reservados	 
   </td>
 </tr>
</table>
</body>
</html>';
return($cont);
	}


	function HeaderSysAdmin()
	{
	  $cont='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			 <html>
			 <head>
			 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			 <link rel="stylesheet" href="../css/estilosysadmin.css" type="text/css">
			 <title>1Uno.co</title>
			 </head>
			 <body onload = "document.forms[0][\'default\'].focus()">
			 <!-- Encabezado Logo y Datos de Session -->
			 <table class="Encabezado" align="center">
			  <tr valign="top">
			    <td width="500"> </td>
			     <td class="MenuEncabezado">';
				
		if($_SESSION["ESTADO"] == "IN")
		{
		   $cont.='<b>Bienvenido, '.$_SESSION['NOMBRE'].' </b> <br>
               	   <a href="usuarios.php?opcion=verdatos" class="Enlace"> Mi Cuenta </a> | 
               	   <a href="instancias.php" class="Enlace"> Instancias </a> | 				   
				   <a href="usuarios.php?opcion=salir" class="Enlace"> Salir del Sistema </a> ';
		}			
				 
 	   $cont.='</td>      
			    </tr>
			  </table>

			 <!-- Contenido Central del Sistema -->
			 <table border="1" class="ContenidoIndex" align="center"> 
			 <tr valign="middle">
			   <td align="center"> 
    	   		  
				  <table class="TablaIndex">
				   <tr valign="Top">
				     <td align="center"> ';
	  return($cont);
	}
	
	
	function PiePaginaSysAdmin()
	{
	 $cont='</td>
	 </tr>
	</table>   
  
  </td>  
 </tr>
</table>

<!-- Pie de pagina -->
<table class="Pie" align="center">
 <tr>
   <td align="center"> 
   
     <b>1Uno.co</b>
     Derechos Reservados	 
   </td>
 </tr>
</table>

</body>
</html>';
return($cont);
	}


   /////////////////////////////////////////////////////////////////////////
   function NombreMes($mes)
   {
      if(($mes == 1)||($mes == "01")) 
	   return("ENERO");
      if(($mes == 2)||($mes == "02")) 
	   return("FEBRERO");
      if(($mes == 3)||($mes == "03")) 
	   return("MARZO");
      if(($mes == 4)||($mes == "04")) 
	   return("ABRIL");
      if(($mes == 5)||($mes == "05")) 
	   return("MAYO");
      if(($mes == 6)||($mes == "06")) 
	   return("JUNIO");
      if(($mes == 7)||($mes == "07")) 
	   return("JULIO");
      if(($mes == 8)||($mes == "08")) 
	   return("AGOSTO");
      if(($mes == 9)||($mes == "09")) 
	   return("SEPTIEMBRE");
      if(($mes == 10)||($mes == "10")) 
	   return("OCTUBRE");
      if(($mes == 11)||($mes == "11")) 
	   return("NOVIEMBRE");
      if(($mes == 12)||($mes == "12")) 
	   return("DICIEMBRE");
	}
	
	/////////////////////////////////////////////////////////////////////
	function FormatoFecha($fecha)
	{
	   $ano  = substr($fecha,0,4);
	   $mes  = substr($fecha,5,2);
	   $dia  = substr($fecha,8,2);
	   $hora = substr($fecha,11,2);
	   $min  = substr($fecha,14,2);
	   
	   $nombreMes = substr($this->NombreMes($mes),0,3);
	   
	   if($hora <= 12){
	     $jornada = "AM";
	   }else{
	     $hora = $hora-12;
		 $jornada = "PM";
	   }   
	   
	   return($nombreMes." ".$dia." de ".$ano." a las ".$hora.":".$min." ".$jornada);
	}
	
	///////////////////////////////////////////////////////////////////////////
	function Aviso($tipo,$contenido)
	{
	  if($tipo == 1)
	    $_SESSION["SYSAVISO"] ='<table> <tr height="36"> <td width="800" align="center" style="vertical-align:middle; background-color:#BBFFB6; font-size:12px; border-color:#1FDF00; border-width:medium;"> '.$contenido.'</td> </tr> </table>';			   
	  if($tipo == 2)
	    $_SESSION["SYSAVISO"] ='<table> <tr height="36"> <td width="800" align="center" style="vertical-align:middle; background-color:#FFFFCC; font-size:12px; border-color:#FFCD67; border-width:medium;"> '.$contenido.'</td> </tr> </table>';			   
	  if($tipo == 3)
	    $_SESSION["SYSAVISO"] ='<table> <tr height="36"> <td width="800" align="center" style="vertical-align:middle; background-color:#FFDDDD; font-size:12px; border-color:#FFBBBB; border-width:medium;"> '.$contenido.'</td> </tr> </table>';			   
	}   

	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////
	
	function CrearCombo($nombrecombo,$tabla,$caption,$valor,$valordefecto,$incluirblanco)
	{  
	   $vsql="SELECT ".$caption." caption ,".$valor." valor FROM ".$tabla." ORDER BY ".$caption."";
	   $conex = $this->Conectar();
       $result = mysql_query($vsql,$conex); 
       $cont = '<SELECT name="'.$nombrecombo.'" class="Texto15">';
      
	   if($incluirblanco == "S")
	     $cont.='<option value=""> </option>';
	     
	   while($row = mysql_fetch_array($result)){
		 if($row['caption'] != "")
		 {
	       if($valordefecto == $row['valor'])
 	          $cont.='<option value="'.$row['valor'].'" selected> '.$row['caption'].'</option>';
	       else
              $cont.='<option value="'.$row['valor'].'"> '.$row['caption'].'</option>';		 		
		 }	 
	   }
   	   $cont.='</SELECT>';	   
	   return($cont);
	}
	
	///////////////////////////////////////////////////////////////////////////
	/// Select campo1 as Valor , Campo 2 as Caption FROM tabla WHERE condicion
	function CrearComboFiltro($nombrecombo,$sql,$pordefecto,$incluirblanco)
	{  
	   $vsql=$sql;
	   $conex = $this->Conectar();
       $result = mysql_query($vsql,$conex); 
       $cont = '<SELECT name="'.$nombrecombo.'" class="Texto15">';
      
	   if($incluirblanco == "S")
	     $cont.='<option value=""> </option>';
	     
	   while($row = mysql_fetch_array($result)){
		 if($row['caption'] != "")
		 {
	       if($valordefecto == $row['valor'])
 	          $cont.='<option value="'.$row['valor'].'" selected> '.$row['caption'].'</option>';
	       else
              $cont.='<option value="'.$row['valor'].'"> '.$row['caption'].'</option>';		 		
		 }	 
	   }
   	   $cont.='</SELECT>';	   
	   return($cont);
	}


  ///////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////
  ////   FUNCIONES DEL LOG DE AUDITORIA    /////
  ///////////////////////////////////////////////////////////////////////////////////////////
  
    function CrearLOG($evento,$descripcion,$usuario,$sentencia,$docuid=0)
	{  
	   if($sentencia != "")
	   {
	      $sentencialista  = str_replace("'"," ",$sentencia);
		  $sentencialista2 = str_replace("\""," ",$sentencialista);
	   }
	   
	   $vsql="INSERT INTO logauditoria(evento,equipo,direccionip,sentencia,descripcion,usuario,docuid,momento) 
	          values('".$evento."','".gethostbyaddr($_SERVER['REMOTE_ADDR'])."','".$this->getRealIP()."','".$sentencialista2.
			  "','".strtoupper($descripcion)."','".$usuario."',".$docuid.",CURRENT_TIMESTAMP)";	
	   $this->EjecutarSQL($vsql);	  
	}
    
	
	/////////////////////////////////////////////////
	function getRealIP() 
	{
      if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
       
      if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
   
      return $_SERVER['REMOTE_ADDR'];
	}
	
 } // Fin de la Clase
  
?>