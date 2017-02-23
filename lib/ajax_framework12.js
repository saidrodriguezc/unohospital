/* 1ro Hacer el reemplazo general del Numero X por Y sin cambiar la linea 70 q1= */

/// Cambiar nombre funcion fillXX
/// Cambiar search-qX y resX ////
function fill12(i) {
e12= document.getElementById('search-q12');
e12.value=i;
document.getElementById('res12').style.display="none";
}

/// Aqui no cambiar Nada ////
function createObject12() {
	var request_type12;
	var browser12 = navigator.appName;
	if(browser12 == "Microsoft Internet Explorer"){
	request_type12 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type12 = new XMLHttpRequest();
	}
		return request_type12;
}

var http12 = createObject12();

// Cambiar nombre funcion autossugesstXX
// Cambiar search-qX - Variable qX   y  ruta de busqueda
function autosuggest12() {
q12 = document.getElementById('search-q12').value;
nocache = Math.random();
http12.open('get', 'lib/searchdiagnostico12.php?q12='+q12+'&nocache = '+nocache);
http12.onreadystatechange = autosuggestReply12;
http12.send(null);
}


// Cambiar resX
function autosuggestReply12() {
if(http12.readyState == 4){
	var response12 = http12.responseText;
	e12 = document.getElementById('res12');
	if(response12!=""){
		e12.innerHTML=response12;
		e12.style.display="block";
	} else {
		e12.style.display="none";
	}
}
}