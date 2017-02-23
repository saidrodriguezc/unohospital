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
    $prefijoid    = $_POST['prefijoid'];
	$tipodoc      = strtoupper($_POST['tipodoc']);
	$prefijo      = strtoupper($_POST['prefijo']);	
	$descripcion  = strtoupper($_POST['descripcion']);	
	$consecutivo  = strtoupper($_POST['consecutivo']);
	
	$encab1 = $_POST['encab1'];
	$encab2 = $_POST['encab2'];
	$encab3 = $_POST['encab3'];
	$encab4 = $_POST['encab4'];
	$encab5 = $_POST['encab5'];				

	$pie1 = $_POST['pie1'];
	$pie2 = $_POST['pie2'];
	$pie3 = $_POST['pie3'];
	$pie4 = $_POST['pie4'];
	$pie5 = $_POST['pie5'];				
	
    $imprepos = strtoupper($_POST['impresionpos']);				

	if($opcion == "guardarnuevo")
	{
        $vsql = "INSERT INTO prefijo(tipodoc,prefijo,descripcion,consecutivo,encab1,encab2,encab3,encab4,encab5,pie1,pie2,pie3,pie4,pie5,impresionpos) 
		         values('".$tipodoc."','".$prefijo."','".$descripcion."','".$consectivo."','".$encab1."','".$encab2."','".
				 $encab3."','".$encab4."','".$encab5."','".$pie1."','".$pie2."','".$pie3."','".$pie4."','".$pie5."','S')";

		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Configuracion creada Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE prefijo SET consecutivo ='".$consecutivo."', 
		         descripcion = '".$descripcion."', encab1 ='".$encab1."', encab2 ='".$encab2."', encab3 ='".$encab3."', 
		         encab4 ='".$encab4."' , encab5 ='".$encab5."' , pie1 ='".$pie1."', pie2 ='".$pie2."', pie3 ='".$pie3."',
		         pie4 ='".$pie4."', pie5 ='".$pie5."', impresionpos = '".$imprepos."'
	             WHERE prefijoid=".$prefijoid;
    
	    $clase->EjecutarSQL($vsql);
	
   		$clase->Aviso(1,"Configuracion modificada Exitosamente");  			  
    }	
	
	header("Location: prefijos.php");
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
			     <td width="37"> <img src="images/iconos/configdocs.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nueva Configuracion de Documentos <td>
				 <td>  <a href="prefijos.php"> Volver a Configuraciones </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST">
	        <input type="hidden" name="prefijoid" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Tipo Docum : </label> </td>
			  <td> <input type="text" name="tipodoc" class="Texto15" size="10"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Prefijo : </label> </td>
			  <td> <input type="text" name="prefijo" class="Texto15" size="10"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15" size="25"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Consecutivo : </td>
			  <td> <input type="text" name="consecutivo" class="Texto15" size="25"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Impresion POS : </td>
			  <td> <input type="checkbox" name="impresionpos" value="checked"> Impresion Tipo Punto de Venta POS 
			 </tr>
			</table>

			<br><br>

			<table width="550">
	         <tr> 
			  <td width="120"> <label class="Texto15"> Encabezado 1 : </label> </td>
			  <td> <input type="text" name="encab1" class="Texto15" size="35" value="'.$row['encab1'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Encabezado 2 : </label> </td>
			  <td> <input type="text" name="encab2" class="Texto15" size="35" value="'.$row['encab2'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Encabezado 3 : </label> </td>
			  <td> <input type="text" name="encab3" class="Texto15" size="35" value="'.$row['encab3'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Encabezado 4 : </label> </td>
			  <td> <input type="text" name="encab4" class="Texto15" size="35" value="'.$row['encab4'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Encabezado 5 : </label> </td>
			  <td> <input type="text" name="encab5" class="Texto15" size="35" value="'.$row['encab5'].'"> 
			 </tr>
			</table>

			<br><br>

			<table width="550">
	         <tr> 
			  <td width="120"> <label class="Texto15"> Pie de Pag 1 : </label> </td>
			  <td> <input type="text" name="pie1" class="Texto15" size="35" value="'.$row['pie1'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Pie de Pag 2 : </label> </td>
			  <td> <input type="text" name="pie2" class="Texto15" size="35" value="'.$row['pie2'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Pie de Pag 3 : </label> </td>
			  <td> <input type="text" name="pie3" class="Texto15" size="35" value="'.$row['pie3'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Pie de Pag 4 : </label> </td>
			  <td> <input type="text" name="pie4" class="Texto15" size="35" value="'.$row['pie4'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Pie de Pag 5 : </label> </td>
			  <td> <input type="text" name="pie5" class="Texto15" size="35" value="'.$row['pie5'].'"> 
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
			     <td width="37"> <img src="images/iconos/configdocs.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Configuracion de Documentos <td>
				 <td>  <a href="prefijos.php"> Volver a Configuraciones </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
  	$vsql = "SELECT * FROM prefijo WHERE prefijoid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
		$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="prefijoid" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Tipo Docum : </label> </td>
			  <td> <input type="text" name="tipodoc" class="Texto15" size="10" value="'.$row['tipodoc'].'" disabled> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Prefijo : </label> </td>
			  <td> <input type="text" name="prefijo" class="Texto15" size="10" value="'.$row['prefijo'].'" disabled> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15" value="'.$row['descripcion'].'" size="25"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Consecutivo : </td>
			  <td> <input type="text" name="consecutivo" class="Texto15" value="'.$row['consecutivo'].'" size="25"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Impresion POS : </td>
			  <td> <input type="checkbox" name="impresionpos" value="checked" '.$row['impresionpos'].'> Impresion Tipo Punto de Venta POS 
			 </tr>			 
			</table>

			<br><br>

			<table width="550">
	         <tr> 
			  <td width="120"> <label class="Texto15"> Encabezado 1 : </label> </td>
			  <td> <input type="text" name="encab1" class="Texto15" size="35" value="'.$row['encab1'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Encabezado 2 : </label> </td>
			  <td> <input type="text" name="encab2" class="Texto15" size="35" value="'.$row['encab2'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Encabezado 3 : </label> </td>
			  <td> <input type="text" name="encab3" class="Texto15" size="35" value="'.$row['encab3'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Encabezado 4 : </label> </td>
			  <td> <input type="text" name="encab4" class="Texto15" size="35" value="'.$row['encab4'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Encabezado 5 : </label> </td>
			  <td> <input type="text" name="encab5" class="Texto15" size="35" value="'.$row['encab5'].'"> 
			 </tr>
			</table>

			<br><br>

			<table width="550">
	         <tr> 
			  <td width="120"> <label class="Texto15"> Pie de Pag 1 : </label> </td>
			  <td> <input type="text" name="pie1" class="Texto15" size="35" value="'.$row['pie1'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Pie de Pag 2 : </label> </td>
			  <td> <input type="text" name="pie2" class="Texto15" size="35" value="'.$row['pie2'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Pie de Pag 3 : </label> </td>
			  <td> <input type="text" name="pie3" class="Texto15" size="35" value="'.$row['pie3'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Pie de Pag 4 : </label> </td>
			  <td> <input type="text" name="pie4" class="Texto15" size="35" value="'.$row['pie4'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Pie de Pag 5 : </label> </td>
			  <td> <input type="text" name="pie5" class="Texto15" size="35" value="'.$row['pie5'].'"> 
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
    $vsql = "DELETE FROM prefijo WHERE prefijoid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Configuracion de Documento Eliminada Exitosamente");  		
	header("Location: prefijos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/configdocs.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Configuracion de Documentos <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <td>  </td>
				 <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
  	$vsql = "SELECT * FROM prefijo ORDER BY tipodoc , prefijo";

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="50"> Tipo </td>
			     <td width="50"> Prefijo </td>			     
				 <td width="200">Descripcion </td>
			     <td width="50"> Consecutivo </td>			     
				 <td width="20"> </td>
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
				  <td> '.$row['tipodoc'].' </td>
				  <td> '.$row['prefijo'].' </td>				  
				  <td> '.$row['descripcion'].' </td>
				  <td> '.$row['consecutivo'].' </td>				  
				  <td> </td>
				  <td> <a href="?opcion=detalles&amp;id='.$row['prefijoid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
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
