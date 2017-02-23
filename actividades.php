<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];
  /// Prueba


  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $actividadid  = $_POST['actividadid'];
	$codigo       = strtoupper($_POST['codigo']);
	$descripcion  = strtoupper($_POST['descripcion']);	
	$realizarcada = $_POST['realizarcada'];		
	$prioridad    = strtoupper($_POST['prioridad']);		

	if($opcion == "guardarnuevo")
	{
        $vsql = "INSERT INTO actividades(codigo,descripcion,realizarcada,prioritaria) values('".$codigo."','".$descripcion."',".$realizarcada.",'".$prioridad."')";    
		$cant = $clase->EjecutarSQL($vsql);

		if($cant == 1)
    		$clase->Aviso(1,"Actividad creada Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE actividades SET codigo = '".$codigo."' , descripcion = '".$descripcion."' ,
 		         realizarcada = ".$realizarcada." , prioritaria = '".$prioridad."'
	             WHERE actividadid=".$actividadid;
    
	    $clase->EjecutarSQL($vsql);
	
   		$clase->Aviso(1,"Marca modificada Exitosamente");  			  
    }	
	
	header("Location: actividades.php");
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
			     <td width="37"> <img src="images/iconos/actividades.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nueva Actividad <td>
				 <td>  <a href="actividades.php"> Listado de Actividades</a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST">
	        <input type="hidden" name="actividadid" value="'.$id.'">
			<table width="450">
	         <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" maxlength="5"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="25" size="30"> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Realizar cada : </td>
			  <td> <input type="text" name="realizarcada" class="Texto15"  maxlength="3" size="3" value="0"> <b>Dias</b> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Prioridad : </td>
			  <td> <input type="radio" name="prioridad" value="1" checked> Prioritaria 
			       <input type="radio" name="prioridad" value="0"> Mantenimiento 
		      </td>
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
			     <td width="37"> <img src="images/iconos/actividades.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Actividades <td>
				 <td>  <a href="actividades.php"> Listado de Actividades </a> </td>
    		     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
  	$vsql = "SELECT * FROM actividades WHERE actividadid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
		$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="actividadid" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" value="'.$row['codigo'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15" value="'.$row['descripcion'].'" size="30"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Realizar cada : </td>
			  <td> <input type="text" name="realizarcada" class="Texto15"  maxlength="3" size="3" value="'.$row['realizarcada'].'"> <b>Dias</b> </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Prioridad : </td>
			  <td>
			       <input type="radio" name="prioridad" value="1"';
			       if($row['prioritaria'] == "1") 
			         $cont.=' checked';
			 $cont.='> Prioritaria 
			       
				   <input type="radio" name="prioridad" value="0"';
				   if($row['prioritaria'] == "0") 
			         $cont.=' checked';
			 $cont.='> Mantenimiento 
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
    mysql_free_result($result); 
    mysql_close($conex);			  
  }
  
  /////////////////////////////////////////  
  if($opcion == "eliminar")
  {
    $id = $_GET['id'];
    
    //Verifico que Esta actividad no este asignada a ninguna Maquina antes de Eliminarla
    $asignada = $clase->SeleccionarUno("SELECT COUNT(*) FROM actividadxmaquina WHERE actividadid=".$id); 
    //Verifico que Esta actividad no este marcada como realizada en algun mantenimiento
	$realizada = $clase->SeleccionarUno("SELECT COUNT(*) FROM actrealizadas WHERE actividadid=".$id); 
    
	if(($asignada == 0)&&($realizada == 0))
	{
    	$vsql = "DELETE FROM actividades WHERE actividadid=".$id;
	    $clase->EjecutarSQL($vsql);
	    $clase->Aviso(3,"Actividad Eliminada Exitosamente");  				
	}
	else
	   $clase->Aviso(2,"Actividad asignada a una Maquinaria o Actividad Realizada en Mantenimiento. <b>No se puede Eliminar</b>");
	
	header("Location: actividades.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: actividades.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT * FROM actividades WHERE codigo like '%".$criterio."%' OR descripcion like '%".$criterio."%' ORDER BY codigo ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_ACTIVIDADES'] = $vsql;
	header("Location: actividades.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM actividades ORDER BY codigo ASC limit 0,30";
	$_SESSION['SQL_ACTIVIDADES'] = "";
	header("Location: actividades.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/actividades.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Actividades <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_ACTIVIDADES'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_ACTIVIDADES'];
	if($vsql == "")
    	$vsql = "SELECT * FROM actividades ORDER BY codigo ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="35"> Codigo </td>
				 <td width="100"> Descripcion </td>
				 <td width="100"> Realizar Cada (Dias) </td>			
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
				  <td> '.$row['realizarcada'].' d&iacute;as</td>
				  <td> <a href="?opcion=detalles&amp;id='.$row['actividadid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
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