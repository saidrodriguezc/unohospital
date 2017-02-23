<?PHP
  session_start(); 
  include("../lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
  include("configreportes.php");  
  require_once '../lib/PHPExcel.php';

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
  $ruta="../";
    
  $avisos = ""; 
  $opcion = "";
  $opcion = $_GET["opcion"];
 
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////
  if($opcion == "ver")
  {
    ini_set("memory_limit","300M");
	$clase->Nombredb = $_SESSION['DBNOMBRE']; 
	$conex  = $clase->Conectar();
	$fdX = $_POST['fecdesde'];  
	$fhX = $_POST['fechasta'];
	$desde = substr($fdX,6,4)."-".substr($fdX,3,2)."-".substr($fdX,0,2);
	$hasta = substr($fhX,6,4)."-".substr($fhX,3,2)."-".substr($fhX,0,2);	
	
	$vsql = "SELECT D.nitempresa , EMP.nombre Empresa , PA.nit, PA.nombre1, PA.nombre2, PA.apellido1, PA.apellido2, PA.direccion, PA.telefono, PA.celular, 
	         PA.email, PA.cargo, PA.edad, PA.genero, E.descripcion estacivil, NE.descripcion niveledu, PRO.nombre MedicoAtendio ,  CM.descripcion ConceptoMedico ,
	         D.total , HC. * , HCS. * 
			 FROM historiacli HC
			 INNER JOIN historiaself HCS ON ( HC.historiaid = HCS.historiaid ) 
			 INNER JOIN conceptomed CM ON (CM.conceptomedid = HC.conceptomed)
			 INNER JOIN documentos D ON (D.docuid = HC.docuid)
			 INNER JOIN contratos CON ON (CON.contratoid = D.contratoid) 
			 INNER JOIN terceros EMP ON (EMP.terid = CON.terid)
			 INNER JOIN terceros PA ON ( PA.terid = HC.teridpaciente ) 
			 INNER JOIN estadocivil E ON ( E.codigo = PA.estadocivilid ) 
			 INNER JOIN niveledu NE ON ( NE.codigo = PA.nivelid ) 
			 INNER JOIN terceros PRO ON (PRO.terid = HC.teridprof)
			 WHERE HC.momento >= '".$desde." 00:00:00' AND HC.momento <= '".$hasta." 23:59:59' ORDER BY 1,2,4";

	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	date_default_timezone_set('Europe/London');

	if (PHP_SAPI == 'cli')
	    die('This example should only be run from a Web Browser');

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Sistemas y Soluciones Web de Colombia")
								 ->setLastModifiedBy("Sistemas y Soluciones Web de Colombia")
								 ->setTitle("Relacion de Compras - DROPOS")
								 ->setSubject("Relacion de Compras - DROPOS")
								 ->setDescription("Relacion de Compras - DROPOS")
								 ->setKeywords("Relacion de Compras - DROPOS")
								 ->setCategory("Categoria General");    
	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)
    	        ->setCellValue('A1', 'NIT EMPRESA')
        	    ->setCellValue('B1', 'EMPRESA')				
            	->setCellValue('C1', 'NIT PACIENTE')
				->setCellValue('D1', 'NOMBRE 1')
        	    ->setCellValue('E1', 'NOMBRE 2')
        	    ->setCellValue('F1', 'APELLIDO 1')
        	    ->setCellValue('G1', 'APELLIDO 2')
        	    ->setCellValue('H1', 'DIRECCION')
                ->setCellValue('I1', 'TELEFONO')
        	    ->setCellValue('J1', 'CELULAR')				
            	->setCellValue('K1', 'EMAIL')
				->setCellValue('L1', 'CARGO')
        	    ->setCellValue('M1', 'EDAD')
        	    ->setCellValue('N1', 'GENERO')
        	    ->setCellValue('O1', 'ESTADO CIVIL')
        	    ->setCellValue('P1', 'NIVEL EDUC')
        	    ->setCellValue('Q1', 'MEDICO ATENDIO')
        	    ->setCellValue('R1', 'CONCEPTO MED')				
            	->setCellValue('S1', 'VLR TOTAL FVE')
				->setCellValue('T1', 'TIPO EXAMEN')
        	    ->setCellValue('U1', 'OBSERV 1')
        	    ->setCellValue('V1', 'OBSERV 2')
        	    ->setCellValue('W1', 'OBSERV 3')
        	    ->setCellValue('X1', 'OBSERV 4')        	    
        	    ->setCellValue('Y1', 'OBSERV 5')
        	    ->setCellValue('Z1', 'OBSERV 6')
        	    ->setCellValue('AA1', 'PESO')
        	    ->setCellValue('AB1', 'TALLA')				
            	->setCellValue('AC1', 'IMC')
				->setCellValue('AD1', 'TENS ART')
        	    ->setCellValue('AE1', 'FREC CARD')
        	    ->setCellValue('AF1', 'FREC RESP')
        	    ->setCellValue('AG1', 'LATERALIDAD')
        	    ->setCellValue('AH1', 'FUMA')        	    
        	    ->setCellValue('AI1', 'BEBE')
        	    ->setCellValue('AJ1', 'DEPORTE');
    
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);				
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);					
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);				
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);					
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);				
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);					
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);				
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);					
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);				
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);					
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);				
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);					
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);							
	$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);					
	$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);				
	$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);					
	$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setAutoSize(true);						
	$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setAutoSize(true);							
	$objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setAutoSize(true);								
	
	$conex  = $clase->Conectar();
    $result = @mysql_query($vsql,$conex); 
	$cant   = @mysql_num_rows($result);
    $i=2;
	$total1=0;   $total2=0;   $total3=0;    $total4=0;     $total5=0;     $total6=0;     $total7=0;     $total8=0;   $total9=0;
	$proveactual =""; 
	$DOACT = "";  $fdesde = "";    $fhasta="";          $det = "";
 	$canfacdia = 0;   $sumadia1 = 0;   $sumadia2 = 0;   $sumadia3 = 0;    $sumadia4 = 0;	
	while($row = @mysql_fetch_array($result)) 
	{
	       /*
	       D.nitempresa , EMP.nombre Empresa , PA.nit, PA.nombre1, PA.nombre2, PA.apellido1, PA.apellido2, PA.direccion, PA.telefono, PA.celular, 
	         PA.email, PA.cargo, PA.edad, PA.genero, E.descripcion estacivil, NE.descripcion niveledu, PRO.nombre MedicoAtendio ,  CM.descripcion ConceptoMedico ,
	         D.total
	         */
	       $objPHPExcel->setActiveSheetIndex(0)
   		          ->setCellValue('A'.$i, $row['nitempresa'])
   	    	      ->setCellValue('B'.$i, $row['Empresa'])
   	    	      ->setCellValue('C'.$i, $row['nit'])				  
   	      	      ->setCellValue('D'.$i, $row['nombre1'])
				  ->setCellValue('E'.$i, $row['nombre2'])
				  ->setCellValue('F'.$i, $row['apellido1'])
				  ->setCellValue('G'.$i, $row['apellido2'])
				  ->setCellValue('H'.$i, $row['direccion'])
				  ->setCellValue('I'.$i, $row['telefono'])
   	    	      ->setCellValue('J'.$i, $row['celular'])
   	    	      ->setCellValue('K'.$i, $row['email'])				  
   	      	      ->setCellValue('L'.$i, $row['cargo'])
				  ->setCellValue('M'.$i, $row['edad'])
				  ->setCellValue('N'.$i, $row['genero'])
				  ->setCellValue('O'.$i, $row['estacivil'])
				  ->setCellValue('P'.$i, $row['niveledu'])
				  ->setCellValue('Q'.$i, $row['MedicoAtendio'])
   	    	      ->setCellValue('R'.$i, $row['ConceptoMedico'])
   	    	      ->setCellValue('S'.$i, $row['total'])				  
   	      	      ->setCellValue('T'.$i, $row['tipoexamen'])
				  ->setCellValue('U'.$i, $row['observa1'])
				  ->setCellValue('V'.$i, $row['observa2'])
				  ->setCellValue('W'.$i, $row['observa3'])
				  ->setCellValue('X'.$i, $row['observa4'])
				  ->setCellValue('Y'.$i, $row['observa5'])
   	    	      ->setCellValue('Z'.$i, $row['observa6'])   	    	      
				  ->setCellValue('AA'.$i, $row['efpeso'])
   	    	      ->setCellValue('AB'.$i, $row['eftalla'])
   	    	      ->setCellValue('AC'.$i, $row['efimc'])				  
   	      	      ->setCellValue('AD'.$i, $row['eftart'])
				  ->setCellValue('AE'.$i, $row['effcard'])
				  ->setCellValue('AF'.$i, $row['effresp'])
				  ->setCellValue('AG'.$i, $row['lateralidad'])
				  ->setCellValue('AH'.$i, $row['fuma'])
				  ->setCellValue('AI'.$i, $row['bebe'])
   	    	      ->setCellValue('AJ'.$i, $row['deporte']);     
   	   $i++; 	      
    } 	      
    
	$rangoEncabezado = 'A1:AJ1';
	$rangoCeldas     = 'A2:AJ'.($cant+1);
	
    // Formato de Encabezado y Celdas
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
    
    $EstiloCeldas = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('argb' => '00000000'),
		),
	  ),
     );
    $objPHPExcel->getActiveSheet()->getStyle($rangoCeldas)->applyFromArray($EstiloCeldas);
    /// Formato de Encabezados 
    $EstiloEncabezado = array(
	'font' => array(
		'bold' => true,
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,		
	),
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('argb' => '00000000'),
		),
	),
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'rotation' => 90,
		'startcolor' => array(
			'argb' => '00FFFFCC',
		),
		'endcolor' => array(
			'argb' => 'FFFFFF',
		),
	  ),
    );
    $objPHPExcel->getActiveSheet()->getStyle($rangoEncabezado)->applyFromArray($EstiloEncabezado);	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Data');
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="RepCondiciones.xls"');
	header('Cache-Control: max-age=0');
    /// Genero la Salida
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;			 
  }


  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
  if($opcion == "")
  {
	 $cont = $clase->HeaderReportes();
     $cont.= EncabezadoReporte("Condiciones de Salud entre Fechas");		 
    
	 $cont.='<form action="?opcion=ver" method="POST">
	         <table width="600">
	           <tr class="CabezoteTabla"> 
			     <td width="10"> </td>
			     <td width="37"> <img src="'.$ruta.'images/iconos/informes.png" width="32" height="32" border="0"> </td>
				 <td width="553"> Rango de Fechas <td>
			  </tr>
			 </table>
			 <table width="600">
	           <tr class="BarraDocumentos"> 
			     <td width="20"> </td>
			     <td width="200"> Desde : </td>
			     <td width="300"> <input type="text" name="fecdesde" size="10" maxlenght="10" value="'.date("01/m/Y").'" autocomplete="off"> </td>
				 <td width="50"> <td>
			  </tr>
	           <tr class="BarraDocumentos"> 
			     <td width="20"> </td>
			     <td width="200"> Hasta : </td>
			     <td width="300"> <input type="text" name="fechasta" size="10" maxlenght="10" value="'.date("30/m/Y").'" autocomplete="off"> </td>
				 <td width="50"> <td>
			  </tr>
			 </table>
			 <br>
			 <table width="600"> 
              <tr> 
 			     <td align="center"> <input type="submit" value="Ver Reporte"> </td>				 
			  </tr>
			 </table> </form>';
   echo $cont; 
  }
  
  ////////////////////////////////////////////////
  ////////////////////////////////////////////////  
 

?> 