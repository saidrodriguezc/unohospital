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
    $mantenimid    = $_POST['id'];
	$numorden      = strtoupper($_POST['numorden']);
	$maquinariaid  = $_POST['maquinariaid'];	
	$operariopro   = $_POST['operariopro'];		
	$fechaX        = $_POST['fecha'];		
	$horaX         = $_POST['hora'];			
	
	$fecha = substr($fechaX,6,4)."-".substr($fechaX,3,2)."-".substr($fechaX,0,2);
	$hora  = $horaX;
	
	if($maquinariaid == ""){
	  $clase->Aviso(2,"Debe Seleccionar una Maquinaria. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: mantenimientos.php");
	  exit();
	}
    
	if($operariopro == ""){	
	  $clase->Aviso(2,"Debe Seleccionar un Operario. &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
      header("Location: mantenimientos.php");
	  exit();
	}	

	if($opcion == "guardarnuevo")
	{
        // Inserto el Nuevo Mantenimiento con estado PROgramado
		$vsql = "INSERT INTO mantenimientos(numorden,maquinariaid,operariopro,operariorea,fecha,hora,estadoact,observacion) 
		         values('".$numorden."',".$maquinariaid.",".$operariopro.",".$operariopro.",'".$fecha."','".$hora."','PRO','".$observ."')";    				 
	    $cant = $clase->EjecutarSQL($vsql);
        $mantenimid = $clase->SeleccionarUno("SELECT MAX(mantenimid) FROM mantenimientos");
		
		// Verifico si Esa Maquinaria tiene actividades preestablecidas
		$tienepredefinidas = $clase->BDLockup($maquinariaid,"actividadxmaquina","maquinariaid","actividadid");
		
		// Si las tiene - Solo Adiciono esas
		if($tienepredefinidas != "")
 		   $vsql = "SELECT * FROM actividadxmaquina AM INNER JOIN actividades A ON (AM.actividadid = A.actividadid) WHERE AM.maquinariaid=".$maquinariaid;
		// Si no - Le agrego todas las actividades posibles a Realizar   
		else
 		   $vsql = "SELECT * FROM actividades A";

    	$conex  = $clase->Conectar();
        $result = mysql_query($vsql,$conex);
        while($row = mysql_fetch_array($result)) 
	    {
          $vsql2="INSERT INTO actrealizadas(mantenimid,actividadid,fecha,hora) values(".$mantenimid.",".$row["actividadid"].",'','')";
          $clase->EjecutarSQL($vsql2);  
        }		   
	
		if($cant == 1)
    		$clase->Aviso(1,"Mantenimiento Programado Exitosamente");  	
		else
			$clase->Aviso(2,"Error al Crear el Registro &nbsp;&nbsp; <a href=\"javascript:history.back(-1);\"> Intentar de Nuevo ? </a>");  		
    }
	
	if($opcion == "guardareditado")
	{
        $vsql = "UPDATE maquinaria SET codigo = '".$codigo."' , descripcion = '".$descripcion."' , 
		         marca=".$marcamaqid.", tipo=".$tipomaqid." , modelo = '".$modelo."' ,
				 serie = '".$serie."' , ano = '".$ano."' , placa = '".$placa."' ,
				 feccompra = '".$feccompra2."' WHERE maquinariaid=".$maquinariaid;

		$clase->EjecutarSQL($vsql);
		
   		$clase->Aviso(1,"Maquinaria modificada Exitosamente");  			  
    }	
	
	header("Location: mantenimientos.php");
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
			     <td width="37"> <img src="images/iconos/productos.png" width="32" height="32" border="0"> </td>
				 <td width="520"> Nuevo Mantenimiento <td>
				 <td>  <a href="mantenimientos.php"> Listado de Mantenimientos </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';		
	
	$cont.='<br><br><br><center>
            <form action="?opcion=guardarnuevo" method="POST" name="x">
			<table width="700">
	         <tr height="30"> 
			  <td width="200"> <label class="Texto15"> Orden No. : </label> </td>
			  <td> <input type="text" name="numorden" class="Texto15" size="10" maxlength="10" id="default" autocomplete="off" tabindex="1" > 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Maquinaria : </td>
			  <td>
                   <select name="maquinariaid" class="Texto15"> 
			         <option> </option>';
			       
     $vsql = "SELECT M.maquinariaid , M.descripcion , M.placa , M.color , M.ano , MM.descripcion desmarca , T.descripcion destipo 
			  FROM maquinaria M INNER JOIN marcamaq MM ON (MM.marcaid = M.marca)
			  INNER JOIN tipomaq T ON (T.tipoid = M.tipo) ORDER BY 6";
	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     $i=0;
	 while($row = mysql_fetch_array($result)) 
	 {
        $cont.='<option value="'.$row['maquinariaid'].'"> '.$row['descripcion'].' - '.$row['color'].' - '.$row['placa'].' - '.$row['desmarca'].' - '.$row['destipo'].' </option>';
		$i++;
     }		   
	 
	 $cont.='</select>';
	 			    
   	 $cont.='</tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Operario Programado : </td>
			  <td> 
                   <select name="operariopro" class="Texto15"> 
			         <option> </option>';
			       
     $vsql = "SELECT * FROM terceros T ORDER BY nombre ASC";
	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     $i=0;
	 while($row = mysql_fetch_array($result)) 
	 {
        $cont.='<option value="'.$row['terid'].'"> '.$row['nombre'].' - '.$row['nit'].' </option>';
		$i++;
     }		   
	 
	 $cont.='</select>';
	 			    
	 $cont.='</td> 
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Fecha : </label> </td>
			  <td> <input type="text" name="fecha" class="Texto15"  maxlength="10" size="10" autocomplete="off" tabindex="9" value="'.date("d/m/Y").'"> 
			       <font color="gray">Formato Dia / Mes / A&ntilde;o</font> </td>
			 </tr>
	         <tr height="30"> 
			  <td> <label class="Texto15"> Hora : </label> </td>
			  <td> <input type="text" name="hora" class="Texto15"  maxlength="10" size="10" autocomplete="off" tabindex="9" value="'.date("H:m:s").'"> 
			       <font color="gray">Formato 24H</font> </td>
			 </tr>
	         <tr height="30" valign="top"> 
			  <td> <label class="Texto15"> Observaciones : </label> </td>
			  <td> <textarea name="observ" class="Texto15" rows="5" cols="45"> </textarea> </td>
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

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "timeline")
  {
     $id   = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/mantenimientos.png" width="32" height="32" border="0"> </td>
				 <td width="590"> Estadisticas del Mantenimiento <td>
				 <td>  <a href="mantenimientos.php"> Lista de Mantenim </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	

	$cont.= FuncionesEspeciales(4,$id);			 

	$cont.= '<form action="?opcion=cambioestado2" method="POST">
	         <input type="hidden" name="mantenimid" value="'.$id.'">
	         <center><br><br>
	         <b> Estadisticas del Mantenimiento </b>
			 <br><br><br>';	

	$vsql   = "SELECT M . * , M2 . * , E.descripcion estado , OPP.nombre programado, OPR.nombre realizo
			   FROM mantenimientos M
			   INNER JOIN maquinaria M2 ON ( M.maquinariaid = M2.maquinariaid ) 
			   INNER JOIN estados E ON ( M.estadoact = E.codigo ) 
			   INNER JOIN terceros OPP ON ( OPP.terid = M.operariopro ) 
			   INNER JOIN terceros OPR ON ( OPR.terid = M.operariorea ) 
			   WHERE M.mantenimid =".$id;		 
			   
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
    if($row = mysql_fetch_array($result));
    {
    $cont.='<table align="center" width="750">
         	  <tr class="TablaDocsPar">
	           <td width="20"> &nbsp; </td>   
			   <td> Orden No. : </td>
			   <td> '.$row["numorden"].' </td> 
               <td width="50"> &nbsp; </td>   
	           <td> Maquinaria : </td>
			   <td> '.$row["descripcion"].' </td> 
			  </tr>			  
         	  <tr class="TablaDocsPar">
	           <td width="20"> &nbsp; </td>   
			   <td> Fecha Inicio : </td>
			   <td> '.$row["fecha"].' </td> 
               <td width="50"> &nbsp; </td>   
	           <td> Hora Inicio : </td>
			   <td> '.$row["hora"].' </td> 
			  </tr>			  
         	  <tr class="TablaDocsPar">
	           <td width="20"> &nbsp; </td>   
			   <td> Estado Actual : </td>
			   <td> '.$row["estado"].' </td> 
               <td width="50"> &nbsp; </td>   
	           <td> Observaciones : </td>
			   <td> '.$row["observacion"].' </td> 
			  </tr>			  
         	  <tr class="TablaDocsPar">
	           <td width="20"> &nbsp; </td>   
			   <td> Programado a : </td>
			   <td> '.$row["programado"].' </td> 
               <td width="50"> &nbsp; </td>   
	           <td> Realizado Por : </td>
			   <td> '.$row["realizo"].' </td> 
			  </tr>			  
	        </table> <br><br>';			
	}

    $cont.='<b> Historico de Actividades </b>
	        <br>		
			<table align="center" width="750">';

	$vsql2   = "SELECT AR. * , A. * 
			   FROM mantenimientos M
			   INNER JOIN actrealizadas AR ON ( M.mantenimid = AR.mantenimid ) 
			   INNER JOIN actividades A ON ( A.actividadid = AR.actividadid ) 
			   WHERE M.mantenimid =".$id;		 

  	$vsql2   = "SELECT * from actividades"; 
    $result2 = mysql_query($vsql2,$conex);
    while($row2 = mysql_fetch_array($result2));
    {
		$cont.='<tr class="TablaDocsPar">
	             <td width="20"> &nbsp; </td>   
	             <td> Actividad : </td>
			     <td> '.$row2["descripcion"].' </td> 
			     <td> Estado : </td>
			     <td> '.$row2["fecha"].' </td> 
			     <td> Momento : </td>
			     <td> '.$row2["fecha"].' '.$row2["hora"].' </td> 
			    </tr>';
	}
	
    $cont.='<br><br><center>';
  }

 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "cambioestado2")
  {
    $mantenimid  = $_POST['mantenimid']; 
    $estadoact   = $_POST['estadoact']; 	
    $fecha       = date("Y-n-d");
    $hora        = date("h:m:s");
			
    $vsql = "INSERT INTO estadosxmante(manteid,estadoid,fecha,hora) values(".$mantenimid.",'".$estadoact."','".$fecha."','".$hora."')";
    $clase->EjecutarSQL($vsql);		

    $vsql = "UPDATE mantenimientos SET estadoact = '".$estadoact."' WHERE mantenimid=".$mantenimid;
    $clase->EjecutarSQL($vsql);		
	
   	$clase->Aviso(1,"Se ha Cambiado el Estado al Mantenimiento Exitosamente");  			  	
	header("Location: mantenimientos.php?opcion=cambioestado&id=".$mantenimid);
  }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "cambioestado")
  {
     $id   = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/mantenimientos.png" width="32" height="32" border="0"> </td>
				 <td width="590"> Cambio de Estado del Mantenimiento <td>
				 <td>  <a href="mantenimientos.php"> Lista de Mantenim </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table>'; 			 
	$cont.= FuncionesEspeciales(3,$id);			 
			 
	$cont.= '<form action="?opcion=cambioestado2" method="POST">
	         <input type="hidden" name="mantenimid" value="'.$id.'">
	         <center><br><br>
	         <b> Seleccione el Estado Actual del Mantenimiento </b>
			 <br><br>
			 <table align="center" width="300">';	

	$actual = $clase->BDLockup($id,"mantenimientos","mantenimid","estadoact");	
	$vsql   = "SELECT * FROM estados ORDER BY orden ASC";		 
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

    $cont.='<tr>
	          <td> Estado : </td>
			  <td> <select name="estadoact">
			          <option value=""> </option>';				  
			  
	while($row = mysql_fetch_array($result))
	{
	   if($actual == $row["codigo"])
	      $cont.='<option value="'.$row["codigo"].'" selected> '.$row["descripcion"].' </option>';
	   else	
  	      $cont.='<option value="'.$row["codigo"].'"> '.$row["descripcion"].' </option>';
	}
			
    $cont.='</form> </select> </td> </tr>
	        </table> <br><br>			
			<table> <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>				
			</tr> </table>';
  }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "asignaroperario2")
  {
    $mantenimid  = $_POST['mantenimid']; 
    $operariorea = $_POST['operariorea']; 	
	
    $vsql = "UPDATE mantenimientos SET operariorea = ".$operariorea." WHERE mantenimid=".$mantenimid;
	$clase->EjecutarSQL($vsql);		
	
   	$clase->Aviso(1,"Operario Asignado al Mantenimiento Exitosamente");  			  	
	header("Location: mantenimientos.php?opcion=asignaroperario&id=".$mantenimid);
  }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "asignaroperario")
  {
     $id   = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/mantenimientos.png" width="32" height="32" border="0"> </td>
				 <td width="590"> Operario que Realizo el Mantenimiento <td>
				 <td>  <a href="mantenimientos.php"> Lista de Mantenim </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table>'; 			 
	$cont.= FuncionesEspeciales(2,$id);			 
			 
	$cont.= '<form action="?opcion=asignaroperario2" method="POST">
	         <input type="hidden" name="mantenimid" value="'.$id.'">
	 		 <center><br><br>
	         <b> Seleccione el Operario que realizo el Mantenimiento </b>
			 <br><br>
			 <table align="center" width="500">';	
	
	$actual = $clase->BDLockup($id,"mantenimientos","mantenimid","operariorea");
	$vsql   = "SELECT * FROM terceros ORDER BY nombre ASC";		 
    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

    $cont.='<tr>
	          <td> Operario : </td>
			  <td> <select name="operariorea">
			          <option value=""> </option>';				  
			  
	while($row = mysql_fetch_array($result))
	{
	    if($actual == $row["terid"])
   		   $cont.='<option value="'.$row["terid"].'" selected> '.$row["nombre"].' </option>';
		else   
   		   $cont.='<option value="'.$row["terid"].'"> '.$row["nombre"].' </option>';			
	}
		   
    $cont.='</select> </td> </tr>
	        </table>
	        <br><br>			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>';  //onclick="location.href=\'?opcion=eliminar&amp;id='.$row['ciudadid']
  }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "actrealizadas2")
  {
    $mantenimid  = $_POST['mantenimid']; 
    $fecha       = date("Y-n-d");
    $hora        = date("h:m:s");

  	$vsql = "SELECT A.descripcion , AR.* FROM actrealizadas AR 
	         INNER JOIN mantenimientos M ON (AR.mantenimid = M.mantenimid) 
         	 INNER JOIN actividades A ON (A.actividadid = AR.actividadid) 
	         WHERE M.mantenimid=".$mantenimid;

    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	while($row = mysql_fetch_array($result))
	{	
       $actrealizada = $_POST[$row["actividadid"]];
	   
	   if($actrealizada == 'S')	   
	   {
	      $vsql = "UPDATE actrealizadas SET fecha ='".$fecha."' , hora='".$hora."' WHERE mantenimid=".$mantenimid." AND actividadid=".$row["actividadid"];
		  $clase->EjecutarSQL($vsql);		
	   }	  
	}
	
   	$clase->Aviso(1,"Actividades Realizadas Guardadas Exitosamente");  			  	
	header("Location: mantenimientos.php?opcion=actrealizadas&id=".$mantenimid);
  }
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
  if(($opcion == "detalles")||($opcion == "actrealizadas"))
  {
     $id   = $_GET['id'];
	 $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/mantenimientos.png" width="32" height="32" border="0"> </td>
				 <td width="590"> Opciones de Mantenimientos <td>
				 <td>  <a href="mantenimientos.php"> Lista de Mantenim </a> </td>
    		     <td width="27"> <a href="?opcion=eliminar&id='.$id.'"> <img src="images/iconoborrar.png" border="0"> </a> </td>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table>'; 			 
	$cont.= FuncionesEspeciales(1,$id);			 
			 
	$cont.= '<form action="?opcion=actrealizadas2" method="POST">
	         <input type="hidden" name="mantenimid" value="'.$id.'">	         
	         <center><br><br>
	         <b> Marque las Actividades Realizadas <br> durante el Mantenimiento </b>
			 <br><br>
			 <table align="center" width="500">';	
	
  	$vsql = "SELECT A.descripcion , AR.* FROM actrealizadas AR 
	         INNER JOIN mantenimientos M ON (AR.mantenimid = M.mantenimid) 
         	 INNER JOIN actividades A ON (A.actividadid = AR.actividadid) 
	         WHERE M.mantenimid=".$id;

    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	while($row = mysql_fetch_array($result))
	{
	   if($row["fecha"] != "0000-00-00")
	      $cont.='<tr><td><input type="checkbox" name="'.$row["actividadid"].'" value="S" checked> '.$row["descripcion"].' </td> </tr>';	    
	   else
	      $cont.='<tr><td><input type="checkbox" name="'.$row["actividadid"].'" value="S"> '.$row["descripcion"].' </td> </tr>';	    	   	  
	}		
	
    $cont.='</table>
	        <br><br>			
			<table>
			   <tr>
    		    <td>  <button type="submit" name="guardar" tabindex="4" id="continuar" class="Botonverde"> Guardar </button>  </td>
				</form>
			  </tr>
			</table>'; 
  }
  
  /////////////////////////////////////////  
  if($opcion == "eliminar")
  {
    $id = $_GET['id'];
    $vsql = "DELETE FROM mantenimientos WHERE mantenimid=".$id;
	$clase->EjecutarSQL($vsql);
	$clase->Aviso(3,"Mantenimiento eliminado Exitosamente");  		
	header("Location: mantenimientos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "masregistros")
  {
    $actual = $_SESSION["NUMREGISTROSXCONSULTA"];
	$ahora = $actual + 50;
	$_SESSION["NUMREGISTROSXCONSULTA"] = $ahora; 
	header("Location: mantenimientos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT M.mantenimid , M.numorden , MQ.descripcion maquina , T1.nombre operario , M.fecha , M.hora , E.descripcion estado
		         FROM mantenimientos M 
				 INNER JOIN maquinaria MQ ON (M.maquinariaid = MQ.maquinariaid)
			     INNER JOIN terceros T1 ON (T1.terid = M.operariopro)
				 INNER JOIN terceros T2 ON (T2.terid = M.operariorea)
				 INNER JOIN estados E ON (E.codigo = M.estadoact)
				 WHERE M.numorden like '%".$criterio."%' OR MQ.descripcion like '%".$criterio."%' OR T1.nombre like '%".$criterio."%'
				 OR T2.nombre like '%".$criterio."%' ORDER BY M.fecha DESC , M.hora DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

    $_SESSION['SQL_MANTENIMIENTOS'] = $vsql;
	header("Location: mantenimientos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "nofiltro")
  {
    $criterio = $_POST['criterio'];
  	$vsql = "SELECT M.mantenimid , M.numorden , MQ.descripcion maquina , T1.nombre operario , M.fecha , M.hora , E.descripcion estado
		         FROM mantenimientos M 
				 INNER JOIN maquinaria MQ ON (M.maquinariaid = MQ.maquinariaid)
			     INNER JOIN terceros T1 ON (T1.terid = M.operariopro)
				 INNER JOIN terceros T2 ON (T2.terid = M.operariorea)
				 INNER JOIN estados E ON (E.codigo = M.estadoact)
				 ORDER BY M.fecha ASC , M.hora ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	$_SESSION['SQL_MANTENIMIENTOS'] = "";
	header("Location: mantenimientos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "")
  {
     $cont = $clase->Header("S","W"); ;  	 
	 $cont.='<table width="100%">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="images/iconos/mantenimientos.png" width="32" height="32" border="0"> </td>
				 <td width="400"> Mantenimientos <td>
				 <td width="27"> <a href="?opcion=nuevo"> <img src="images/icononuevo.png" border="0"> </a> </td>
				 <form action="?opcion=encontrar" method="POST" name="x">
				 <td> <input type="text" name="criterio" size="30" placeholder="Criterio a Buscar" tabindex="1" id="default"> </td>
				 <td> <input type="submit" value="Encontrar" class="boton"> </td> ';

	 if($_SESSION['SQL_MANTENIMIENTOS'] != "")
         $cont.='<td width="10"> <a href="?opcion=nofiltro"> <img src="images/nofiltro.png"> </a> </td>'; 

		$cont.=' </form>
    		     <td width="8"> </td>
			   </tr>	 			   
			 </table> ';	
	
	
    $vsql = $_SESSION['SQL_MANTENIMIENTOS'];
	if($vsql == "")
    	$vsql = "SELECT M.mantenimid , M.numorden , MQ.descripcion maquina , T1.nombre operario , M.fecha , M.hora , E.descripcion estado
		         FROM mantenimientos M 
				 INNER JOIN maquinaria MQ ON (M.maquinariaid = MQ.maquinariaid)
			     INNER JOIN terceros T1 ON (T1.terid = M.operariopro)
				 INNER JOIN terceros T2 ON (T2.terid = M.operariorea)
				 INNER JOIN estados E ON (E.codigo = M.estadoact)
				 ORDER BY M.fecha ASC , M.hora ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);

	 $cont.='<table width="100%">
	           <tr class="TituloTabla"> 
			     <td width="10"> </td>
			     <td width="50">  Orden </td>
				 <td width="80"> Maquinaria </td>
				 <td width="50"> Operario Programado </td>			
				 <td width="20">  Estado </td>							 				 				 
				 <td width="20" align="center">  Fecha </td>							 
				 <td width="20" align="center">  Hora </td>							 				 
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
				  <td> '.$row['numorden'].' </td>
				  <td> '.$row['maquina'].' </td>
				  <td> '.substr($row['operario'],0,25). '</td>
				  <td> '.$row['estado']. '</td>				  
				  <td align="center"> '.FormatoFecha($row['fecha']). '</td>				  
				  <td align="center"> '.$row['hora']. '</td>				  				  
				  <td> <a href="?opcion=detalles&amp;id='.$row['mantenimid'].'"> <img src="images/seleccion.png" border="0"> </td>				  
				 </tr>';
	}
	$cont.='</table>
	        <table width="100%">
	           <tr class="PieTabla"> 
			     <td width="10"> </td>
			     <td width="100"> <a href="?opcion=masregistros"> Mas Registros </a> </td>
			     <td width="150"> </td>
				 <td width="60"> <a href="#arriba"> Arriba </a> </td>
			   </tr>
			</table>';
			
    mysql_free_result($result); 
    mysql_close($conex);			  
  }
  
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  
 

  ///////////////////////////////////////////////////////////////////////    
  function FuncionesEspeciales($item,$id)
  {
  	 $cont.='<table width="100%">   <tr class="BarraDocumentos">';
			
			  if($item != 1)
			    $cont.='<td width="25%" align="center"> <a href="?opcion=actrealizadas&amp;id='.$id.'"> <img src="images/actividades.png"> Actividades Realizadas </a> </td>';
		      else
			    $cont.='<td width="25%" align="center" class="BarraDocumentosSel"> <img src="images/actividades.png"> Actividades Realizadas </td>';		
    				 
			  if($item != 2)
			    $cont.='<td width="25%" align="center"> <a href="?opcion=asignaroperario&amp;id='.$id.'"> <img src="images/terceros.png"> Asignacion Operario </a> </td>';
		      else
			    $cont.='<td width="25%" align="center" class="BarraDocumentosSel"> <img src="images/terceros.png"> Asignacion Operario </td>';		

			  if($item != 3)
			    $cont.='<td width="25%" align="center"> <a href="?opcion=cambioestado&amp;id='.$id.'"> <img src="images/iconpagado.png"> Cambio de Estado  </a> </td>';
		      else
			    $cont.='<td width="25%" align="center" class="BarraDocumentosSel"> <img src="images/iconpagado.png"> Cambio de Estado </td>';		

			  if($item != 4)
			    $cont.='<td width="25%" align="center"> <a href="?opcion=timeline&amp;id='.$id.'"> <img src="images/estadisticas.png"> Estadisticas </a> </td>';
		      else
			    $cont.='<td width="25%" align="center" class="BarraDocumentosSel"> <img src="images/estadisticas.png"> Estadisticas </td>';		

	 $cont.=' </tr> </table> ';	
     return($cont);			 
  }

  ///////////////////////////////////////////////////////////////////////    
  function FormatoFecha($fecha)
  {
    return(substr($fecha,8,2)."/".substr($fecha,5,2)."/".substr($fecha,0,4));
  }
  
?> 