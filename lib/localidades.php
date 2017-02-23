<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];


  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $ciudadid     = $_POST['ciudadid'];
	$codigo       = strtoupper($_POST['codigo']);
	$nombre       = strtoupper($_POST['nombre']);	
	$departamento = strtoupper($_POST['departamento']);	

	if($opcion == "guardarnuevo")
	{
        $vsql = "INSERT INTO ciudades(codigo,nombre,departamento,creador,momento) values('".$codigo."', 
	            '".$nombre."','".$departamento."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";
    
		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Ciudad Creada Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE ciudades SET codigo = '".$codigo."' , nombre = '".$nombre."' , departamento = '".$departamento."' 
	             WHERE ciudadid=".$ciudadid;
    
	    $clase->EjecutarSQL($vsql);
	
   		$clase->Aviso(1,"Ciudad Modificada Exitosamente");  			  
    }	
	
	header("Location: localidades.php");
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
			     <td width="37"> <img src="images/iconos/localidades.png" width="32" height="32" border="0"> </td>
				 <td width="580"> Nueva Localidad <td>
				 <td>  <a href="localidades.php"> Listado de Localidades </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST">
	        <input type="hidden" name="ciudadid" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Localidad : </td>
			  <td> <input type="text" name="nombre" class="Texto15"> 
			 </tr>
	         <tr> 
    		  <td> <label class="Texto15"> Departamento : </td>
			  <td> <input type="text" name="departamento" class="Texto15"> 
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
			     <td width="37"> <img src="images/iconos/localidades.png" width="32" height="32" border="0"> </td>
				 <td width="580"> Localidades <td>
				 <td>  <a href="localidades.php"> Listado de Localidades </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
  	$vsql = "SELECT * FROM ciudades WHERE ciudadid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
		$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="ciudadid" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" value="'.$row['codigo'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Localidad : </td>
			  <td> <input type="text" name="nombre" class="Texto15" value="'.$row['nombre'].'"> 
			 </tr>
	         <tr> 
    		  <td> <label class="Texto15"> Departamento : </td>
			  <td> <input type="text" name="departamento" class="Texto15" value="'.$row['departamento'].'"> 
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
    $vsql = "DELETE FROM ciudades WHERE ciudadid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Ciudad Eliminada Exitosamente");  		
	header("Location: localidades.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: localidades.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT * FROM ciudades WHERE codigo like '%".$criterio."%' OR nombre like '%".$criterio."%' OR departamento like '%".$criterio."%' ORDER BY nombre ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	$_SESSION['SQL_LOCALIDADES'] = $vsql;
	header("Location: localidades.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM ciudades ORDER BY nombre ASC limit 0,30";
	$_SESSION['SQL_LOCALIDADES'] = "";
	header("Location: localidades.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/localidades.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Localidades  <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_LOCALIDADES'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_LOCALIDADES'];
	if($vsql == "")
    	$vsql = "SELECT * FROM ciudades ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="35"> Codigo </td>
				 <td width="100"> Localidad </td>
				 <td width="100"> Departamento </td>			
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
				  <td> '.$row['nombre'].' </td>
				  <td> '.$row['departamento'].' </td>
				  <td> <a href="?opcion=detalles&amp;id='.$row['ciudadid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
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