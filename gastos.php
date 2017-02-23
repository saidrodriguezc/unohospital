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
    $gastoid      = $_POST['gastoid'];
	$maquinariaid = $_POST['maquinariaid'];
	$conceptoid   = $_POST['conceptoid'];	
	$proveedorid  = $_POST['proveedorid'];		
	$estadogasto  = $_POST['estadogasto'];			
	$valor        = $_POST['valor'];			
	$observaciones= $_POST['observaciones'];			
	$fechahora    = substr($_POST['fechahora'],6,4)."-".substr($_POST['fechahora'],3,2)."-".substr($_POST['fechahora'],0,2);			

	if($opcion == "guardarnuevo")
	{
        $vsql = 'INSERT INTO gastos(proveedorid,maquinariaid,conceptoid,valor,fechahora,observaciones,estado,creador) 
		         values('.$proveedorid.','.$maquinariaid.','.$conceptoid.','.$valor.',"'.$fechahora.'","'.$observaciones.'","'.$estadogasto.
				'","'.$_SESSION['USUARIO'].'")';
    
		$cant = $clase->EjecutarSQL($vsql);
	
		if($cant == 1)
    		$clase->Aviso(1,"Gasto Registrado Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = 'UPDATE gastos 
		         SET proveedorid = '.$proveedorid.' , maquinariaid = '.$maquinariaid.' ,
				 conceptoid = '.$conceptoid.' , estado = "'.$estadogasto.'" ,
				 valor = '.$valor.' , fechahora = "'.$fechahora.'", observaciones="'.$observaciones.'"
	             WHERE gastoid ='.$gastoid;

	    $clase->EjecutarSQL($vsql);
	
   		$clase->Aviso(1,"Gasto modificado Exitosamente");  			  
    }	
	
	header("Location: gastos.php");
  }
  
  /////////////////////////////////////////////////////////////////////////  
  /////////////////////////////////////////////////////////////////////////    
  if($opcion == "nuevo")
  {
     $id = $_GET['id'];
	 $cont = $clase->Header("S","W"); 	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/gastos.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Gasto <td>
				 <td>  <a href="gastos.php"> Listado de Gastos </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';		
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST">
	        <input type="hidden" name="gastoid" value="'.$id.'">
			<table width="600">
	         <tr> 
			  <td> <label class="Texto15"> Maquinaria : </label> </td>
			  <td> ';
    $cont.= $clase->CrearCombo("maquinariaid","maquinaria","descripcion","maquinariaid","","N"); 
	$cont.=' </tr>
	         <tr> 
			  <td> <label class="Texto15"> Concepto : </td>
			  <td> ';
    $cont.= $clase->CrearCombo("conceptoid","conceptos","descripcion","conceptoid","","N"); 
	$cont.=' </tr>			 
	         <tr> 
			  <td> <label class="Texto15"> Proveedor : </td>
			  <td>';
    $cont.= $clase->CrearCombo("proveedorid","proveedores","nombre","proveedorid","","N"); 
	$cont.='  </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Valor : </td>
			  <td> <input type="text" name="valor" value="'.number_Format(0).'">
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Fecha : </td>
			  <td> <input type="text" name="fechahora" value="'.date('d/m/Y').'">
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Estado : </td>
			  <td>';
    $cont.= $clase->CrearCombo("estadogasto","estadosgasto","descripcion","codigo","","N"); 
	$cont.=' </select>
			 </tr>			 
	         <tr> 
			  <td> <label class="Texto15"> Observaciones : </td>
			  <td> <textarea name="observaciones" cols="38" rows="3"></textarea>
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
			     <td width="37"> <img src="images/iconos/gastos.png" width="32" height="32" border="0"> </td>
				 <td width="510"> Gastos de Maquinarias <td>
				 <td>  <a href="gastos.php"> Listado de Gastos </a> </td>
    		     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
  	$vsql = "SELECT * FROM gastos WHERE gastoid=".$id;
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	if($row = mysql_fetch_array($result))
	{
	$fechahora  = substr($row['fechahora'],8,2)."-".substr($row['fechahora'],5,2)."-".substr($row['fechahora'],0,4);			
	$cont.='<br><br><br><center>
            <form action="?opcion=guardareditado" method="POST">
	        <input type="hidden" name="gastoid" value="'.$id.'">
			<table width="600">
	         <tr> 
			  <td> <label class="Texto15"> Maquinaria : </label> </td>
			  <td> ';
    $cont.= $clase->CrearCombo("maquinariaid","maquinaria","descripcion","maquinariaid",$row['maquinariaid'],"N"); 
	$cont.=' </tr>
	         <tr> 
			  <td> <label class="Texto15"> Concepto : </td>
			  <td> ';
    $cont.= $clase->CrearCombo("conceptoid","conceptos","descripcion","conceptoid",$row['conceptoid'],"N"); 
	$cont.=' </tr>			 
	         <tr> 
			  <td> <label class="Texto15"> Proveedor : </td>
			  <td>';
    $cont.= $clase->CrearCombo("proveedorid","proveedores","nombre","proveedorid",$row['proveedorid'],"N"); 
	$cont.='  </td>
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Valor : </td>
			  <td> <input type="text" name="valor" value="'.$row['valor'].'">
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Fecha : </td>
			  <td> <input type="text" name="fechahora" value="'.$fechahora.'">
			 </tr>
	         <tr> 
			  <td> <label class="Texto15"> Estado : </td>
			  <td>';
    $cont.= $clase->CrearCombo("estadogasto","estadosgasto","descripcion","codigo",$row['estado'],"N"); 
	$cont.=' </select>
			 </tr>	
			  <tr> 
			  <td> <label class="Texto15"> Observaciones : </td>
			  <td> <textarea name="observaciones" cols="38" rows="3">'.$row['observaciones'].'</textarea>
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
    $vsql = "DELETE FROM gastos WHERE gastoid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Gasto Eliminado Exitosamente");  		
	header("Location: gastos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: gastos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    $vsql = "SELECT P.nombre prove , M.descripcion maqui , C.descripcion conce , G.* 
			 FROM gastos G 
			 INNER JOIN proveedores P ON (P.proveedorid = G.proveedorid)
			 INNER JOIN maquinaria M ON (M.maquinariaid = G.maquinariaid)
			 INNER JOIN conceptos C ON (C.conceptoid = G.conceptoid)
			 WHERE P.nombre like '%".$criterio."%' OR M.descripcion like '%".$criterio."%' 
             OR C.descripcion like '%".$criterio."%' OR G.valor like '%".$criterio."%' 
		     ORDER BY G.fechahora DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['SQL_GASTOS'] = $vsql;
	header("Location: gastos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
    	$vsql = "SELECT P.nombre prove , M.descripcion maqui , C.descripcion conce , G.* 
				 FROM gastos G 
				 INNER JOIN proveedores P ON (P.proveedorid = G.proveedorid)
				 INNER JOIN maquinaria M ON (M.maquinariaid = G.maquinariaid)
				 INNER JOIN conceptos C ON (C.conceptoid = G.conceptoid)
				 ORDER BY G.fechahora DESC limit 0,30";

	$_SESSION['SQL_GASTOS'] = "";
	header("Location: gastos.php");
  }

/////////////////////////////////////////////////////////////////////////////////  
/////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/gastos.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Gastos de Maquinarias <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_GASTOS'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_GASTOS'];
	if($vsql == "")
    	$vsql = "SELECT P.nombre prove , M.descripcion maqui , C.descripcion conce , G.* 
				 FROM gastos G 
				 INNER JOIN proveedores P ON (P.proveedorid = G.proveedorid)
				 INNER JOIN maquinaria M ON (M.maquinariaid = G.maquinariaid)
				 INNER JOIN conceptos C ON (C.conceptoid = G.conceptoid)
				 ORDER BY G.fechahora DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="100">  Proveedor </td>
				 <td width="100"> Maquinaria </td>
				 <td width="100"> Concepto </td>				 
				 <td width="30"> Estado </td>			
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
				  <td> '.$row['prove'].' </td>
				  <td> '.$row['maqui'].' </td>
				  <td> '.$row['conce'].' </td>				  
				  <td> '.$row['estado'].' </td>				  				  
				  <td> <a rel="facebox" href="?opcion=opciones&amp;id='.$row['gastoid'].'"> <img src="images/funciones.png" border="0"></td>
				  <td> <a href="?opcion=detalles&amp;id='.$row['gastoid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
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
