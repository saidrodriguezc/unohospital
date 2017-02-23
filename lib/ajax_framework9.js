/* 1ro Hacer el reemplazo general del Numero X por Y sin cambiar la linea 70 q1= */

/// Cambiar nombre funcion fillXX
/// Cambiar search-qX y resX ////
function fill7(i) {
e7= document.getElementById('search-q7');
e7.value=i;
document.getElementById('res7').style.display="none";
}

/// Aqui no cambiar Nada ////
function createObject7() {
	var request_type7;
	var browser7 = navigator.appName;
	if(browser7 == "Microsoft Internet Explorer"){
	request_type7 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type7 = new XMLHttpRequest();
	}
		return request_type7;
}

var http7 = createObject7();

// Cambiar nombre funcion autossugesstXX
// Cambiar search-qX - Variable qX   y  ruta de busqueda
function autosuggest7() {
q7 = document.getElementById('search-q7').value;
nocache = Math.random();
http7.open('get', 'lib/searchempresa9.php?q1='+q7+'&nocache = '+nocache);
http7.onreadystatechange = autosuggestReply7;
http7.send(null);
}


// Cambiar resX
function autosuggestReply7() {
if(http7.readyState == 4){
	var response7 = http7.responseText;
	e7 = document.getElementById('res7');
	if(response7!=""){
		e7.innerHTML=response7;
		e7.style.display="block";
	} else {
		e7.style.display="none";
	}
}
}