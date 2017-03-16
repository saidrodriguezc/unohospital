<?PHP
  session_start();  
  include("lib/Sistema.php");
  $clase = new Sistema();  
  $opcion = $_GET["opcion"];

  //////////////////////////////////////////////////////////
  // Cerrar Sesion
  if($opcion == "salir")
  {
   	$clase->CrearLOG('003','El usuario abandona el Sistema de Forma segura',strtoupper($_SESSION["USERNAME"]),'');
	$_SESSION["ESTADO"] = "OUT";
	session_unset();
	session_destroy();
	header("Location: index.php");
  }	
  
  /////////////////////////////////////////  
  if($opcion == "logoutcaducada")
  {
    $clase->CrearLOG('003','El usuario abandona el Sistema por Inactividad',strtoupper($_SESSION["USERNAME"]),'');
	session_unset();
	session_destroy();
	header("Location: index.php");  
  }
  
  //////////////////////////////////////////////////////////
  // Iniciar Sesion
  if($opcion == "login")
  {
     $usuario   = strtoupper($_POST['usuario']);
	 $clave     = $_POST['clave'];
	 
     $vsql = "SELECT COUNT(*) FROM usuarios WHERE username='".$usuario."' AND clave='".$clave."'";
	 $existe = $clase->SeleccionarUno($vsql);
		
	 if($existe == 1)
	 {
	       session_start();
		   $_SESSION["ESTADO"] = "IN";
		   $_SESSION["DBNOMBRE"] = "unohospital";
    	   $_SESSION["EMPRESAACTUAL"] = "Salud Empresarial IPS";
    	   $_SESSION["USUARIO"] = $usuario;		   
    	   $_SESSION["USERNAME"] = $usuario;	    	   
	       $_SESSION["NOMBREUSUARIO"] = $clase->BDLockup($usuario,"usuarios","username","nombre");
	       $_SESSION["EMAILUSUARIO"]  = $clase->BDLockup($usuario,"usuarios","username","email");		
	       $_SESSION["ROL"]  = $clase->BDLockup($usuario,"usuarios","username","rol");
	       $_SESSION["TERIDPROF"]  = $clase->BDLockup($usuario,"usuarios","username","teridprof");
		   $_SESSION["NUMREGISTROSXCONSULTA"] = "50";			   

		   // Configuraciones
	       $_SESSION["G_NOMBREEMP"] = $clase->BDLockup("G_NOMBREEMP","configuraciones","variab","contenido");				   
	       $_SESSION["G_NITEMP"] = $clase->BDLockup("G_NITEMP","configuraciones","variab","contenido");				   
	       $_SESSION["G_MONEDALOCAL"] = $clase->BDLockup("G_MONEDALOCAL","configuraciones","variab","contenido");				   
	       $_SESSION["G_MONEDAEXT"] = $clase->BDLockup("G_MONEDAEXT","configuraciones","variab","contenido");				   
	       $_SESSION["U_CLIENTEPRED"] = $clase->BDLockup("U_CLIENTEPRED","configuraciones","variab","contenido");				   		   		   
	       $_SESSION["U_VENDEDORPRED"] = $clase->BDLockup("U_VENDEDORPRED","configuraciones","variab","contenido");				   		   		   		   
	       $_SESSION["U_BODEGAPRED"] = $clase->BDLockup("U_BODEGAPRED","configuraciones","variab","contenido");				   

		   $_SESSION["G_diaopeFVE"] = $clase->BDLockup("G_diaopeFVE","configuraciones","variab","contenido");
		   $_SESSION["G_diaopeRSA"] = $clase->BDLockup("G_diaopeRSA","configuraciones","variab","contenido");		   
   		   $_SESSION["G_diaopeFCO"] = $clase->BDLockup("G_diaopeFCO","configuraciones","variab","contenido");
		   $_SESSION["G_diaopeCCO"] = $clase->BDLockup("G_diaopeCCO","configuraciones","variab","contenido");   		   
		   
		   // SQLs recordados		   		   
           $_SESSION["SYSAVISO"] = "";
	       $_SESSION["SQL_LOCALIDADES"] = "";
	       $_SESSION["SQL_GRUPOSPER"] = "";	   
		   $_SESSION["SQL_BODEGAS"] = "";
		   $_SESSION["SQL_LINEAS"] = "";
		   $_SESSION["SQL_GRUPOSPROD"] = "";		   
		   $_SESSION["SQL_OBSERVACIONES"] = "";		   		   
		   $_SESSION["SQL_MEDIOSPAGO"] = "";
		   $_SESSION["SQL_ZONAS"] = "";		
		   $_SESSION["SQL_SUCURSALES"] = "";
		   $_SESSION["SQL_USUARIOS"] = "";	
		   $_SESSION["SQL_PROMOCIONES"] = "";	  

		   $_SESSION["SQL_PACIENTES"] = "";	  
		   $_SESSION["SQL_PROFESIONALES"] = "";	  		   
		   $_SESSION["SQL_EXAMENES"] = "";	  		   		   
		   $_SESSION["SQL_EXAMENES"] = "";	  		   		   		   
		   $_SESSION["SQL_NIVELESEDU"] = "";	  		   		   		   
		   $_SESSION["SQL_TIPOEXAMEN"] = "";	  		   		   		   
		   $_SESSION["SQL_EXAMENES"] = "";	  		   		  
		   $_SESSION["SQL_CONCEPTOMED"] = "";	  		   		   		    		   
		   $_SESSION["SQL_ENTIDADES"] = "";	  		   		   		   
		   		   		   		   
		   $_SESSION["FILTRO_DOCUMENTOS"] = "MA";
		   $_SESSION["ORDEN_DOCUMENTOS"] = "";		   	
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "";		   			   
		   $_SESSION["SQL_DOCUMENTOS"] = "";	
		   $_SESSION["SQL_AYUDADOCUMENTOS"] = "";			   	   		   		   		   		   		   
		   $_SESSION["SQL_PRODUCTOS"] = "";		   		   		   		   
		   $_SESSION["SQL_CONCEPTOS"] = "";		   		   		   		   		   
		   $_SESSION["SQL_TERCEROS"] = "";		   		   		   		   		   		   

		   $_SESSION["DOCUID"] = "0";				   
		   $_SESSION["NUMREGISTROSXCONSULTA"] = "30";		

		   
           // Registro en el LOG de Auditoria el Acceso
		   $clase->CrearLOG('001','El usuario ingresa al Sistema Correctamente',strtoupper($_SESSION["USERNAME"]),'');

		   // Registro en una Cookie el Usuario y la Empresa
		   setcookie("ult_username_1uno", $username, time()+(60*60*24*365));

  		   header("Location:principal.php");
	  }
   	  else
   	  {   
		   echo' <script language="Javascript">
		          alert("Datos incorrectos. Por favor intente de nuevo");
                  document.location.href=\'index.php\';
		         </script>';					
	 }	
  }
  
  //////////////////////////////////////////////////////////
  // Impresion del Contenido	  
  $cont.= $clase->PiePaginaSysadmin();
  echo $cont;
?>	 	  