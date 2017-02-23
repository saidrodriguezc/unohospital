/* 1ro Hacer el reemplazo general del Numero X por Y sin cambiar la linea 70 q1= */

/// Cambiar nombre funcion fillXX
/// Cambiar search-qX y resX ////
function fill13(i) {
e13= document.getElementById('search-q13');
e13.value=i;
document.getElementById('res13').style.display="none";
}

/// Aqui no cambiar Nada ////
function createObject13() {
	var request_type13;
	var browser13 = navigator.appName;
	if(browser13 == "Microsoft Internet Explorer"){
	request_type13 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type13 = new XMLHttpRequest();
	}
		return request_type13;
}

var http13 = createObject13();

// Cambiar nombre funcion autossugesstXX
// Cambiar search-qX - Variable qX   y  ruta de busqueda
function autosuggest13() {
q13 = document.getElementById('search-q13').value;
nocache = Math.random();
http13.open('get', 'lib/searchdiagnostico13.php?q13='+q13+'&nocache = '+nocache);
http13.onreadystatechange = autosuggestReply13;
http13.send(null);
}


// Cambiar resX
function autosuggestReply13() {
if(http13.readyState == 4){
	var response13 = http13.responseText;
	e13 = document.getElementById('res13');
	if(response13!=""){
		e13.innerHTML=response13;
		e13.style.display="block";
	} else {
		e13.style.display="none";
	}
}
}