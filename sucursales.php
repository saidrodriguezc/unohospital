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
    $bodegaid   = $_POST['sucid'];
	$codigo     = strtoupper($_POST['codigo']);
	$nombre     = strtoupper($_POST['nombre']);	
    $consecter  = strtoupper($_POST['consecter']);	

	if($opcion == "guardarnuevo")
	{
        $vsql = "INSERT INTO sucursales(codigo,nombre,consecter,creador,momento) values('".$codigo."', 
	            '".$nombre."','".$_SESSION['USERNAME']."','".$consecter."',CURRENT_TIMESTAMP)";
    
		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Sucursal creada Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE sucursales SET codigo = '".$codigo."' , nombre = '".$nombre."' , consecter = '".$consecter."' 
	             WHERE sucid=".$sucid;
    
	    $clase->EjecutarSQL($vsql);
	
   		$clase->Aviso(1,"Sucursal modificada Exitosamente");  			  
    }	
	
	header("Location: sucursales.php");
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
			     <td width="37"> <img src="images/iconos/sucursales.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nueva Sucursal <td>
				 <td>  <a href="sucursales.php"> Listado de Sucursales </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST">
	        <input type="hidden" name="sucid" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" maxlength="5"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Nombre : </td>
			  <td> <input type="text" name="nombre" class="Texto15"  maxlength="25" size="30"> 
			 </tr>
			</table>
			<br>
			<fieldset style="width:400px;">
             <legend> <b> Configuracion de la Sucursal </b> </legend>
			   <br>
               <table width="400">
			    <tr> 
			     <td> <label class="Texto15"> Consec Clientes : </td>
			     <td> <input type="text" name="consecter" class="Texto15"  maxlength="25" size="20"> 
			    </tr>
			   </table>
			   <br>
			</fieldset>
			 
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
			     <td width="37"> <img src="images/iconos/sucursales.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Sucursales <td>
				 <td>  <a href="sucursales.php"> Listado de Sucursales </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
  	$vsql = "SELECT * FROM sucursales WHERE sucid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
		$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="sucid" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" value="'.$row['codigo'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Nombre : </td>
			  <td> <input type="text" name="nombre" class="Texto15" value="'.$row['nombre'].'" size="30"> 
			 </tr>
			</table>
			<br>
			<fieldset style="width:400px;">
             <legend> <b> Configuracion de la Sucursal </b> </legend>
			   <br>
               <table width="400">
			    <tr> 
			     <td> <label class="Texto15"> Consec Clientes : </td>
			     <td> <input type="text" name="consecter" class="Texto15" value="'.$row['consecter'].'"  maxlength="25" size="20"> 
			    </tr>
			   </table>
			   <br>
			</fieldset>
			
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
    $vsql = "DELETE FROM sucursales WHERE sucid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Sucursal Eliminada Exitosamente");  		
	header("Location: sucursales.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: sucursales.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT * FROM sucursales WHERE codigo like '%".$criterio."%' OR nombre like '%".$criterio."%' ORDER BY nombre ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_SUCURSALES'] = $vsql;
	header("Location: sucursales.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM sucursales ORDER BY nombre ASC limit 0,30";
	$_SESSION['SQL_SUCURSALES'] = "";
	header("Location: sucursales.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/sucursales.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Sucursales <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_SUCURSALES'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_SUCURSALES'];
	if($vsql == "")
    	$vsql = "SELECT * FROM sucursales ORDER BY nombre ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="35"> Codigo </td>
				 <td width="100"> Nombre </td>
				 <td width="100">  </td>			
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
				  <td> </td>
				  <td> <a href="?opcion=detalles&amp;id='.$row['sucid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
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