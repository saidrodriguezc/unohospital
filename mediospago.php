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
  function FormaPagoLetras($codigo)
  {
    if($codigo == "CO")
	   return("Contado");
	if($codigo == "CR") 
	   return("Credito");  
  }
  
  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////  
  if(($opcion == "guardareditado")||($opcion == "guardarnuevo"))
  {
    $mediopagoid     = $_POST['mediopagoid'];
	$codigo       = strtoupper($_POST['codigo']);
	$descripcion  = strtoupper($_POST['descripcion']);	
	$formapago    = strtoupper($_POST['formapago']);
	$icono        = $_POST['icono'];	
	$monext       = strtoupper($_POST['monext']);
	$esdesc       = strtoupper($_POST['esdesc']);				

	if($opcion == "guardarnuevo")
	{
        $vsql = "INSERT INTO mediospago(codigo,descripcion,formapago,icono,monext,esdesc,creador,momento) values('".$codigo."', 
	            '".$descripcion."','".$formapago."','".$icono."','".$monext."','".$esdesc."','".$_SESSION['USERNAME']."',CURRENT_TIMESTAMP)";
    
		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Medio de Pago creado Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE mediospago SET codigo = '".$codigo."' , descripcion = '".$descripcion."',formapago = '".$formapago."',
		         icono = '".$icono."', monext='".$monext."' , esdesc='".$esdesc."'  
	             WHERE mediopagoid=".$mediopagoid;

	    $clase->EjecutarSQL($vsql);
	
   		$clase->Aviso(1,"Medio de Pago modificado Exitosamente");  			  
    }	
	
	header("Location: mediospago.php");
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
			     <td width="37"> <img src="images/iconos/mediospago.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Medio de Pago <td>
				 <td>  <a href="mediospago.php"> Listado de Medios de Pago </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST">
	        <input type="hidden" name="mediospagoid" value="'.$id.'">
			<table width="400">
	         <tr> 
			  <td> <label class="Texto15"> Codigo : </label> </td>
			  <td> <input type="text" name="codigo" class="Texto15" size="10" maxlength="5"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Descripcion : </td>
			  <td> <input type="text" name="descripcion" class="Texto15"  maxlength="25" size="30"> 
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Forma de Pago : </td>
			  <td> 
			        <input type="radio" name="formapago" value="CO" checked> Contado 
					<input type="radio" name="formapago" value="CR"> Credito 
			  </td>		
             </tr>
	         <tr> 
			  <td> <label class="Texto15"> Icono : </td>
			  <td> 
			        <input type="radio" name="icono" value="mpefectivo" checked> <img src="images/iconos/mpefectivo.png" width="32" height="32">
			        <input type="radio" name="icono" value="mptarjeta"> <img src="images/iconos/mptarjeta.png" width="32" height="32">
			        <input type="radio" name="icono" value="mpcredito"> <img src="images/iconos/mpcredito.png" width="32" height="32">
			        <input type="radio" name="icono" value="mpotro"> <img src="images/iconos/mpotro.png" width="32" height="32">										
			  </td>		
             </tr>
	         <tr> 
			  <td> <label class="Texto15"> Especiales : </td>
			  <td> 
			        <input type="checkbox" name="monext" value="S"> <label class="Texto13"> Es Moneda Extranjera?  </label> <br>
					<input type="checkbox" name="esdesc" value="S"> <label class="Texto13"> Forma de Pago para Descuentos? </label>
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

  /////////////////////////////////////////  
  if($opcion == "detalles")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/mediospago.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Medios de Pago <td>
				 <td>  <a href="mediospago.php"> Listado de Medios de Pago </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
  	$vsql = "SELECT * FROM mediospago WHERE mediopagoid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
		$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="mediopagoid" value="'.$id.'">
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
			  <td> <label class="Texto15"> Forma de Pago : </td>
			  <td> 
			        <input type="radio" name="formapago" value="CO" ';

					if($row['formapago'] == "CO")
					 $cont.='checked';

		 	$cont.='> Contado 
					<input type="radio" name="formapago" value="CR" ';

					if($row['formapago'] == "CR")
					 $cont.='checked';
					
			$cont.='> Credito 
			  </td>		
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Icono : </td>
			  <td> 
			        <input type="radio" name="icono" value="mpefectivo" ';
    		        if($row['icono'] == "mpefectivo")
					  $cont.='checked';					
			$cont.='> <img src="images/iconos/mpefectivo.png" width="32" height="32">
			        <input type="radio" name="icono" value="mptarjeta" ';
					if($row['icono'] == "mptarjeta")
					  $cont.='checked';					
			$cont.='> <img src="images/iconos/mptarjeta.png" width="32" height="32">
			        <input type="radio" name="icono" value="mpcredito" ';
					if($row['icono'] == "mpcredito")
					  $cont.='checked';					
			$cont.='> <img src="images/iconos/mpcredito.png" width="32" height="32">
			        <input type="radio" name="icono" value="mpotro" ';
					if($row['icono'] == "mpotro")
					  $cont.='checked';					
			$cont.='> <img src="images/iconos/mpotro.png" width="32" height="32">									
			  </td>		
             </tr>			  
	         <tr> 
			  <td> <label class="Texto15"> Especiales : </td>
			  <td> 
			        <input type="checkbox" name="monext" value="S" ';
					
					if($row['monext'] == "S")
					 $cont.='checked';
					 
			$cont.='> <label class="Texto13"> Es Moneda Extranjera?  </label> <br>
					<input type="checkbox" name="esdesc" value="S" ';

					if($row['esdesc'] == "S")
					 $cont.='checked';
					
			$cont.='> <label class="Texto13"> Forma de Pago para Descuentos? </label>
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
    $vsql = "DELETE FROM mediospago WHERE mediopagoid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Medio de Pago Eliminado Exitosamente");  		
	header("Location: mediospago.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: mediospago.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT * FROM medfiospago WHERE codigo like '%".$criterio."%' OR descripcion like '%".$criterio."%' ORDER BY descripcion ASC Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_MEDIOSPAGO'] = $vsql;
	header("Location: mediospago.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT * FROM mediospago ORDER BY descripcion ASC limit 0,30";
	$_SESSION['SQL_MEDIOSPAGO'] = "";
	header("Location: mediospago.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/mediospago.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Medios de Pago<td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_MEDIOSPAGO'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_MEDIOSPAGO'];
	if($vsql == "")
    	$vsql = "SELECT * FROM mediospago ORDER BY descripcion ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="35"> Codigo </td>
				 <td width="100"> Descripcion </td>
				 <td width="100"> Forma de Pago </td>			
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
				  <td> '.FormaPagoLetras($row['formapago']).'</td>
				  <td> <a href="?opcion=detalles&amp;id='.$row['mediopagoid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
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