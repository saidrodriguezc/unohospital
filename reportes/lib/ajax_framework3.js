/* 1ro Hacer el reemplazo general del Numero X por Y sin cambiar la linea 30 q1= */

/// Cambiar nombre funcion fillXX
/// Cambiar search-qX y resX ////
function fill3(i) {
e3= document.getElementById('search-q3');
e3.value=i;
document.getElementById('res3').style.display="none";
}

/// Aqui no cambiar Nada ////
function createObject3() {
	var request_type3;
	var browser3 = navigator.appName;
	if(browser3 == "Microsoft Internet Explorer"){
	request_type3 = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		request_type3 = new XMLHttpRequest();
	}
		return request_type3;
}

var http3 = createObject3();

// Cambiar nombre funcion autossugesstXX
// Cambiar search-qX - Variable qX   y  ruta de busqueda
function autosuggest3() {
q3 = document.getElementById('search-q3').value;
nocache = Math.random();
http3.open('get', 'lib/searchlineasprod3.php?q1='+q3+'&nocache = '+nocache);
http3.onreadystatechange = autosuggestReply3;
http3.send(null);
}


// Cambiar resX
function autosuggestReply3() {
if(http3.readyState == 4){
	var response3 = http3.responseText;
	e3 = document.getElementById('res3');
	if(response3!=""){
		e3.innerHTML=response3;
		e3.style.display="block";
	} else {
		e3.style.display="none";
	}
}
}