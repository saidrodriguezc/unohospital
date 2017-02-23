<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $id = $_GET["id"];
  
  $vsql = "SELECT * FROM historiacli HC 
           INNER JOIN historiaself HS ON (HS.historiaid = HC.historiaid) 
		   INNER JOIN terceros T ON (T.terid = HC.teridpaciente)
		   WHERE HC.historiaid=".$id;
		   
  $conex  = $clase->Conectar();
  $result = mysql_query($vsql,$conex);

  if($row = mysql_fetch_array($result)) 
  {
   	 require('lib/fpdf/fpdf.php');
	 $pdf=new FPDF();
	 $pdf->AddPage();
     
     $x=25;
	 
	 /// Imprimo los Formatos del Papel
	 $pdf->Image('images/logoempresa.jpg',5,6,80,19,0);
	      
	 $pdf->SetFont('Arial','B',20);
	 $pdf->SetTextColor(0,0,90);
	 $cont= 'CERTIFICADO DE ';                              $pdf->Text(102,14,$cont);	
	 $cont= 'APTITUD LABORAL';                              $pdf->Text(99,22,$cont);	
	 
 	 $pdf->Image('fotos/6664195.jpg',178,3,25,29,0);
	 
	 $pdf->SetDrawColor(0,0,110);

	 $pdf->Rect(176,2,29,31);	 

	 $pdf->Rect(4,61,35,13);	 	 
	 $pdf->Rect(45,61,160,13);	 	 	 
	 $pdf->Rect(4,100,201,13);	 	 

	 $pdf->SetFont('Arial','B',10);
	 $pdf->SetTextColor(0,0,110);
	 
	 $cont= 'CIUDAD :';                       	 $pdf->Text(6,35,$cont);	
	 $cont= 'FECHA :';                         	 $pdf->Text(70,35,$cont);		 
	 
     $cont= 'TIPO DE EXAMEN';
	 $pdf->Text(6,65,$cont);		 
	 $cont= 'CONCEPTO';
	 $pdf->Text(46,65,$cont);		 

     $cont= 'RECOMENDACIONES';
	 $pdf->Text(5,104,$cont);		 

     $cont= 'FIRMA MEDICO';
	 $pdf->Text(35,138,$cont);		 
	 $pdf->line(15,135,80,135);

	 $cont= 'FIRMA TRABAJADOR';
	 $pdf->Text(120,138,$cont);		 
	 $pdf->line(105,135,170,135);

     $pdf->Rect(187,117,18,22);	 	 
	 
	 
	 $pdf->SetFont('Arial','',7);
	 $cont= 'Huella Indice';        	 $pdf->Text(189,142,$cont);	
	 $cont= 'Derecho';        	         $pdf->Text(191,145,$cont);		 
	 
	 $cont= 'El Presente certificado no tiene validez sin la firma del Médico especialista y sello de la empresa';
	 $pdf->Text(50,153,$cont);	
	 
	 $cont= 'El Presente certificado no tiene validez sin la firma del Médico especialista y sello de la empresa';
	 $pdf->Text(50,153,$cont);	
	 
	 /// Coloco los Datos en la casilla correspondiente
	 /// Datos Fijos
	 $pdf->SetTextColor(0,0,0);
	 $pdf->SetFont('Arial','B',9);   	 $pdf->Text(24,35,'SAN JOSE DE CUCUTA');	
	 
	 // Datos Variables
	 
	 $pdf->SetTextColor(0,0,0);
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(6,43,'PACIENTE : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(28,43,$row['nombre']);	
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(6,49,'DOCUMENTO : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(32,49,$row['nit']);	
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(6,55,'DIRECCION : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(30,55,$row['direccion']);	
	 
	 $pdf->Output("Certific_".$id.".pdf");
	 $pdf->Output();
	

  }
			
  mysql_free_result($result); 
  mysql_close($conex);			  
?> 