<?PHP
  session_start(); 
  include("lib/Sistema.php");
  include("lib/libdocumentos.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];

  /////////////////////////////////////////  
  if($opcion == "imprimircompra")
  {  	  
	 $docuid  = $_GET['id'];
	 $vsql = "SELECT * FROM v_compras WHERE docuid=".$docuid;	 
	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     
	 $detalles = '<table>
	               <tr class="TablaDocsPar"> 
	                <td align="center" width="40"> <b> Cant </b> </td>
					<td width="250" align="left"> <b> Producto </b></td>
					<td width="100" align="right"> <b> Vlr Un </b> </td>
					<td width="100" align="right"> <b> Vlr Par </b> </td>
				   </tr>';
	 $i=0; 
	 while($row = mysql_fetch_array($result))
     { 
	  $cont='<div id="principal">
	         <br><br>
			 <table width="400">
	           <tr> <td align="center"> <b> Factura de Compra </b> </td> </tr>
	           <tr> <td align="center"> N.I.T. '.$_SESSION["G_NITEMP"].' </td> </tr>			   
	           <tr> <td align="center"> '.$_SESSION["G_NOMBREEMP"].' </td> </tr>				 
	         </table>
			 <br>
			 <table>
		       <tr> <td width="70"> Factura No.  </td> 
			        <td> <b> '.$row['prefijo'].'&nbsp;&nbsp;'.$row['numero'].'</b> </td> </tr>	 
	           <tr> <td> Fecha : </td>
			        <td> '.FormatoFecha($row['fechadoc']).' </td> </tr>	 			   
	           <tr> <td> Proveedor : </td>
			        <td> '.$row['nomcliente'].' </td> </tr>	 			   			   
	           <tr> <td> Cobrador : </td>
                    <td> '.$row['nomvendedor'].' </td> </tr>	 			   			   			   
			 </table><br>';

       // Sello de Anulado
	   if($row['fecanulado'] != "0000-00-00 00:00:00")
	   {
	       $cont.='<DIV STYLE="position:absolute; top:50px; left:50px; width:200px; height:200px; visibility:visible">
                   <img src="images/selloanulado.png" boder="0"> </DIV>';		
       }

       if($i%2 == 0)
	      $detalles.= '<tr class="TablaDocsImPar">';
	   else
	      $detalles.= '<tr class="TablaDocsPar">';
	   
	   $detalles.= '  <td align="center"> '.$row['cantidad'].' </td>
					  <td align="left"> '.substr($row['nomproducto'],0,25).' </td>
					  <td align="right"> '.FormatoNumero($row['valunitario']).' </td>
					  <td align="right"> '.FormatoNumero($row['valparcial']).' </td>
					</tr>';
       $pie ='<table>
	           <tr> 
			        <td width="255"> &nbsp; </td> 
			        <td width="90" align="right" class="TablaDocsPar"> <b>Base :</b> </td> 
			        <td align="right" class="TablaDocsPar"> <b> '.FormatoNumero($row['base']).' </b> </td> 
			   </tr>	 
	           <tr> 
			        <td width="255"> &nbsp; </td> 
			        <td width="90" align="right" class="TablaDocsPar"> <b> IVA :</b> </td> 
			        <td align="right" class="TablaDocsPar"> <b> '.FormatoNumero($row['iva']).' </b> </td> 
			   </tr>	 
	           <tr> 
			        <td width="255"> &nbsp; </td> 
			        <td width="90" align="right" class="TablaDocsPar"> <b> Total :</b> </td> 
			        <td align="right" class="TablaDocsPar"> <b> '.FormatoNumero($row['total']).' </b> </td> 
			   </tr>	 
			 </table><br><br>
            </div>';
		$i++;			
	 }
	 $detalles.='</table>';

     $encabezado = $clase->HeaderImpresion();
 	 $cont.= $detalles;		 
	 echo $encabezado.$cont.$pie;
     exit();   
  }

  /////////////////////////////////////////  
  if($opcion == "analisisexistencias")
  {  	  
	 $docuid  = $_GET['id'];
	 $vsql = "SELECT DISTINCT I.descripcion nomprod, B.descripcion nombod, E.existencia , SUM(DD.cantidad) cant
			  FROM existencias E INNER JOIN dedocumentos DD ON ( DD.itemid = E.itemid ) 
			  INNER JOIN bodegas B ON (B.bodegaid = DD.bodegaid)
			  INNER JOIN item I ON (DD.itemid = I.itemid) WHERE  E.bodegaid = DD.bodegaid AND DD.docuid =".$docuid." GROUP BY 1,2,3";

	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
	 $cont = '<center> <h3> Analisis de Existencias </h3>
	          <table width="600">
	           <tr class="TablaDocsPar">
			    <td width="15">&nbsp;</td>
			    <td width="180"> <b> Producto </b> </td>
				<td width="100"> <b> Bodega </b> </td>
				<td> <b> En Bodega </b> </td>
				<td> <b> Cant Comprada </b> </td>
				<td> <b> Saldo </b> </td>	
			    <td width="20">&nbsp;</td>							
			   </tr>';
	 $i=0; 		   	
     while($row = mysql_fetch_array($result))
     { 
       $saldo = $row['existencia'] - $row['cant'];
	   
	   if($i%2==0)
	     $cont.= '<tr class="TablaDocsImPar">';
	   else 
	     $cont.= '<tr class="TablaDocsPar">';	   
       
	   $cont.='<td> &nbsp; </td>
	           <td> '.$row['nomprod'].' </td>
       	       <td> '.$row['nombod'].' </td>
       	       <td align="center"> '.$row['existencia'].' </td>			   
       	       <td align="center"> '.$row['cant'].' </td>
       	       <td align="center"> '.$saldo.' </td>';

	   if($saldo >= 0)
	   		$cont.='<td align="center"> <img src="images/instactiva.png" border="0"> </td>';   			   
	   else
	   		$cont.='<td align="center"> <img src="images/instinactiva.png" border="0"> </td>';   			   
				   		
   	  $cont.='</tr>';
	  $i++;
	}		   			   			   
	$cont.='</table><br><br>';
	   
	echo $cont;  
	exit();
  }
  
  /////////////////////////////////////////  
  if($opcion == "asentarcompra")
  {  	  
	 $docuid  = $_GET['id'];
	 $total = $clase->BDLockup($docuid,"documentos","docuid","total");
	 
    if($total > 0)
	{
      $fecha   = date("Y-m-d").' 00:00:00';	 	 
	  $vsql = "UPDATE documentos SET fecasentado = '".$fecha."' WHERE docuid=".$docuid;	 
	  $clase->EjecutarSQL($vsql);
      header("Location: documentos.php");
	 
	}
	else
    {
        $clase->Aviso(3,"No se pueden Asentar Compras sin Productos"); 
	    header("Location: documentos.php"); 		
	}	
     exit();
  }

  /////////////////////////////////////////  
  if($opcion == "reversarcompra")
  {  	  
	 $docuid  = $_GET['id'];
	 $vsql = "SELECT DISTINCT DD.itemid , E.existencia , SUM(DD.cantidad) cant FROM existencias E
			  INNER JOIN dedocumentos DD ON ( DD.itemid = E.itemid ) WHERE E.bodegaid = DD.bodegaid AND DD.docuid =".$docuid." GROUP BY 1,2";

	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);
     
     $problemasEncontrados = 0;

     while($row = mysql_fetch_array($result))
     { 
	   $saldoposible = $row['existencia'] - $row['cant'];

	   if( $saldoposible < 0)
	   {
          $problemasEncontrados = 1;
		  break;
	   }  
	 }  

	 if($problemasEncontrados == 1)
	 { 
	   $clase->Aviso(2,"Problemas con las Existencias de Articulos. <a href=\"compras.php?opcion=analisisexistencias&id=".$id."\" rel=\"facebox\"> Ver Detalles </a>"); 
	   header("Location: compras.php?opcion=editarcompra&id=".$docuid); 		
	 }
	 else
	 {
 	    $fecha   = '0000-00-00 00:00:00';	 	 
	    $vsql = "UPDATE documentos SET fecasentado = '".$fecha."' WHERE docuid=".$docuid;	 
	    $clase->EjecutarSQL($vsql);

	    header("Location: documentos.php");
	 }
	 
    exit();

  }

  /////////////////////////////////////////  
  if($opcion == "preanularcompra")
  {  	  
       $docuid  = $_GET['id'];
	   echo'<br> <table width="400"> <tr> 
				    <td align="center"> <b> Est&aacute; seguro de Anular el Documento? </b> </td> 
			     </tr> <tr> 
				    <td align="center"> Este proceso es Irreversible </td> 
			     </tr> <tr> 
				    <td align="center"> <a href="compras.php?opcion=anularcompra&amp;id='.$docuid.'"> Anular Documento </a> </b> </td> 
			     </tr> </table> <br>';
		exit();		
  }
  /////////////////////////////////////////  
  if($opcion == "anularcompra")
  {  	  
	 $docuid  = $_GET['id'];
	 $fecha   = date("Y-m-d").' 00:00:00';
	 	 	 
	 $vsql = "UPDATE documentos SET fechadoc = '".$fecha."' , fecasentado = '".$fecha."' , fecanulado = '".$fecha."' ,
	          base = 0 , iva = 0 , total = 0 , totalitems = 0 WHERE docuid=".$docuid;	 
	 $docuid = $clase->EjecutarSQL($vsql);
	 
	 $vsql = "UPDATE dedocumentos SET cantidad = 0 , valunitario = 0 , valdescuento = 0 ,valparcial = 0 , porciva = 0 , 
	          valbase = 0 , valiva = 0 WHERE docuid=".$docuid;	 
	 $docuid = $clase->EjecutarSQL($vsql);
	 
     header("Location: documentos.php");
  }

  /////////////////////////////////////////  
  if($opcion == "guardarencabezado")
  {  	  
	 $docuid   = $_POST['id'];
	 $prefijo  = $_POST['prefijo'];
     $numero   = $_POST['numero'];
	 $fecha    = FechaMySQL($_POST['fecha']);	 	 
	 $cliente  = $_POST['cliente'];	 	 
	 $vendedor = $_POST['vendedor'];	 	 	 
	 $observ   = $_POST['observ'];	 	 

     $terid1  = $clase->BDLockup($cliente,"terceros","codigo","terid");
     $terid2  = $clase->BDLockup($vendedor,"terceros","codigo","terid");	 
  	 	 	 
	 $vsql = "UPDATE documentos SET prefijo = '".$prefijo."' , numero = '".$numero."' ,
	          fechadoc = '".$fecha."' , terid1 = '".$terid1."' ,
			  terid2 = '".$terid2."' , observacion = '".$observ."' 
			  WHERE docuid=".$docuid;	

	 $clase->EjecutarSQL($vsql);
	 header("Location: ventas.php?opcion=editarventa&id=".$id);
  }
    
  /////////////////////////////////////////  
  if($opcion == "nuevacompra")
  {
     $tipop    = "FCO";
	 $prefijop = "00";
	 $numerop  = "T-".rand(100000,999999);
	 $fechap   = date("d/m/Y");
	 $teridp   = "1";
	 $creador  = $_SESSION['USERNAME'];	 
	 
	 $vsql = "INSERT INTO documentos(tipodoc,prefijo,numero,fechadoc,terid1,terid2,observacion,base,iva,total,creador,momento) 
	          values('".$tipop."','".$prefijop."','".$numerop."',CURRENT_TIMESTAMP,".$teridp.",".$teridp.",'',0,0,0,'".$creador."',CURRENT_TIMESTAMP)";
	 $clase->EjecutarSQL($vsql);
	 
	 $docuid = $clase->SeleccionarUno("SELECT Max(docuid) FROM documentos");
	 header("Location: ?opcion=editarcompra&id=".$docuid);
  }
 
  /////////////////////////////////////////  
  if($opcion == "editarcompra")
  {  	  
     $id = $_GET['id'];
     $_SESSION["DOCUID"] = $id;	 
	 $cont = $clase->Header("N","W"); 
	
	 //Barras Superiores de Documentos e Insercion y Acciones Masivas
	 $cont.='<form action="?opcion=guardarencabezado" method="POST" name="x">';
	 $cont.= BarraDocumentos();
	 $cont.= SegundaBarraEditarDocCompra($id);

     // Extraigo Datos desde MySQL
     $vsql = "SELECT * FROM v_compras WHERE docuid=".$id;
	 $conex  = $clase->Conectar();
     $result = mysql_query($vsql,$conex);

     $i=0;
     $detalles = '';
 
     while($row = mysql_fetch_array($result))
     { 
       $tipop   = "FCO";
       $prefijo = $row['prefijo'];
	   $numero  = $row['numero'];
  	   $fecha   = FormatoFecha($row['fechadoc']);
 	   $cliente  = $row['codcliente'];
 	   $vendedor = $row['codvendedor'];	   
 	   $base     = $row['base'];	   
 	   $iva      = $row['iva'];	   	   	   
 	   $total    = $row['total'];	   	   	   	   
       $items    = $row['totalitems'];
	   $observ   = $row['observacion'];	   	   	   
	    
	   if($i%2==0)
	     $detalles.= '<tr class="TablaDocsImPar">';
	   else 
	     $detalles.= '<tr class="TablaDocsPar">';	   
       
	   $detalles.='  <td width="10"> &nbsp; </td>
				     <td width="80" align="left">'.$row['codproducto'].'</td>			
		 		     <td width="150"> <label class="Texto11"> '.substr($row['nomproducto'],0,50).' </label> </td>			
					 <td width="50" align="right"> '.$row['cantidad'].' </td>
					 <td width="80" align="right"> '.$row['codbodega'].' </a></td>
				 	 <td width="75" align="right"> '.FormatoNumero($row['valparcial']).' </td>	
		             <td width="10"> &nbsp; </td>
					</tr>';
	   $i++;
    }     
	
    // Division para Ingreso de Documentos
	$cont.='<table width="950">   <tr valign="top">  <td width="550">';
	 
  	// Formulacio Insertar Documentos
	$cont.='<input type="hidden" name="id" value="'.$id.'"> 
	        <table widht="627">
	         <tr valign="middle" class="EncabezadoDocu" height="33"> 
			   <td width="7"> &nbsp; </td>
			   <td width="83"> Documento : </td> 
			   <td width="220"> <select name="tipodoc"> 
			           <option value="FCO">FCO</option> 
                       <option value="REN">REN</option> 					   
					</select> 
			        <input type="text" size="2" name="prefijo" value="'.$prefijo.'">
					<input type="text" size="7" name="numero" value="'.$numero.'"> 
			   </td>			
  			   <td width="7"> &nbsp; </td>
			   <td width="56"> Fecha : </td>
			   <td width="162"> <input size="7" id="f_date1" name="fecha" value="'.$fecha.'"/> <input type="image" src="images/calendario.png" id="f_btn1">
				    <script type="text/javascript">//<![CDATA[
				      Calendar.setup({
				      inputField : "f_date1",
				      trigger    : "f_btn1",
				      onSelect   : function() { this.hide() },					  
			          showTime   : 12,
			          dateFormat : "%d/%m/%Y"
				      });
					//]]></script>
                </td> 
              </tr>
             </table>
		 
			<table widht="627">
			 <tr valign="middle" class="EncabezadoDocu" height="25"> 
			   <td width="7"> &nbsp; </td>
			   <td width="83"> Proveedor : </td> 
			   <td width="180"> 
			   

<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework7.js"></script>
<style type="text/css">
#search-wrap7 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res7{width:150px; border:solid 1px #DEDEDE; display:none;}
#res7 ul, #res7 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res7 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res7 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res7 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res7 li a:hover{background:#FFFFFF;}
#res7 ul {padding:4px;}
</style>
<div id="search-wrap7">
<input name="cliente" id="search-q7" type="text" onkeyup="javascript:autosuggest7();" maxlength="12" size="15" tabindex="5" value="'.$cliente.'"/>
<div id="res7"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			   
			   
			  
			   </td>			   
			   <td width="20"> &nbsp; </td>
			   <td width="83"> Cobrador : </td> 
			   <td width="163"> 
			   
<!-- ************************************************************************************************ -->
<!-- AJAX AUTOSUGGEST SCRIPT -->
<script type="text/javascript" src="lib/ajax_framework8.js"></script>
<style type="text/css">
#search-wrap8 input{font-size:13px; text-transform:Capitalize; background-color:#D6F0FE; border-style:groove;}
#res8{width:150px; border:solid 1px #DEDEDE; display:none;}
#res8 ul, #res8 li{padding:0; margin:0; border:0; list-style:none; background:#F6F6F6;}
#res8 li {border-top:solid 1px #DEDEDE; background:#CEEAF5;}
#res8 li a{display:block; padding:2px; text-decoration:none; color:#000000; font-weight:bold; font-size:10px; font-family:Verdana;}
#res8 li a small{display:block; text-decoration:none; color:#999999; font-weight:normal; font-size:9px;font-family:Verdana;}
#res8 li a:hover{background:#FFFFFF;}
#res8 ul {padding:4px;}
</style>
<div id="search-wrap8">
<input name="vendedor" id="search-q8" type="text" onkeyup="javascript:autosuggest8();" maxlength="12" size="15" autocomplete="off" tabindex="5" value="'.$vendedor.'"/>
<div id="res8"></div>
</div>
<!-- AJAX AUTOSUGGEST SCRIPT -->
<!-- ************************************************************************************************ -->
			   
			   </td>
			 </tr>   
	         </table>
			 
			 <table widht="627">
			 <tr valign="middle" class="EncabezadoDocu" height="58"> 
			   <td width="7"> &nbsp; </td>
			   <td width="100"> Observacion </td> 
			   <td width="496"> <textarea name="observ" cols="49" rows="2">'.$observ.'</textarea> </td>
			 </tr>		 
		   </table>
		   </form>
		   
		   <table widht="100%">
			 <tr valign="middle" class="TotalesDocumentos"> 
			   <td width="65%">
			     
				 <table width="100%">
				   <tr height="25" class="TotalesDocumentos"> 
				    <td width="10"> &nbsp; </td>
		  		    <td width="60" align="right"> Dctos : &nbsp;</td>
					<td width="80" align="right"> 0 </td>
			        <td width="20"> &nbsp; </td>			    
			        <td width="100" align="right"> Prods : &nbsp; </td>
			        <td width="70" align="right"> '.$items.' Und  </td>
			        <td width="30"> &nbsp; </td>			   
		           </tr>   
  			       <tr valign="middle" class="TotalesDocumentos" height="25"> 
			        <td width="10"> &nbsp; </td>
			        <td width="60" align="right"> Base : &nbsp;</td>
			        <td width="80" align="right">  '.FormatoNumero($base).' </td>
			        <td width="20"> &nbsp; </td>			    
			        <td width="100" align="right"> I.V.A. : &nbsp; </td>
			        <td width="70" align="right"> '.FormatoNumero($iva).' </td>
			        <td width="30"> &nbsp; </td>			   
		           </tr>   
	              </table>
               </td>
			   <td align="center">
			     <h1>'.FormatoNumero($total).'</h1>
			  </td>
			</tr>
		   </table>	   	 
			   
			   
			    	
		   <table width="100%">
		   <tr class="BarraDocumentos">
		     <td width="10"> &nbsp; </td>
			 <td width="80"> <b>Código</b> </td>
			 <td width="150"> <b>Producto</b> </td>			 
			 <td width="50" align="right">  <b>Cant</b> </td>
			 <td width="80" align="center">  <b>Bodega</b> </td>
		 	 <td width="60" align="right">  <b>Precio</b> </td>	
		     <td width="22"> &nbsp; </td>
		   </tr>
		  </table>
		  
<div id=scrolltable style=" background: #FFFFFF; overflow:auto;
padding-right: 0px; padding-top: 0px; padding-left: 0px; padding-bottom: 0px;
border-right: #6699CC 0px solid; border-top: #999999 0px solid;
border-left: #6699CC 0px solid; border-bottom: #6699CC 0px solid;
scrollbar-arrow-color : #999999; scrollbar-face-color : #666666;
scrollbar-track-color :#3333333 ; position: absolute;
height:400px; width:535px">   
		  
		  <table width="518">
		   '.$detalles.'</table></div>';

	// Lateral con informacion de Productos
	$cont.='  </td> 
	          <td width="1"> &nbsp; </td>
	          <td align="left" width="400"> 
                 <iframe src="ayudadocumentos.php?id='.$id.'" frameborder="0" scrollbars="auto" width="410" height="600"> </iframe>
  	     	  </td>  
		    </tr>   
	  	  </table>';			
  }
  
  ////////////////////////////////
  echo $cont.$clase->PiePagina();  

  
?>