<?PHP
  session_start(); 
  include("lib/Sistema.php");
  include("lib/libdocumentos.php");
  
  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];


  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $gruposprodid  = $_POST['gruposprodid'];
	$codigo        = strtoupper($_POST['codigo']);
	$descripcion   = strtoupper($_POST['descripcion']);	

	$meta01        = $_POST['meta01'];
	$meta02        = $_POST['meta02'];
	$meta03        = $_POST['meta03'];
	$meta04        = $_POST['meta04'];
	$meta05        = $_POST['meta05'];
	$meta06        = $_POST['meta06'];
	$meta07        = $_POST['meta07'];
	$meta08        = $_POST['meta08'];
	$meta09        = $_POST['meta09'];
	$meta10        = $_POST['meta10'];
	$meta11        = $_POST['meta11'];
	$meta12        = $_POST['meta12'];											

	if($opcion == "guardarnuevo")
	{
        $vsql = "INSERT INTO gruposprod(codigo,descripcion,creador,momento) values('".$codigo."', 
	            '".$descripcion."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";
    
		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Grupo de Productos creada Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE gruposprod SET codigo = '".$codigo."' , descripcion = '".$descripcion."' , 
		         meta01=".$meta01." , meta02=".$meta02." , meta03=".$meta03." , meta04=".$meta04." ,
		         meta05=".$meta05." , meta06=".$meta06." , meta07=".$meta07." , meta08=".$meta08." ,				 
		         meta09=".$meta09." , meta10=".$meta10." , meta11=".$meta11." , meta12=".$meta12."			 
	             WHERE gruposprodid=".$gruposprodid;

	    $clase->EjecutarSQL($vsql);
	
   		$clase->Aviso(1,"Grupo de Productos modificada Exitosamente");  			  
    }	
	
	header("Location: gruposprod.php");
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
			     <td width="37"> <img src="images/iconos/gruposprod.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Grupo de Productos <td>
				 <td>  <a href="gruposprod.php"> Listado de Grupos de Productos </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';		
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST">
	        <input type="hidden" name="gruposprodid" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" maxlength="5"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="45" size="30"> 
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
			     <td width="37"> <img src="images/iconos/gruposprod.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Grupos de Productos <td>
				 <td>  <a href="gruposprod.php"> Listado de Grupos de Productos </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
  	$vsql = "SELECT * FROM gruposprod WHERE gruposprodid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
		$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="gruposprodid" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" value="'.$row['codigo'].'"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15" value="'.$row['descripcion'].'" size="30" maxlenght="40"> 
			 </tr>
			</table>

			<br><br><br>
			<b><label class="Texto15"> Metas de Cumplimiento </label> </b> <br><br>
            <table width="600">
			  <tr>
			     <td> <label class="Texto15"> Ene </td>
				 <td> <input type="text" name="meta01" class="Texto15" value="'.$row['meta01'].'" size="8"> </td>
			     <td> <label class="Texto15"> Feb </td>
			     <td> <input type="text" name="meta02" class="Texto15" value="'.$row['meta02'].'" size="8"> </td>
			     <td> <label class="Texto15"> Mar </td>
				 <td> <input type="text" name="meta03" class="Texto15" value="'.$row['meta03'].'" size="8"> </td>						 						 
			     <td> <label class="Texto15"> Abr </td>
				 <td> <input type="text" name="meta04" class="Texto15" value="'.$row['meta04'].'" size="8"> </td>
              </tr>
  		      <tr>
   			     <td> <label class="Texto15"> May </td>
				 <td> <input type="text" name="meta05" class="Texto15" value="'.$row['meta05'].'" size="8"> </td>
     			 <td> <label class="Texto15"> Jun </td>
				 <td> <input type="text" name="meta06" class="Texto15" value="'.$row['meta06'].'" size="8"> </td>						 						 
   			     <td> <label class="Texto15"> Jul </td>
				 <td> <input type="text" name="meta07" class="Texto15" value="'.$row['meta07'].'" size="8"> </td>
     			 <td> <label class="Texto15"> Ago </td>
				 <td> <input type="text" name="meta08" class="Texto15" value="'.$row['meta08'].'" size="8"> </td>						 						 
              </tr>
  		      <tr>
   			     <td> <label class="Texto15"> Sep </td>
				 <td> <input type="text" name="meta09" class="Texto15" value="'.$row['meta09'].'" size="8"> </td>
     			 <td> <label class="Texto15"> Oct </td>
				 <td> <input type="text" name="meta10" class="Texto15" value="'.$row['meta10'].'" size="8"> </td>						 						 
   			     <td> <label class="Texto15"> Nov </td>
				 <td> <input type="text" name="meta11" class="Texto15" value="'.$row['meta11'].'" size="8"> </td>
     			 <td> <label class="Texto15"> Dic </td>
				 <td> <input type="text" name="meta12" class="Texto15" value="'.$row['meta12'].'" size="8"> </td>						 						 
              </tr>
			 </table>
			 			
			<br><br><br>
			
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
    $vsql = "DELETE FROM gruposprod WHERE lineaprodid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Grupo de Productos eliminada Exitosamente");  		
	header("Location: gruposprod.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: gruposprod.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT * FROM gruposprod WHERE codigo like '%".$criterio."%' OR descripcion like '%".$criterio."%' ORDER BY descripcion ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_GRUPOSPROD'] = $vsql;
	header("Location: gruposprod.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM gruposprod ORDER BY descripcion ASC limit 0,30";
	$_SESSION['SQL_GRUPOSPROD'] = "";
	header("Location: gruposprod.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/gruposprod.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Grupos de Productos <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_GRUPOSPROD'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_GRUPOSPROD'];
	if($vsql == "")
    	$vsql = "SELECT * FROM gruposprod ORDER BY codigo ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

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
				  <td width="35"> '.$row['codigo'].' </td>
				  <td width="100"> '.$row['descripcion'].' </td>
				  <td width="100"> </td>
				  <td> <a href="?opcion=detalles&amp;id='.$row['gruposprodid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
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