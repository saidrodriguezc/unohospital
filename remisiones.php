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
    $bodegaid     = $_POST['bodegaid'];
	$codigo       = strtoupper($_POST['codigo']);
	$descripcion  = strtoupper($_POST['descripcion']);	

	if($opcion == "guardarnuevo")
	{
        $vsql = "INSERT INTO bodegas(codigo,descripcion,creador,momento) values('".$codigo."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";
    
		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Bodega creada Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	header("Location: bodegas.php");
  }
  
  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////    
  if($opcion == "nuevo")
  {
     $hid = $_GET['id'];
     $vsql     = "SELECT T.nombre FROM historiacli HC INNER JOIN terceros T ON (HC.teridpaciente = T.terid) WHERE HC.historiaid = ".$hid; 
	 $pacientex = $clase->SeleccionarUno($vsql);

	 $cont = $clase->Header("S","W");
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/historiacli.png" width="32" height="32" border="0"> </td>
				 <td width="600"> Nueva Remision a Pacientes <td>
    		     <td width="8"> </td>
			   </tr>	 			   
			   <tr class="CabezoteTabla"> 
			     <td width="100"> </td>
			     <td width="100"> Paciente : </td>
				 <td width="600"> <b>'.$pacientex.'</b> <td>
    		     <td width="100"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <form action="remisiones.php?opcion=guardarnuevo" method="POST">
	        <input type="hidden" name="hid" value="'.$hid.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Detalles de la Remision : </label> </td>
			  <td> <textarea name="consentimiento" cols="50" rows="6"></textarea> 
			 </tr>
			</table>
			
			<br><br><center>
			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar Remision </button>  </td>
				</form>
			  </tr>
			</table>';
  }

  ///////////////////////////////////////////////////////////////////////////////  
  ///////////////////////////////////////////////////////////////////////////////    
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/bodegas.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Bodegas <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_BODEGAS'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_BODEGAS'];
	if($vsql == "")
    	$vsql = "SELECT * FROM bodegas ORDER BY descripcion ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="35"> Codigo </td>
				 <td width="100"> Descripcion </td>
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
				  <td> '.$row['descripcion'].' </td>
				  <td> </td>
				  <td> <a href="?opcion=detalles&amp;id='.$row['bodegaid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
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