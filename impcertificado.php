<?PHP
  session_start(); 
  include("lib/Sistema.php");

  $clase = new Sistema();
  $clase->Nombredb = $_SESSION['DBNOMBRE']; 
 
  $opcion = $_GET["opcion"];
  $id     = $_GET["id"];
  

  if($opcion == "error")
  {
      echo' <br><br> <center>
	   <h2> Se ha producido un Error!</h3>
	   <b> Revise la informacion del Paciente y de la orden de Servicio </b> <br><br>
	   <a href="javascript:window.close();"> Cerrar Ventana </a>
	  ';    
	  exit();
  }
  
  ///////////////////////////////////////////////////////////////////////////////////////////  
  ///////////////////////////////////////////////////////////////////////////////////////////
  if($opcion == "")
  {
    if($id > 3000)
	{
       header("Location: impcertificado2013.php?&id=".$id); 
    } 
	
  $vsql = "SELECT EC.descripcion ECivil , HC.momento fhis , NE.descripcion NivelEdu , D.nitempresa, HC.* , HS.* , T.* FROM historiacli HC 
           INNER JOIN historiaself HS ON (HS.historiaid = HC.historiaid) 
		   INNER JOIN terceros T ON (T.terid = HC.teridpaciente)
		   INNER JOIN documentos D ON (D.docuid = HC.docuid)
		   LEFT JOIN estadocivil EC ON (T.estadocivilid = EC.codigo)
		   LEFT JOIN niveledu NE ON (T.nivelid = NE.nivelid)		   		   
		   WHERE HC.historiaid=".$id;

  $conex  = $clase->Conectar();
  $result = mysql_query($vsql,$conex);
  $registros = mysql_num_rows($result);

  if($registros == 0)
      header("Location: impcertificado.php?opcion=error");	  
      
  
  if($row = mysql_fetch_array($result)) 
  {
   	 require('lib/fpdf/fpdf.php');
	 $pdf=new FPDF();
	 $pdf->AddPage();
     
     $x=25;
     
	 /// Primero Coloco las Firmas del Medico y paciente para que queden detras del Contenido
  	 $firmamedico = $clase->BDLockup($row['teridprof'],'terceros','terid','rutafirma');
	 if($firmamedico != "")
	 {
	    if(file_exists('firmas/'.$firmamedico))
	      $pdf->Image('firmas/'.$firmamedico,12,113,40,15,0);
     }
 	 if($row['rutafirma'] != "")
	 {
	    if(file_exists('firmas/'.$row['rutafirma']))
	       $pdf->Image('firmas/'.$row['rutafirma'],110,110,65,29,0);
     }
 
	 /// Imprimo los Formatos del Papel
	 $pdf->Image('images/logoempresa.jpg',5,6,80,19,0);
	      
	 $pdf->SetFont('Arial','B',20);
	 $cont= 'CERTIFICADO DE ';                              $pdf->Text(102,14,$cont);	
	 $cont= 'APTITUD LABORAL';                              $pdf->Text(99,22,$cont);	
	 
 	 if(($row['rutafoto'] != "")&&($_SESSION['USERNAME'] != "SINFOTO"))
	 {
	    if(file_exists('fotos/'.$row['rutafoto']))
	 	    $pdf->Image('fotos/'.$row['rutafoto'],178,3,25,29,0);
	 }
     		
	 $pdf->Rect(176,2,29,31);	 

	 $pdf->Rect(4,50,50,13);	 	 
	 $pdf->Rect(55,50,150,13);	 	 	 
	 $pdf->Rect(4,100,201,13);	 	 

	 $pdf->SetFont('Arial','B',10);
	 
	 $cont= 'CIUDAD :';                       	 $pdf->Text(6,35,$cont);	
	 $cont= 'FECHA :';                         	 $pdf->Text(70,35,$cont);		 
	 $cont= 'HORA :';                         	 $pdf->Text(120,35,$cont);		 
	 	 
     $cont= 'TIPO DE EXAMEN';                    $pdf->Text(6,54,$cont);		 
	 $cont= 'CONCEPTO';                       	 $pdf->Text(56,54,$cont);		 

     $cont= 'RECOMENDACIONES';
     $pdf->Text(5,104,$cont);

     $pdf->SetFont('Arial','',9);    
     $cont = $row['observa5'];
     //$pdf->Text(7,109,$cont);		 
	 $pdf->SetXY(4,105);                                           $pdf->MultiCell(180,2.9,$cont,0,'L');

     $teridprof  = $clase->BDLockup($id,'historiacli','historiaid','teridprof');
	 $nombreprof = $clase->BDLockup($teridprof,'terceros','terid','nombre');
  	 $registroprof = $clase->BDLockup($teridprof,'terceros','terid','registropro');
	  
	 /// Firmas
	 $pdf->SetFont('Arial','',8);
	 $cont= 'FIRMA MEDICO';                $pdf->Text(8,129,$cont);
	 $pdf->Text(8,132,$nombreprof);
	 $pdf->Text(8,135,'L.S.O. No. '.$registroprof);	   
	 		 
	 $pdf->line(8,125,70,125);

	 $cont= 'FIRMA TRABAJADOR';
	 $pdf->Text(120,129,$cont);		 
	 $pdf->line(105,125,170,125);

     /*$pdf->Rect(187,115,18,15);	 	  
	 
	 $pdf->SetFont('Arial','',7);
	 $cont= 'Huella Indice';        	 $pdf->Text(189,132,$cont);	
	 $cont= 'Derecho';        	         $pdf->Text(191,135,$cont);		 
	 */	 
	 $cont= 'El Presente certificado no tiene validez sin la firma del Médico especialista y sello de la empresa';
	 $pdf->Text(76,135,$cont);	
	 
	 /// Coloco los Datos en la casilla correspondiente
	 /// Datos Fijos
	 $pdf->SetFont('Arial','B',9);   	 $pdf->Text(24,35,'SAN JOSE DE CUCUTA');	
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(86,35,substr($row['fhis'],8,2)."/".substr($row['fhis'],5,2)."/".substr($row['fhis'],0,4));	
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(135,35,substr($row['fhis'],11,2).":".substr($row['fhis'],14,2).":".substr($row['fhis'],17,2));		 
	 	 
	 // Datos Variables
	 $pdf->SetFont('Arial','B',10);    	 
	 $pdf->SetXY(5,55);                  $pdf->MultiCell(44,2.9,$row['tipoexamen'],0,'L');
	 $pdf->SetFont('Arial','',12);    	 $pdf->Text(57,59,substr($clase->BDLockup($row['conceptomed'],'conceptomed','codigo','descripcion'),0,100));		 
	 
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(6,41,'PACIENTE : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(28,41,$row['nombre']);	
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(120,41,'DOCUMENTO : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(147,41,$row['nit']);	
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(6,47,'DIRECCION : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(30,47,$row['direccion']);	

	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(6,70,'FECHA DE NACIMIENTO : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(52,70,$row['fechanac']);	
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(81,70,'ESTADO CIVIL : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(109,70,$row['ECivil']);	
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(141,70,'NIVEL EDUCATIVO : ');	
     $pdf->SetFont('Arial','',9);    	 $pdf->Text(177,70,$row['NivelEdu']);	

	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(6,76,'EMPRESA : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(27,76,substr($clase->BDLockup($row['nitempresa'],'terceros','nit','nombre'),0,24));	
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(81,76,'N.I.T. : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(93,76,$row['nitempresa']);	
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(141,76,'CARGO : ');	
     $pdf->SetFont('Arial','',10);    	 $pdf->Text(157,76,$row['cargo']);	

     $pdf->Rect(4,79,201,19);	 	  	 
	 $pdf->SetFont('Arial','B',10);   	 $pdf->Text(6,83,'PROCEDIMIENTOS REALIZADOS');	
    
	 
	 /// Busco los procedimientos realizados - En la Orden
	 $vsql2="SELECT I.descripcion FROM dedocumentos D INNER JOIN item I ON (I.itemid = D.itemid) WHERE D.docuid=".$row['docuid']." AND I.codigo <> '002.01' AND I.codigo <> '002.19'";
     $conex  = $clase->Conectar();
	 $result2 = mysql_query($vsql2,$conex);
     $procedimientos = mysql_num_rows($result2);

	 
     if($procedimientos == 0)
     {  
	    
	 $yproced = 100;
	 }    	  
     else
	 {
	    $yproced = 87;
		$i=1;
        $pdf->SetFont('Arial','',9);
		while($row2 = mysql_fetch_array($result2))
		{
           if($i%2 != 0)
		     $pdf->Text(6,$yproced,". ".$row2['descripcion']);
		   else
		   {
		       $pdf->Text(110,$yproced,". ".$row2['descripcion']);	   
			   $yproced += 4;	
		   }  	      
		   
		   $i++;
		}
	 } 
	 
	 $pdf->Output("certificados/Certific_".$id.".pdf");
	 $pdf->Output();

  }
			
  mysql_free_result($result); 
  mysql_close($conex);			  

}  

?> 