<?PHP
  session_start(); 
  include("lib/Sistema.php");
  include("lib/libdocumentos.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  //////////////////////////////////////////////////////////////////////////////////
  if($opcion == "aplicarbusqueda")
  {  	  
    $ventas    = $_POST['ventas'];
    $compras   = $_POST['compras'];
    $traslados = $_POST['traslados'];		

    $tercero   = $_POST['tercero'];
    $fecdesde  = $_POST['fecdesde'];
    $fechasta  = $_POST['fechasta'];

    $bloqueada  = $_POST['bloqueada'];			
	
	/// Concatenando la Consulta SQL
	$vsql = "SELECT * FROM documentos D INNER JOIN terceros T ON (T.terid = D.terid1) WHERE 1 ";
	// Filtro de Tipos de Documento   	
	if(($ventas == "FVE")||($compras == "FCO")||($traslados == "TRB"))
	{
	   $vsql.=" AND D.tipodoc IN(";

	   if($ventas == "FVE")
         $vsql.= "'FVE',";  
	   if($compras == "FCO")
         $vsql.= "'FCO',";  
	   if($traslados == "TRB")
         $vsql.= "'TRB',";  	   
		 
	   $vsql.="'XXX') ";
	}	
	// Filtro de Tercero   
	if($tercero != "")
       $vsql.= " AND T.nombre LIKE '%".strtoupper($tercero)."%' ";  	
    // Filtro de Asentado o NO
	if($bloqueada == "BL")
       $vsql.= " AND D.fecasentado <> '0000-00-00 00:00:00' ";  	
	if($bloqueada == "NO")
       $vsql.= " AND D.fecasentado = '0000-00-00 00:00:00' ";  	

    // Filtro de Fechas
    $vsql.=" AND D.fechadoc >= '".FechaMySQL($fecdesde)." 00:00:00' AND D.fechadoc <='".FechaMySQL($fechasta)." 23:59:59' ";
	
	$vsql.= "ORDER BY D.tipodoc ASC, D.prefijo ASC, CAST(D.numero as DECIMAL) ASC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];

    $_SESSION['ORDEN_DOCUMENTOS']="";
    $_SESSION['TIPO_ORDEN_DOCUMENTOS']="";
    $_SESSION['FILTRO_DOCUMENTOS']="";	  
    $_SESSION["NUMREGISTROSXCONSULTA"] = 500;	
	$_SESSION['SQL_DOCUMENTOS'] = $vsql;	
	header("Location: documentos.php");
  }
 
  //////////////////////////////////////////////////////////////////////////////////  
  if($opcion == "busqavanzada")
  {  	  
	 $mesactual = date("m");
	 if($mesactual <= 3)
	   $mesactual=1;
	   
	 $cont='<form action="?opcion=aplicarbusqueda" method="POST">
			  <table width="600" align="center">
	            <tr class="CabezoteTabla" valign="top"> 
				  <td align="center"> <b> Busqueda Avanzada de Documentos </b> </td>
			 	</tr>
			  </table>
			  
			  <table width="600" align="center"> 
			    <tr class="BarraDocumentos"> 
				 <td width="20"> &nbsp; </td>
			     <td width="160"> Tipo Documento : </td>
			     <td>
				    <br>
					<input type="checkbox" name="ventas" value="FVE"> Venta 
 				    <input type="checkbox" name="compras" value="FCO"> Compra 
					<input type="checkbox" name="traslados" value="TRB"> Traslado
					<input type="checkbox" name="tipodoc" value="AJI"> Ajuste Inv
					<input type="checkbox" name="tipodoc" value="CCO"> Consumo					
					<br>
				    <input type="checkbox" name="tipodoc" value="FVE"> Cta Cobrar 
 				    <input type="checkbox" name="tipodoc" value="FCO"> Recibo Caja 
					<input type="checkbox" name="tipodoc" value="TRA"> Cta Pagar
					<input type="checkbox" name="tipodoc" value="AJI"> Comp Egreso
                    <br><br>
			 	 </td>				 
			    </tr>
			    <tr class="BarraDocumentos"> 
				 <td width="20"> &nbsp; </td>
			     <td width="160"> Datos del Beneficiario : </td>
			     <td>
				    <input type="text" name="tercero" size="30">
			 	 </td>				 
			  </tr>			  
  		      <tr class="BarraDocumentos"> 
				 <td width="20"> &nbsp; </td>
			     <td width="160"> Fecha de Emision : </td>
			     <td>
				    Desde <input type="text" name="fecdesde" value="01'."/0".$mesactual."/".date("Y").'" size="10">
	                Hasta <input type="text" name="fechasta" value="'.date("d/m/Y").'" size="10">
			 	 </td>				 
			  </tr>			  
  		      <tr class="BarraDocumentos"> 
				 <td width="20"> &nbsp; </td>
			     <td width="160"> Valor Total : </td>
			     <td>
				    Desde <input type="text" name="totaldesde" value="0" size="10">
	                Hasta <input type="text" name="totalhasta" value="0" size="10">
			 	 </td>				 
			  </tr>
  		      <tr class="BarraDocumentos"> 
				 <td width="20"> &nbsp; </td>
			     <td width="160"> Forma de Pago </td>
 			     <td>
                     <input type="checkbox" name="efectivo" value="EF"> Efectivo
                     <input type="checkbox" name="credito" value="CR"> Cr&eacute;dito
                     <input type="checkbox" name="tarjetas" value="TD"> Tarjetas CR/DB
                     <input type="checkbox" name="otrasfp" value="99"> Otras FP					 					 					 
			 	 </td>				 
			  </tr>
  		      <tr class="BarraDocumentos"> 
				 <td width="20"> &nbsp; </td>
			     <td width="160"> Estado Documento : </td>
 			     <td>
                     <input type="radio" name="bloqueada" value="TO" checked> Todos 
					 <input type="radio" name="bloqueada" value="BL"> Solo Bloqueados
					 <input type="radio" name="bloqueada" value="NO"> Solo NO Bloqueados 
			 	 </td>				 
			  </tr>
  		      <tr class="BarraDocumentos"> 
				 <td width="20"> &nbsp; </td>
			     <td width="160"> &nbsp;</td>
			     <td> &nbsp; </td>				 
			  </tr>
			 </table>
		     <table width="600" align="center">
	            <tr class="CabezoteTabla" valign="top"> 
				  <td align="center"> <input type="submit" value=" Encontrar Documentos " class="Boton"> </td>
			 	</tr>
			  </table>
              </form>';
	echo $cont;
	exit();		  
  }

//////////////////////////////////////////////////////////////////////////////////  

  if($opcion == "opciones")
  {  	  
    $docuid  = $_GET['id'];
	$cont='<table width="400">
	           <tr class="CabezoteTabla"> 
				 <td align="center"> <b> Opciones de Documento </b> <td>
			   </tr> <tr class="BarraDocumentos"> 
			     <td align="center"> <a href="ventas.php?opcion=preanularventa&amp;id='.$docuid.'" rel="facebox">Anular Documento</a> </td>
			   </tr> <tr class="BarraDocumentos"> 
			     <td align="center"> <a href="#" onclick="window.open(\'ventas.php?opcion=imprimirventa&amp;id='.$docuid.'\',\'ImpFV\',\'width=600,height=650,left=100,top=100\')">
				     <img src="images/iconpdf.png" border="0"> Visualizar Documento </a> </td>  
			   </tr> <tr class="BarraDocumentos"> 
                  <td align="center">   
					 <a href="#" onclick="window.open(\'facturapida.php?opcion=ordenesmedicos&amp;id='.$docuid.'\',\'ImpTicMed\',\'width=300,height=240,left=200,top=100\')">
				     <img src="images/iconoimprimir.png" border="0"> Imprimir Tickets Medico </a> </td>
			   </tr>
			 </table>';
	 echo $cont;		  
 	 exit(0);		
  }

  
  /////////////////////////////////////////    
  if($opcion == "encontrar")
  {
    $criterio = $_POST['criterio'];
    if($criterio != "")
	{
      $criterio = strtoupper($_POST['criterio']);

	  if((strpos($criterio, '/') != "")&&(strpos($criterio, '/', 1) != ""))
	  {
	     $ncriterio = substr($criterio,6,4)."-".substr($criterio,3,2)."-".substr($criterio,0,2);
	     $criterio = $ncriterio;	 
	  }
	  
      $_SESSION["NUMREGISTROSXCONSULTA"] = 100;
	  $vsql = "SELECT * FROM documentos D INNER JOIN terceros T ON (T.terid = D.terid1) 
	           WHERE D.tipodoc like '%".$criterio."%' OR D.numero like '%".$criterio."%' OR T.nombre like '%".$criterio."%' OR D.fechadoc like '".$criterio."%'
		  	   ORDER BY D.tipodoc ASC , D.prefijo ASC , CAST(D.numero as DECIMAL) ASC 
			   Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    }
	else
	{
      $_SESSION['ORDEN_DOCUMENTOS']="";
  	  $_SESSION['TIPO_ORDEN_DOCUMENTOS']="";
      $_SESSION['FILTRO_DOCUMENTOS']="";	  
      $_SESSION["NUMREGISTROSXCONSULTA"] = 100;	
	  $vsql = "SELECT * FROM documentos D INNER JOIN terceros T ON (T.terid = D.terid1) 
		  	   ORDER BY D.docuid DESC 
			   Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	}

	$_SESSION['SQL_DOCUMENTOS'] = $vsql;
	header("Location: documentos.php");
  }

  /////////////////////////////////////////    
  if($opcion == "soloventas")
  {
    $vsql = "SELECT * FROM documentos D INNER JOIN terceros T ON (T.terid = D.terid1) 
	         WHERE D.tipodoc = 'FVE' ORDER BY D.prefijo ASC , CAST(D.numero as DECIMAL) ASC
			 Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['FILTRO_DOCUMENTOS'] = "D.tipodoc = 'FVE'";
    $_SESSION['SQL_DOCUMENTOS'] = $vsql;
	header("Location: documentos.php");
  }

  /////////////////////////////////////////    
  if($opcion == "soloprestaciones")
  {
    $vsql = "SELECT * FROM documentos D INNER JOIN terceros T ON (T.terid = D.terid1) 
	         WHERE D.tipodoc = 'PSE' ORDER BY D.prefijo ASC , CAST(D.numero as DECIMAL) ASC
			 Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['FILTRO_DOCUMENTOS'] = "D.tipodoc = 'PSE'";			 
    $_SESSION['SQL_DOCUMENTOS'] = $vsql;
	header("Location: documentos.php");
  }

  /////////////////////////////////////////    
  if($opcion == "soloordenes")
  {
    $vsql = "SELECT * FROM documentos D INNER JOIN terceros T ON (T.terid = D.terid1) 
	         WHERE D.tipodoc = 'RSA' ORDER BY D.prefijo ASC , CAST(D.numero as DECIMAL) ASC
			 Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
    $_SESSION['FILTRO_DOCUMENTOS'] = "D.tipodoc = 'RSA'";			 
    $_SESSION['SQL_DOCUMENTOS'] = $vsql;
	header("Location: documentos.php");
  }

  /////////////////////////////////////////    
  if($opcion == "ordenar")
  {
    $ordenarpor = $_GET['by'];
    $vsql = "SELECT * FROM documentos D INNER JOIN terceros T ON (T.terid = D.terid1) ";
    
	if(($_SESSION['FILTRO_DOCUMENTOS'] != "")&&($_SESSION['FILTRO_DOCUMENTOS'] != "MA"))
       $vsql.=' WHERE '.$_SESSION['FILTRO_DOCUMENTOS'].' ';
	
	if($ordenarpor == "TIPODOC")
	{    	
	   if($_SESSION['ORDEN_DOCUMENTOS'] != "TIPODOC"){
  	      $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		  $vsql.=" ORDER BY D.tipodoc ASC, D.prefijo , CAST(D.numero as DECIMAL) ASC";    
       }
	   else
	   {
	     if(($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "ASC")||($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "")){
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "DESC";		   			   
		   $vsql.=" ORDER BY D.tipodoc DESC, D.prefijo , CAST(D.numero as DECIMAL) ASC";   
		 }   
         else{
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		   $vsql.=" ORDER BY D.tipodoc ASC, D.prefijo , CAST(D.numero as DECIMAL) ASC";   
		 }   
       }	    
  	   $_SESSION['ORDEN_DOCUMENTOS'] = "TIPODOC";
    }
	
	if($ordenarpor == "PREFIJO")    	
	{    	
	   if($_SESSION['ORDEN_DOCUMENTOS'] != "PREFIJO"){
  	      $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		  $vsql.=" ORDER BY D.prefijo ASC, CAST(D.numero as DECIMAL) ASC";    
       }
	   else
	   {
	     if(($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "ASC")||($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "")){
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "DESC";		   			   
		   $vsql.=" ORDER BY D.prefijo DESC, CAST(D.numero as DECIMAL) ASC";   
		 }   
         else{
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		   $vsql.=" ORDER BY D.prefijo ASC, CAST(D.numero as DECIMAL) ASC";   
		 }   
       }	    
  	   $_SESSION['ORDEN_DOCUMENTOS'] = "PREFIJO";
    }

	if($ordenarpor == "NUMERO")    	
	{    	
	   if($_SESSION['ORDEN_DOCUMENTOS'] != "NUMERO"){
  	      $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		  $vsql.=" ORDER BY CAST(D.numero as DECIMAL) ASC";    
       }
	   else
	   {
	     if(($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "ASC")||($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "")){
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "DESC";		   			   
		   $vsql.=" ORDER BY CAST(D.numero as DECIMAL) DESC";   
		 }   
         else{
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		   $vsql.=" ORDER BY CAST(D.numero as DECIMAL) ASC";   
		 }   
       }	    
  	   $_SESSION['ORDEN_DOCUMENTOS'] = "NUMERO";
    }

	if($ordenarpor == "FECHA")    	
	{    	
	   if($_SESSION['ORDEN_DOCUMENTOS'] != "FECHA"){
  	      $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		  $vsql.=" ORDER BY D.fechadoc ASC , D.tipodoc ASC , D.prefijo ASC , CAST(D.numero as DECIMAL) ASC";    
       }
	   else
	   {
	     if(($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "ASC")||($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "")){
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "DESC";		   			   
		   $vsql.=" ORDER BY D.fechadoc DESC , D.tipodoc ASC , D.prefijo ASC , CAST(D.numero as DECIMAL) ASC";   
		 }   
         else{
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		   $vsql.=" ORDER BY D.fechadoc ASC , D.tipodoc ASC , D.prefijo ASC , CAST(D.numero as DECIMAL) ASC";   
		 }   
       }	    
  	   $_SESSION['ORDEN_DOCUMENTOS'] = "FECHA";
    }

	if($ordenarpor == "TERCERO")    	
    {
	   if($_SESSION['ORDEN_DOCUMENTOS'] != "TERCERO"){
  	      $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		  $vsql.=" ORDER BY D.terid1 ASC , D.tipodoc ASC , D.prefijo ASC , CAST(D.numero as DECIMAL) ASC";    
       }
	   else
	   {
	     if(($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "ASC")||($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "")){
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "DESC";		   			   
		   $vsql.=" ORDER BY D.terid1 DESC , D.tipodoc ASC , D.prefijo ASC , CAST(D.numero as DECIMAL) ASC";   
		 }   
         else{
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		   $vsql.=" ORDER BY D.terid1 ASC , D.tipodoc ASC , D.prefijo ASC , CAST(D.numero as DECIMAL) ASC";   
		 }   
       }	    
  	   $_SESSION['ORDEN_DOCUMENTOS'] = "TERCERO";
    }
	
	if($ordenarpor == "MONTO")    	
     {
	   if($_SESSION['ORDEN_DOCUMENTOS'] != "MONTO"){
  	      $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		  $vsql.=" ORDER BY D.total ASC";    
       }
	   else
	   {
	     if(($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "ASC")||($_SESSION["TIPO_ORDEN_DOCUMENTOS"] == "")){
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "DESC";		   			   
		   $vsql.=" ORDER BY D.total DESC";   
		 }   
         else{
		   $_SESSION["TIPO_ORDEN_DOCUMENTOS"] = "ASC";		   			   
		   $vsql.=" ORDER BY D.total ASC";   
		 }   
       }	    
  	   $_SESSION['ORDEN_DOCUMENTOS'] = "MONTO";
    }
	
    $vsql.= " Limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	$_SESSION['SQL_DOCUMENTOS'] = $vsql;
	header("Location: documentos.php");
  }
      
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    

  if($opcion == "")
  {
    $cont = $clase->Header("N","W"); 
	
	 //Barras Superiores de Documentos e Insercion y Acciones Masivas
	 $cont.= BarraDocumentos();	 
	 $cont.= MenuAccesoLateral();	 
	 $cont.= SegundaBarraDocumentos();
	 
    // Busqueda de Documentos
    $vsql = $_SESSION['SQL_DOCUMENTOS'];

	if($vsql == "")
	{
 	    $_SESSION["NUMREGISTROSXCONSULTA"] = 500;
    	$vsql = "SELECT * FROM documentos D INNER JOIN terceros T ON (D.terid1 = T.terid) 
		         WHERE tipodoc = 'FVE' ORDER BY tipodoc ASC , prefijo ASC , numero DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];
	}	
	
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
    
	if(mysql_num_rows($result) == 0)
	{
    	$clase->Aviso(2,"No se encontraron Documentos con este criterio de Busqueda");  			  			   		
    	$vsql = "SELECT * FROM documentos D INNER JOIN terceros T ON (D.terid1 = T.terid) 
		         WHERE tipodoc = 'FVE' ORDER BY tipodoc ASC , prefijo ASC , numero DESC limit 0,".$_SESSION["NUMREGISTROSXCONSULTA"];      
        $result = mysql_query($vsql,$conex);
	}
	
	// Titulos de Documentos
	$cont.='<table width="900">
	     <tr valign="top">
		  <td>   			
			<div style="overflow:auto; height:600px;width:910px;">  			  
			  <table width="892">
	           <tr class="TituloTabla"> 
			     <td width="8"> </td>
				 <td width="5"> </td>
				 <td width="10"> </td>
				 <td width="10"> </td>
			     <td width="10" align="center">'; 
                 if($_SESSION["ORDEN_DOCUMENTOS"] == "TIPODOC")
				    $cont.="*";					
    $cont.='     <a href="?opcion=ordenar&amp;by=TIPODOC"><font color="#FFFFFF">Tipo</a></td>
			     <td width="10" align="center">';
                 if($_SESSION["ORDEN_DOCUMENTOS"] == "PREFIJO")
				    $cont.="*";								     
    $cont.='     <a href="?opcion=ordenar&by=PREFIJO"><font color="#FFFFFF">Pref</a></td>
			     <td width="10" align="center">';
                 if($_SESSION["ORDEN_DOCUMENTOS"] == "NUMERO")
				    $cont.="*";								     
    $cont.='     <a href="?opcion=ordenar&by=NUMERO"><font color="#FFFFFF">Numero</a></td>				 				 
                 <td width="10" align="center">';
                 if($_SESSION["ORDEN_DOCUMENTOS"] == "FECHA")
				    $cont.="*";								     
    $cont.='     <a href="?opcion=ordenar&by=FECHA"><font color="#FFFFFF">Fecha</a></td>
                 <td width="55" align="center">';
                 if($_SESSION["ORDEN_DOCUMENTOS"] == "HORA")
				    $cont.="*";
    $cont.='     <a href="?opcion=ordenar&by=HORA"> <font color="#FFFFFF">Hora </a> </td>				 
				 <td width="150">';
                 if($_SESSION["ORDEN_DOCUMENTOS"] == "TERCERO")
				    $cont.="*";				 
    $cont.='     <a href="?opcion=ordenar&by=TERCERO"><font color="#FFFFFF">Tercero / Beneficiario</a></td> 
				 <td width="25" align="right">';
                 if($_SESSION["ORDEN_DOCUMENTOS"] == "MONTO")
				    $cont.="*";				 
    $cont.='     <a href="?opcion=ordenar&by=MONTO"><font color="#FFFFFF">Valor</a></td>			
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
		          
		 $cont.=' <td width="8"> </td>
			      <td width="5" align="left"> <input type="checkbox" name="seleccionar"> </td>';
 
         $cont.= IconoAsentarDoc($row['fecanulado'],$row['fecasentado'],$row['tipodoc'],$row['docuid']);
		 
	     $cont.=' <td width="10" align="left">';  
         $cont.= IconoImprimirDoc($row['fecanulado'],$row['fecasentado'],$row['tipodoc'],$row['docuid']);
		 $cont.=' <img src="images/iconoimprimir.png" border="0"> </a> </td>				  
				  <td width="10" align="center"> '.$row['tipodoc'].'</td>
				  <td width="10" align="center"> '.$row['prefijo'].'</td>
				  <td width="10" align="center"> '.$row['numero'].'</td>
				  <td width="10" align="center"> '.FormatoFecha($row['fechadoc']).'</td>
				  <td width="55" align="center"> '.FormatoHora($row['fechadoc']).'</td>				  				  
				  <td width="150" align="left"> '.substr($row['nombre'],0,25).' </td>				  
				  <td width="25" align="right"> '.FormatoNumero($row['total']).' </td>
    		      <td width="20"> </td>';
				  
         if($row['fecasentado'] == "0000-00-00 00:00:00")
		 { 
		    if(($row['tipodoc'] == "FVE")||($row['tipodoc'] == "PSE")||($row['tipodoc'] == "RSA"))
  			    $cont.='<td width="10" align="left"> <a href="ventas.php?opcion=editarventa&amp;id='.$row['docuid'].'"> <img src="images/reversado.png" border="0"> </a> </td>';
		    if($row['tipodoc'] == "FCO")
   			    $cont.='<td width="10" align="left"> <a href="compras.php?opcion=editarcompra&amp;id='.$row['docuid'].'"> <img src="images/reversado.png" border="0"> </a> </td>';
            if($row['tipodoc'] == "TRB")
   			    $cont.='<td width="10" align="left"> <a href="traslados.php?opcion=editartraslado&amp;id='.$row['docuid'].'"> <img src="images/reversado.png" border="0"> </a> </td>';				
         }				
         else
		 {
		    if($row['tipodoc'] == "PSE")
		      $cont.='<td> <a href="facturapida.php?opcion=menups&docuid='.$row['docuid'].'"><img src="images/iconoaccionesm.png" border="0"></a></td>';						            if($row['tipodoc'] == "FVE")
		      $cont.='<td> <a href="facturapida.php?opcion=menufv&docuid='.$row['docuid'].'" rel="facebox"><img src="images/iconoaccionesm.png" border="0"></a></td>';				  
		 }
		$cont.='</tr>';		 
	}
	$cont.='</table> 
	</div>
	
	</td>
	</tr>
	</table>
	
	</td>
	</tr>
	</table>';
			
    mysql_free_result($result); 
    mysql_close($conex);			  
  }
  
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  
  
?>