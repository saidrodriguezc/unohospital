/* 1ro Hacer el reemplazo general del Numero X por Y sin cambiar la linea 50 q1= */

/// Cambiar nombre funcion fillXX
/// Cambiar search-qX y resX ////
function fill5(i) {
e5= document.getElementById('search-q5');
e5.value=i;
document.getElementById('res5').style.display="none";
}

/// Aqui no cambiar Nada ////
function createObject5() {
	var request_type5;
	var browser5 = navigator.appName;
	if(browser5 == "Microsoft Internet Explorer"){
	request_type5 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type5 = new XMLHttpRequest();
	}
		return request_type5;
}

var http5 = createObject5();

// Cambiar nombre funcion autossugesstXX
// Cambiar search-qX - Variable qX   y  ruta de busqueda
function autosuggest5() {
q5 = document.getElementById('search-q5').value;
nocache = Math.random();
http5.open('get', 'lib/searchzona5.php?q1='+q5+'&nocache = '+nocache);
http5.onreadystatechange = autosuggestReply5;
http5.send(null);
}


// Cambiar resX
function autosuggestReply5() {
if(http5.readyState == 4){
	var response5 = http5.responseText;
	e5 = document.getElementById('res5');
	if(response5!=""){
		e5.innerHTML=response5;
		e5.style.display="block";
	} else {
		e5.style.display="none";
	}
}
}