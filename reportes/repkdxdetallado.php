<?PHP
  session_start(); 
  include("../lib/Sistema.php");
  include("../lib/libdocumentos.php");  
  include("configreportes.php");  

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
  $ruta="../";
    
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];
 
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  if($opcion == "excel")
  {
	$ordalf = $_POST['ordalf'];
	
	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=archivo.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
    
	$vsql="SELECT * FROM terceros ";
	if($ordalf == "S")
	  $vsql.= "ORDER BY nombre ASC";
	else
      $vsql.= "ORDER BY codigo ASC";	  

	echo'<table border=1>
		   <tr bgcolor="#CCCCCC">
             <th>Codigo</th>
	         <th>Nit</th>
			 <th>Nombre</th>
			 <th>Direccion</th>
			 <th>Telefono</th>
			 <th>Celular</th>
			 <th>Email</th>			 			 
           </tr>';

    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);
	while($row = mysql_fetch_array($result))
	{
      echo' <tr>
              <td>'.$row['codigo'].' </td>
              <td>'.$row['nit'].' </td>
              <td>'.$row['nombre'].' </td>			  
              <td>'.$row['direccion'].' </td>			  
              <td>'.$row['telefono'].' </td>			  
              <td>'.$row['celular'].' </td>			  
              <td>'.$row['email'].' </td>			  			  			  			  
            </tr>';
	}
	
	echo'</table>';	  
  }

  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  if($opcion == "ver")
  {
	$codproducto = $_POST['codproducto'];
	$codbodega   = $_POST['codbodega'];
	$desde       = $_POST['desde'];
	$hasta       = $_POST['hasta'];		
	
	$nomproducto = $clase->BDLockup($codproducto,"item","codigo","descripcion");
	$nombodega   = $clase->BDLockup($codbodega,"bodegas","codigo","descripcion");
		
    // Calculo el Saldo Inicial de Inventario del Producto a Fecha de inicio
	$vsql = "SELECT D.tipodoc , SUM(DD.cantidad) canti FROM documentos D 
             INNER JOIN dedocumentos DD ON (D.docuid = DD.docuid)INNER JOIN item I ON (I.itemid = DD.itemid)
             INNER JOIN bodegas B ON (B.bodegaid = DD.bodegaid) WHERE D.fecasentado is not NULL AND 
			 (D.tipodoc = 'FVE' OR D.tipodoc = 'RSA' OR D.tipodoc = 'FCO' OR D.tipodoc = 'CON') AND 
			 I.codigo = '".$codproducto."' AND B.codigo = '".$codbodega."' AND D.fechadoc <= '".FechaMySQL($desde)."'
             GROUP BY 1";

    $conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex);	

	$entradas = 0;
	$salidas  = 0;		

	while($row = mysql_fetch_array($result))
	{
       $cantidad = $row['canti'];
	   
	   if(($row['tipodoc'] == "FCO")||($row['tipodoc'] == "REN"))    
          $entradas = $entradas + $cantidad;              
       
       if(($row['tipodoc'] == "FVE")||($row['tipodoc'] == "CON"))           
          $salidas = $salidas + $cantidad;	 
    }	
	
	$saldoinicial = $entradas - $salidas;
	
	/// Armo la primera linea para Mostrar
	$contini.= '<tr class="TablaDocsImPar">
                  <td width="5" align="center"> &nbsp; </td>
      		      <td width="50" align="center"> '.$desde.'</td>				 
                  <td width="80" align="center"> SALDO INICIAL</B>		
		          <td width="40" align="center"> '.$nombodega.'</font></td>			  
                  <td width="20" align="right">  '.$saldoinicial.'</font></td>			  			  			  			  
                  <td width="40" align="right">  '.$saldoinicial.' </td>			  			  			  			  				 
				    <td width="15" align="center"> <img src="../images/instinpreactivada.png" border="0"> </td>
  	              <td width="10" align="center"> &nbsp; </td>  	              
                </tr>';
	
	// Calculo los Movimientos del Reporte dentro de las fechas señaladas
	$vsql = "SELECT D.docuid , D.tipodoc, D.prefijo, D.numero, D.fechadoc, B.descripcion, SUM( DD.cantidad ) cantidad
		     FROM documentos D
			 INNER JOIN dedocumentos DD ON ( D.docuid = DD.docuid ) 
			 INNER JOIN bodegas B ON ( DD.bodegaid = B.bodegaid ) 
			 INNER JOIN productos P ON ( DD.itemid = P.itemid ) 
			 INNER JOIN item I ON ( P.itemid = I.itemid )  
			 WHERE I.codigo = '".$codproducto."' AND D.fechadoc >= '".FechaMySQL($desde)."' AND D.fechadoc <= '".FechaMySQL($hasta)."'";
	
	if($codbodega != "")
	    $vsql.= " AND B.codigo = '".$codbodega."' ";
	
	$vsql.= " AND D.fecasentado <> '0000-00-00 00:00:00' AND D.fecanulado = '0000-00-00 00:00:00' 
	          GROUP BY 1 , 2, 3, 4, 5 ORDER BY fechadoc , tipodoc ASC";

    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Kardex Detallado del Producto");	
		
	$cont.='<center><b>'.$nomproducto.'</b><br><br></center>
	        <div style="overflow:auto; height:570px;width:850px;">
	        <table width="800">
		    <tr class="TablaDocsPar"> 
             <th width="5"> &nbsp; </th>			
             <th width="50" align="center"> Fecha </th>
	         <th width="80" align="center"> Documento </th>
			 <th width="30" align="center"> Bodega</th>
			 <th width="20" align="right">  Cantidad </th>			 			 
			 <th width="40" align="right">  Saldo </th>						 
             <th width="15"> &nbsp; </th>					  			 			 
             <th width="10"> &nbsp; </th>             
           </tr>';
    
	$cont.= $contini;
	       

    $i=0;
    $result = mysql_query($vsql,$conex);
	
	$saldo = $saldoinicial;
	
	while($row = mysql_fetch_array($result))
	{

	    if(($row['tipodoc'] == "FCO")||($row['tipodoc'] == "REN")||($row['tipodoc'] == "AIN"))
	      $saldo = $saldo + $row['cantidad'];
	  
	    if(($row['tipodoc'] == "FVE")||($row['tipodoc'] == "RSA")||($row['tipodoc'] == "CON")||($row['tipodoc'] == "PVE"))
	      $saldo = $saldo - $row['cantidad'];

        if($i%2 == 0)
	      $cont.= '<tr class="TablaDocsPar">';
	    else
	      $cont.= '<tr class="TablaDocsImPar">';

        $cont.= '<td width="5" align="center"> &nbsp; </td>
		         <td width="50" align="center">'.FormatoFecha($row['fechadoc']).' </td>				 
                 <td width="80" align="center">';
		
		if($row['tipodoc'] == "FVE")	 
		{	
			$cont.='<a href="#" OnClick="window.open(\'../ventas.php?opcion=imprimirventa&amp;id='.$row['docuid'].'\',\'ImpFV\',\'width=600,height=650,left=100,top=100\')">                               '.                  $row['tipodoc'].' '.$row['prefijo'].' '.$row['numero'].'</font></td>
					<td width="40" align="center">'.$row['descripcion'].'</font></td>
					<td width="20" align="right">'.$row['cantidad'].'</font></td>			  			  			  			  
                    <td width="40" align="right">'.$saldo.' </td>			  			  			  			  				 
				    <td width="15" align="center"> <img src="../images/instinactiva.png" border="0"> </td>
					<td width="10" align="center"> &nbsp; </td>';
		}		

		if($row['tipodoc'] == "CON")	 
		{	

			$cont.='<a href="#" OnClick="window.open(\'../consumos.php?opcion=imprimirconsumo&amp;id='.$row['docuid'].'\',\'ImpFV\',\'width=600,height=650,left=100,top=100\')"> '.             $row['tipodoc'].' '.$row['prefijo'].' '.$row['numero'].'</font></td>
					<td width="40" align="center">'.$row['descripcion'].'</font></td>
					<td width="20" align="right">'.$row['cantidad'].'</font></td>			  			  			  			  
                    <td width="40" align="right">'.$saldo.' </td>			  			  			  			  				 
				    <td width="15" align="center"> <img src="../images/instinactiva.png" border="0"> </td>
					<td width="10" align="center"> &nbsp; </td>';
	}		

		if($row['tipodoc'] == "FCO")	 
		{	

			$cont.='<a href="#" OnClick="window.open(\'../compras.php?opcion=imprimircompra&amp;id='.$row['docuid'].'\',\'ImpFV\',\'width=600,height=650,left=100,top=100\')"> '.               $row['tipodoc'].' '.$row['prefijo'].' '.$row['numero'].'</font></td>
					<td width="40" align="center">'.$row['descripcion'].'</font></td>
					<td width="20" align="right">'.$row['cantidad'].'</font></td>			  			  			  			  
                    <td width="40" align="right">'.$saldo.' </td>			  			  			  			  				 
				    <td width="15" align="center"> <img src="../images/instactiva.png" border="0"> </td>
					<td width="10" align="center"> &nbsp; </td>';
		}		

       $cont.= '</tr>';		 
	   $ultimafecha = FormatoFecha($row['fechadoc']);
	   $i++;		
	}
	
    $cont.='<tr class="TablaDocsPar">
	        <td width="5" align="center"> &nbsp; </td>
   	        <td width="50" align="center"> <b>'.$ultimafecha.'</b> </td>				 
            <td width="80" align="center">  </td>
 	        <td width="40" align="center"> </td>
     		<td width="20" align="right">   <b> SALDO : </b>  </td>			  			  			  			  
            <td width="40" align="right">   <b>'.$saldo.'</b></td>			  			  			  			  				 
  			<td width="15" align="center"> <img src="../images/instinpreactivada.png" border="0"> </td>
    		<td width="10" align="center"> &nbsp; </td></tr>';
	$cont.='</table><br><br><br><br></div>';	  
  }
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
    $cont = $clase->HeaderReportes();
    $cont.= EncabezadoReporte("Kardex Detallado de Productos");	

    $cont.='<br><br><table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Kardex Detallado de Productos <td>
			  </tr>
			 </table>
			 
			 <form action="?opcion=ver" method="Post" name="x" onsubmit="return ValidarForm(this);">
			 
			 <script type="text/javascript">
			 <!--
				function ValidarForm(formulario) {
					if(formulario.codproducto.value.length==0) { 
						formulario.codproducto.focus();    // Damos el foco al control
						alert(\'Debe Elegir un Producto\'); //Mostramos el mensaje
						return false; //devolvemos el foco
					}
					return true; //Si ha llegado hasta aquí, es que todo es correcto
				}
			 -->	
			 </script>			 
			 
			 <table width="600">
	           <tr class="BarraDocumentos"> 
			     <td width="50"> </td>
			     <td width="70"> Producto  </td>
			     <td width="200"> 

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="../lib/ajax_framework12.js"></script>
<style type="text/css">
#search-wrap12 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res12{width:150px; border:solid 1px #DEDEDE; display:none;}
#res12 ul, #res4 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res12 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res12 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res12 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res12 li a:hover{background:#FFFFFF;}
#res12 ul {padding:4px;}
</style>
<div id="search-wrap12">
<input name="codproducto" id="search-q12" type="text" onkeyup="javascript:autosuggest12();" maxlength="12" size="10" tabindex="5"/>
<div id="res12"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->


				 </td>				 
				 <td width="100"> </td>
			     <td width="70"> Bodega  </td>
			     <td width="200"> '.$clase->CrearCombo("codbodega","bodegas","descripcion","codigo","PRE","N").' </td>				 
				 <td width="50"> </td>
			  </tr>
              <tr class="BarraDocumentos"> 
			     <td width="50"> </td>
			     <td width="70"> Desde :  </td>
			     <td width="200"> 		 				 
				  <input type="text" name="desde" size="10" value="'.date("d/m/Y").'" id="desde" onClick="popUpCalendar(this, x.desde,\'dd/mm/yyyy\');">
			      </td>				 
			     <td width="100"> </td>
			     <td width="70"> Hasta :  </td>
			     <td width="200"> 
                   <input type="text" name="hasta" size="10" value="'.date("d/m/Y").'" id="hasta" onClick="popUpCalendar(this, x.hasta,\'dd/mm/yyyy\');">
				 </td>				 
				 <td width="50"> </td>
			  </tr>
			 </table>
			 <br>
			  <table width="600">
	           <tr> 
			     <td align="center"> <input type="submit" value="Ver Reporte" tabindex="4"> </td>
			   </tr>
			  </table>';
  }
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  echo $cont;  

?> 