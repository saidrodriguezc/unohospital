<?PHP
  session_start(); 
  ini_set("memory_limit","100M");
  include("../lib/Sistema.php");
  require_once '../lib/PHPExcel.php';

  $clase = new Sistema();
  
  //// Controlo que la Sesion este Activa 
  if(($_SESSION['ESTADO'] == "OUT")||($_SESSION['ESTADO'] == ""))
  {
     $_SESSION["ESTADO"] = "OUT";
	 session_unset();
	 session_destroy();
	 header("Location: ../index.php");
	 exit();
  }
  else
  {

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
								 ->setTitle("Listado de Ventas en Excel - ELGUIA Clinico")
								 ->setSubject("Listado de Ventas en Excel - ELGUIA Clinico")
								 ->setDescription("Listado de Ventas en Excel - ELGUIA CLinico")
								 ->setKeywords("Listado Ventas Excel")
								 ->setCategory("Categoria General");
    
	//////////////////////////////////////////////////////////////////////////
	///// Cambiar Aqui Datos e Informacion
	//////////////////////////////////////////////////////////////////////////
	
	$objPHPExcel->setActiveSheetIndex(0)
    	        ->setCellValue('A1', 'ANO')
        	    ->setCellValue('B1', 'MES')
            	->setCellValue('C1', 'TIPO ENTIDAD')
	            ->setCellValue('D1', 'DATOS DEL CLIENTE')
	            ->setCellValue('E1', 'FACTURA')				
	            ->setCellValue('F1', 'VLR FACTURADO');
    
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);				
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);					
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);				
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);					
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);			
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);			

    $vsql = "SELECT EXTRACT(YEAR FROM D.fechadoc) ano , EXTRACT(MONTH FROM D.fechadoc) mes , CLA.descripcion tipo , T.nombre  , D.tipodoc , D.numero , D.total
			 FROM documentos D 
			 INNER JOIN terceros T ON (T.terid = D.terid1)
			 INNER JOIN clasificater  CLA ON (CLA.clasificaterid = T.clasificaterid)
			 WHERE D.tipodoc = 'FVE' AND D.fecasentado <> '0000-00-00 00:00:00' AND D.fecanulado = '0000-00-00 00:00:00'
			 ORDER BY 1 ASC , 2 ASC , 3";		
			 
	$conex  = $clase->Conectar();
    $result = mysql_query($vsql,$conex); 
	$cant   = mysql_num_rows($result);	
    $i=2;
	while($row = mysql_fetch_array($result)) 
	{
	  $objPHPExcel->setActiveSheetIndex(0)
   		          ->setCellValue('A'.$i, $row['ano'])
   	    	      ->setCellValue('B'.$i, $row['mes'])
   	      	      ->setCellValue('C'.$i, $row['tipo'])
   	      	      ->setCellValue('D'.$i, $row['nombre'])				  
   	      	      ->setCellValue('E'.$i, $row['tipodoc'].$row['numero'])				  
   	      	      ->setCellValue('F'.$i, $row['total']);				  				  

      $i++;
    }
    
	$rangoEncabezado = 'A1:F1';
	$rangoCeldas     = 'A2:F'.($cant+1);
	
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
		'top' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
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
	$objPHPExcel->getActiveSheet()->setTitle('Listado de Facturas');
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="xlslistaventas.xls"');
	header('Cache-Control: max-age=0');
    /// Genero la Salida
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;

  } /// Fin del Control de Sesion

?> 