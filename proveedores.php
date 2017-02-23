<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $proveedorid   = $_POST['proveedorid'];
	$nit           = strtoupper($_POST['nit']);	
	$nombre        = strtoupper($_POST['nombre']);	
    $direccion     = strtoupper($_POST['direccion']);		
	$telefono      = strtoupper($_POST['telefono']);
	$email         = strtolower($_POST['email']);
	$regimen       = strtoupper($_POST['regimen']);		
		
	// Valido que el grupo y la linea esten en la base de datos
	if($opcion == "guardarnuevo")
	{
		$vsql = "INSERT INTO proveedores(nit,nombre,direccion,telefono,email,regimen,creador,momento)
		         values('".$nit."','".$nombre."','".$direccion."','".$telefono."','".$email."','".
				 $regimen."','".$_SESSION['USUARIO']."',CURRENT_TIMESTAMP)";    

		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Proveedor creado Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE proveedores SET nit = '".$nit."' , nombre = '".$nombre."' ,
		         direccion = '".$direccion."' , telefono = '".$telefono."' ,
		         regimen = '".$regimen."' , email = '".$email."' ,				 
		         momento = CURRENT_TIMESTAMP				 				 
	             WHERE proveedorid=".$proveedorid;

        $clase->EjecutarSQL($vsql);
		
  		$clase->Aviso(1,"Proveedor modificado Exitosamente");  			  
    }	
	
	header("Location: proveedores.php");
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
				 <td width="520"> Nuevo Proveedor <td>
				 <td>  <a href="proveedores.php"> Listado de Proveedores </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST" name="x">
			<table width="700">
	         <tr> 
			  <td> <label class="Texto15"> CC o NIT : </label> </td>
			  <td> <input type="text" name="nit" class="Texto15" size="15" maxlength="15" tabindex="2"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Nombre : </td>
			  <td> <input type="text" name="nombre" class="Texto15" maxlength="60" size="45" tabindex="3"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Direccion : </td>
			  <td> <input type="text" name="direccion" class="Texto15" maxlength="60" size="45" tabindex="4"> 
			 </tr>			
	         <tr> 
			  <td> <label class="Texto15"> Telefono : </label> </td>
			  <td> <input type="text" name="telefono" class="Texto15" size="25" maxlength="25" tabindex="7"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> E-mail: </label> </td>
			  <td> <input type="text" name="email" class="Texto15" size="25" maxlength="50" tabindex="9"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Regimen : </label> </td>
			  <td> <input type="radio" name="regimen" value="SIMPLIFICADO" checked> Simplificado 
			       <input type="radio" name="regimen" value="COMUN"> Comun
				   <input type="radio" name="regimen" value="GRANDES"> Grandes Contribuyentes
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
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Proveedores <td>
				 <td>  <a href="proveedores.php"> Listado de Proveedores </a> </td>
    		     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
  	$vsql = "SELECT * FROM proveedores WHERE proveedorid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST" name="x">
			<input type="hidden" name="proveedorid" value="'.$id.'">
			<table width="700">
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
	         <tr> 
			  <td> <label class="Texto15"> Telefono : </label> </td>
			  <td> <input type="text" name="telefono" class="Texto15" size="25" maxlength="25" tabindex="7" value="'.$row['telefono'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> E-mail: </label> </td>
			  <td> <input type="text" name="email" class="Texto15" size="25" maxlength="50" tabindex="9" value="'.$row['email'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Regimen : </label> </td>
			  <td>';
			  
			  if($row['regimen'] == 'SIMPLIFICADO')
  			     $cont.='<input type="radio" name="regimen" value="SIMPLIFICADO" checked> Simplificado';
			  else
			     $cont.='<input type="radio" name="regimen" value="SIMPLIFICADO"> Simplificado';  
			  
			  if($row['regimen'] == 'COMUN')
  			     $cont.='<input type="radio" name="regimen" value="COMUN" checked> Com&uacute;n';
			  else
			     $cont.='<input type="radio" name="regimen" value="COMUN"> Com&uacute;n';    				

			  if($row['regimen'] == 'GRANDES')
  			     $cont.='<input type="radio" name="regimen" value="GRANDES" checked> Grandes Contribuyentes';
			  else
			     $cont.='<input type="radio" name="regimen" value="GRANDES"> Grandes Contribuyentes';    				

			 $cont.='</tr>
			</table>
			
			<br><br>
			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>';
    }
  }
  
  /////////////////////////////////////////  
  if($opcion == "eliminar")
  {
    $id = $_GET['id'];
    $vsql = "DELETE FROM proveedores WHERE proveedorid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Proveedor Eliminado Exitosamente");  		
	header("Location: proveedores.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: proveedores.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT * FROM proveedores WHERE nit like '%".$criterio."%' OR nombre like '%".$criterio."%' ORDER BY nombre ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_PROVEEDORES'] = $vsql;
	header("Location: proveedores.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM proveedores ORDER BY nombre ASC limit 0,30";
	$_SESSION['SQL_PROVEEDORES'] = "";
	header("Location: proveedores.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/terceros.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Proveedores <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_PROVEEDORES'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_PROVEEDORES'];
	if($vsql == "")
    	$vsql = "SELECT * FROM proveedores ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="25">  NIT  </td>				 
				 <td width="200"> Nombres y Apellidos </td>
				 <td width="35"> Telefono </td>			
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
				  <td> '.$row['nit'].' </td>
				  <td> '.$row['nombre'].' </td>
				  <td> '.$row['telefono'].'</td>
                  <td> <a href="?opcion=detalles&amp;id='.$row['proveedorid'].'"> <img src="images/seleccion.png" border="0"> </td>				  				  
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