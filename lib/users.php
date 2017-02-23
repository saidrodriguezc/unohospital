<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  ////////////////////////////////////////////////////// 
  if($opcion == "accesos")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/usuarios.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Acceso a Tablas Basicas de Usuarios </b><td>
				 <td>  <a href="users.php"> Listado de Usuarios </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	$cont.= FuncionesEspeciales(2);

   	$vsql = "SELECT T.descripcion nomtabla , UT.* FROM usuarios U
			 INNER JOIN usuariosxtablas UT ON ( UT.username = U.username ) 
			 INNER JOIN tablas T ON ( T.tablaid = UT.tablaid )
			 WHERE U.username='".$id."'";
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	
	$cont.='<center>
            <form action="?opcion=guardaraccesos" method="POST">
            <table width="100%">
	          <tr class="BarraDocumentosSel">
			     <td width="30"> </td>
			     <td width="150"> <b>Tabla - Entidad</b> </td>
				 <td width="20"> <img src="images/iconobuscar.png" border="0"> <b>Visualizar</b> </td>
				 <td width="20"> <img src="images/icononuevo.png" border="0"> <b>Agregar</b> </td>
				 <td width="20"> <img src="images/iconoeditar.png" border="0"> <b>Modificar</b> </td>
				 <td width="20"> <img src="images/iconoborrar.png" border="0"> <b>Eliminar</b> </td>				 
  				 <td width="10"> </td>				 		  
	    	  </tr>';
    $i = 0;
    while($row = mysql_fetch_array($result)) 
	{
	     $i++;
		 if($i%2 == 0)
		   $cont.='<tr class="TablaDocsPar">';
		 else
		   $cont.='<tr class="TablaDocsImPar">';		 
		          
		 $cont.=' <td width="30"> </td>
				  <td width="150"> '.strtoupper($row['nomtabla']).' </td>
				  <td width="20" align="center"> <input type="checkbox" name="tabla_'.$row['tablaid'].'" '.$row['visualizar'].'> </td>
				  <td width="20" align="center"> <input type="checkbox" name="tabla_'.$row['tablaid'].'" '.$row['agregar'].'> </td>
				  <td width="20" align="center"> <input type="checkbox" name="tabla_'.$row['tablaid'].'" '.$row['modificar'].'> </td>
				  <td width="20" align="center"> <input type="checkbox" name="tabla_'.$row['tablaid'].'" '.$row['eliminar'].'> </td>				  
   				  <td width="10"> </td>				 		  
				 </tr>';
	}
	$cont.='</table>  <br>
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table><br>';
  }			

  /////////////////////////////////////////////////////
  if($opcion == "permisos")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/usuarios.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Permisos Especiales del Usuario </b><td>
				 <td>  <a href="users.php"> Listado de Usuarios </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
			 
	$cont.= FuncionesEspeciales(1);
	
	$cont.='<br><center>
            <form action="?opcion=guardarnuevo" method="POST">
			<table width="600">
			  <tr>
			    <td> Hola </td>
			  </tr>
            </table>';
  }			

  
  /////////////////////////////////////////////////////////////////////////    
  if(($opcion == "micuenta")||($opcion == "miclave"))
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/usuarios.png" width="32" height="32" border="0"> </td>
				 <td width="470"> Configuracion de Cuenta <td>
				 <td> '.$_SESSION['NOMBREUSUARIO'].' </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
			 
	$cont.= FuncionesEspeciales();	
  }
  
  ////////////////////////////////////////////////////////////////////////  
  if($opcion == "guardarclave")
  {
    $username   = $_POST['username'];
	$clave1     = $_POST['clave1'];
	$clave2     = $_POST['clave2'];	
	
	if($clave1 == $clave2)
    {
		$vsql = "UPDATE usuarios SET clave ='".$clave1."' WHERE username='".$username."'";
		$cant = $clase->EjecutarSQL($vsql);
	
        if($cant == 1)
    		$clase->Aviso(1,"Contrase&ntilde;a Cambiada Exitosamente");  	
 	    else
			$clase->Aviso(2,"Error al Asignar la Contrase&ntilde;a");  		
	}		
	else
			$clase->Aviso(2,"Las contrase&ntilde;as no coinciden");  		
   
    header("Location: users.php");
  }
  

  /////////////////////////////////////////////////////////////////////////    
  if($opcion == "cambiarclave")
  {
    $username = $_GET['id'];
    $cont.='<h3> Cambiar Contrase&ntilde;a de Usuario </h3><center>
            <form action="?opcion=guardarclave" method="POST">
	        <input type="hidden" name="username" value="'.$username.'">
			<table width="260">
	         <tr> 
			  <td width="120"> <label class="Texto15"> Clave : </label> </td>
			  <td> <input type="password" name="clave1" class="Texto15" size="10" maxlength="20"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Confirme : </td>
			  <td> <input type="password" name="clave2" class="Texto15" size="10" maxlength="20"> </td>
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


  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $username    = $_POST['username'];
	$nombre      = strtoupper($_POST['nombre']);
	$email       = strtoupper($_POST['email']);	

	if($opcion == "guardarnuevo")
	{
        $vsql = "INSERT INTO usuarios(username,nombre,email,momento) values('".$username."', 
	            '".$nombre."','".$email."',CURRENT_TIMESTAMP)";
    
		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Usuario creado Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE usuarios SET nombre = '".$nombre."' , email = '".$email."' 
	             WHERE username=".$username;
    
	    $clase->EjecutarSQL($vsql);
	
   		$clase->Aviso(1,"Usuario modificado Exitosamente");  			  
    }	
	
	header("Location: users.php");
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
			     <td width="37"> <img src="images/iconos/usuarios.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Usuario <td>
				 <td>  <a href="users.php"> Listado de Usuarios </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST">
			<table width="600">
	         <tr> 
			  <td width="150"> <label class="Texto15"> Usuario </label> </td>
			  <td> <input type="text" name="username" class="Texto15" size="15" maxlength="15"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Nombre  </td>
			  <td> <input type="text" name="nombre" class="Texto15"  maxlength="25" size="30"> 
			 </tr>
			 <tr> 
			  <td> <label class="Texto15"> E - Mail </td>
			  <td> <input type="text" name="email" class="Texto15"  maxlength="25" size="30"> 
			 </tr>
			 <tr> 
			  <td> <label class="Texto15"> Contrase&ntilde;a </td>
			  <td> <input type="password" name="clave1" class="Texto15"  maxlength="15" size="15"> 
			 </tr>
			 <tr> 
			  <td> <label class="Texto15"> Confirme Clave </td>
			  <td> <input type="password" name="clave2" class="Texto15"  maxlength="15" size="15"> 
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
			     <td width="37"> <img src="images/iconos/usuarios.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Usuarios <td>
				 <td>  <a href="users.php"> Listado de Usuarios </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
  	$vsql = "SELECT * FROM usuarios WHERE username='".$id."'";
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
		$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="username" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Username : </label> </td>
			  <td> <input type="text" name="username" class="Texto15" size="10" value="'.$row['username'].'" disabled> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Nombre : </td>
			  <td> <input type="text" name="nombre" class="Texto15" value="'.$row['nombre'].'" size="30"> 
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
    mysql_free_result($result); 
    mysql_close($conex);			  
  }
  
  /////////////////////////////////////////  
  if($opcion == "eliminar")
  {
    $id = $_GET['id'];
    $vsql = "DELETE FROM usuarios WHERE username=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Usuario Eliminado Exitosamente");  		
	header("Location: users.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: users.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT * FROM usuarios WHERE username like '%".$criterio."%' OR nombre like '%".$criterio."%' ORDER BY nombre ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_USUARIOS'] = $vsql;
	header("Location: users.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM usuarios ORDER BY nombre ASC limit 0,30";
	$_SESSION['SQL_USUARIOS'] = "";
	header("Location: users.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/usuarios.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Usuarios del Sistema <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_USUARIOS'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_USUARIOS'];
	if($vsql == "")
    	$vsql = "SELECT * FROM usuarios ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="35"> Username </td>
				 <td width="100"> Nombre </td>
				 <td width="100"> Email </td>			
				 <td width="25"> </td>
				 <td width="25"> </td>				 
				 <td width="25"> </td>				 
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
				  <td width="35"> '.strtoupper($row['username']).' </td>
				  <td width="100"> '.$row['nombre'].' </td>
				  <td width="100"> '.$row['email'].'</td>
				  <td width="25"> <a href="?opcion=permisos&amp;id='.$row['username'].'" title="Modificar Permisos"> <img src="images/iconoasentar.png" border="0"> </td>				  
				  <td width="25"> <a href="?opcion=cambiarclave&amp;id='.$row['username'].'" rel="facebox"  title="Cambiar Clave"> <img src="images/iconoreversar.png" border="0"> </td>				  
				  <td width="25"> <a href="?opcion=detalles&amp;id='.$row['username'].'" title="Seleccionar"> <img src="images/seleccion.png" border="0"> </td>				  		
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
  
  ///////////////////////////////////////////////////////////////////////
  function FuncionesEspeciales($item)
  {
     $id = $_GET['id'];
	 $clase = new Sistema();
	 $nombre = $clase->BDLockup($id,"usuarios","username","nombre");
	 
	 // Barra de Acciones Especiales
  	 $cont.='<table width="100%">	
	          <tr class="BarraDocumentos">';
     if($item == 1)			  
	    $cont.='<td width="30%" class="BarraDocumentosSel" align="center"> <img src="images/estadisticas.png">Permisos Basicos</td>';
	 else	
        $cont.='<td width="30%" align="center"> <a href="?opcion=permisos&id='.$id.'"><img src="images/estadisticas.png">Permisos Basicos</a> </td>';	 

     if($item == 2)			  
	    $cont.='<td width="30%" class="BarraDocumentosSel" align="center"> <img src="images/estadisticas.png"> Acceso a Tablas Basicas </td>';
	 else	
        $cont.='<td width="30%" align="center"> <a href="?opcion=accesos&id='.$id.'"><img src="images/estadisticas.png"> Acceso a Tablas Basicas </a> </td>';	 

	 $cont.='   <td width="5%"> </td>				 
	            <td width="35%"> <b>Usuario : </b>'.$nombre.'</td>				 
			  </tr>	 			   
			 </table>';	
     return($cont);			 
  }
 
?> 