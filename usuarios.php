<?PHP
  session_start();  
  include("lib/Sistema.php");
  $clase = new Sistema();  
  $opcion = $_GET["opcion"];


  ///////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////  
  if($opcion == "cambiarmiclave")
  {		  
     $username = $_SESSION['USERNAME'];
	 $nombre   = $clase->BDLockup($username,'usuarios','username','nombre'); 	 
	 $clave  = $clase->BDLockup($username,'usuarios','username','clave'); 	 

	 $cont = $clase->Header("S","W");
     $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/usuarios.png" width="32" height="32" border="0"> </td>
				 <td width="300"> <b> Cambiar mi contrase&ntilde;a</b> <td>
				 <td width="300"> Username : <b> '.$username.' </b><td>
				 <td width="400"> Nombre : <b> '.$nombre.' </b><td>				 
 			   </tr></table>
			   <form action="usuarios.php?opcion=cambiarmiclave2" method="POST" name="x" onSubmit="return validarenvio()"> 
			   <script language="JavaScript">
			   <!--
               function ver_password() 
			   {
                  var passwd_valor1 = document.x.claveactual.value;
				  var passwd_valor2 = document.x.clave1.value;
				  var passwd_valor3 = document.x.clave2.value;				  
				  
    			  document.getElementById(\'claveactualx\').innerHTML = (document.x.input_ver.checked)
		          ? \'<input type="text"     name="claveactual" value="" size="30" maxlength="20" class="Texto14">\'
			      : \'<input type="password" name="claveactual" value="" size="30" maxlength="20" class="Texto14">\';
				  document.getElementById(\'clave1x\').innerHTML = (document.x.input_ver.checked)
		          ? \'<input type="text"     name="clave1" value="" size="30" maxlength="20" class="Texto14">\'
			      : \'<input type="password" name="clave1" value="" size="30" maxlength="20" class="Texto14">\';
                  document.getElementById(\'clave2x\').innerHTML = (document.x.input_ver.checked)
		          ? \'<input type="text"     name="clave2" value="" size="30" maxlength="20" class="Texto14">\'
			      : \'<input type="password" name="clave2" value="" size="30" maxlength="20" class="Texto14">\';
				  
				  document.x.claveactual.value = passwd_valor1;
				  document.x.clave1.value      = passwd_valor2;
				  document.x.clave2.value      = passwd_valor3;
			   }
			   
			   function validar()
			   {
			      var actual = "'.strtoupper($clave).'";
				  var ca = document.getElementById("claveactual").value;                      ca = ca.toUpperCase();
				  var c1 = document.getElementById("clave1").value;                           c1 = c1.toUpperCase(); 
				  var c2 = document.getElementById("clave2").value;				  			  c2 = c2.toUpperCase();	  
				  
				  if(actual == ca)
				     estadoact.src = "images/registrado.png"; 
				  else
				     estadoact.src = "images/nofiltro.png";  	
                  
				  if((c1 != "")&&(c2 != "")) 
				  {
					if((c1.length > 5)&&(c2.length > 5))
					{
					  if(c1 == c2)
				         estadonue.src = "images/registrado.png"; 
				      else
				         estadonue.src = "images/nofiltro.png";  	
					}	
		 	 	    else
					{
     			       alert("La nueva clave debe tener 6 caracteres o mas"); 
					   c1.focus();
					}   
                  }
			   }
			   
			   /////////////////////////////////////////////////////////////////////////////////////
			   function validarenvio()
			   {
			      var actual = "'.strtoupper($clave).'";                                      actual = actual.toUpperCase(); 
				  var ca = document.getElementById("claveactual").value;                      ca = ca.toUpperCase();
				  var c1 = document.getElementById("clave1").value;                           c1 = c1.toUpperCase(); 
				  var c2 = document.getElementById("clave2").value;				  			  c2 = c2.toUpperCase();	  

			      if((ca.length > 0)&&(c1.length > 0)&&(c2.length > 0))
				  {
				     if(actual == ca)
				     {
				        if(c1 == c2)
					    {
					       return(true);
					    }
 				        else
				        {
				           alert("La claves nuevas No coinciden!");
					       return(false);
			            }		
			         }
				     else
				     {
				        alert("La clave Actual es incorrecta!");
					    return(false);
			         }		
			     }
				 else
                 {
				   alert("Debe completar todos los campos");
				   return(false);
			     }						  
			   } 
			   -->
			   </script>
			   
			   <center><br><br><br>
			   <fieldset style="width: 600px; height: 320px;" class="grupos">
			   <legend class="titgruposp">&nbsp;&nbsp;<b> Ingrese sus Contrase&ntilde;as </b>&nbsp;&nbsp;</legend>
			   <br>
			   <table width="350">
			     <tr height="60"> 
				   <td> <b> <label class="Texto12"> Clave Actual <br> <span id="claveactualx"> <input type="password" id="claveactual" name="claveactual" size="30" maxlength="20" class="Texto14" onBlur="validar();"></span> 
				        <img src="images/informacion.png" id="estadoact" border="0">	</td>  </tr>
			     <tr height="60"> 				   
				   <td> <b> <label class="Texto12"> Nueva Clave <br> <span id="clave1x"> <input type="password" id="clave1" name="clave1" size="30" maxlength="20" class="Texto14" onBlur="validar();"> </span>
                      </td>  </tr>
			     <tr height="60"> 				   
				   <td> <b> <label class="Texto12"> Confirme nueva Clave <br> <span id="clave2x"> <input type="password" id="clave2" name="clave2" size="30" maxlength="20" class="Texto14" onBlur="validar();"> </span> 
				        <img src="images/informacion.png" id="estadonue" border="0">   </td>  </tr>
			     <tr> 				   
				   <td> <label class="Texto12"> <input type="checkbox" name="input_ver" value="ver" onclick="ver_password();" class="Texto14"> <label class="Texto14"> Mostrar caracteres </td>  </tr>
			     <tr height="80" valign="middle"> 				   
				   <td align="center"> <input type="submit" value="Cambiar Mi Contrase&ntilde;a" class="button red"> </td>  </tr>
			   </table> 	   				   				   			   
			   </fieldset> 
			   </form>';			   
  }

  //////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////
  if($opcion == "cambiarmiclave2")
  {
	$usuario = strtoupper($_SESSION['USERNAME']);
    $cactual = strtoupper($_POST['claveactual']);
    $nclave1 = strtoupper($_POST['clave1']);
    $nclave2 = strtoupper($_POST['clave2']);

	$claveactual = $clase->BDLockup($_SESSION['USERNAME'],'usuarios','username','clave');
    if($cactual == strtoupper($claveactual))
	{
      if($nclave1 == $nclave2)
      {    
        $vsql="UPDATE usuarios SET clave='".$nclave1."' WHERE username='".$usuario."'";
        $clase->EjecutarSQL($vsql);
        $clase->Aviso(1,'Contrase&ntilde;a Modificada con Exito'); 		   
	    
        // Registro en el LOG de Auditoria el Borrado
        $clase->CrearLOG('011','Se cambio la Contraseña al Usuario '.$username.' Exitosamente',strtoupper($_SESSION["USERNAME"]),'');
	  }
	  else
        $clase->Aviso(3,'Las contrase&ntilde;as no Coinciden'); 
    }
    else
       $clase->Aviso(2,'La contrase&ntilde;a Actual es incorrecta'); 

	header("Location: principal.php");   
  }

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