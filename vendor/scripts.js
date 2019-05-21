  <script>
var   xmlhttp, myObj, x, txt = "";
xmlhttp = new XMLHttpRequest();
xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        myObj = JSON.parse(this.responseText);
        txt += "<option value=0>Elegir Opción</option>" + "<br>";
        for (x=0; x < 23 ; x++) {
            txt += "<option value="+myObj['provincias'][x].id+">"+myObj['provincias'][x].nombre+"</option>" + "<br>";
          }
        document.getElementById("idProvincia").innerHTML = txt;
    }
};
xmlhttp.open("GET", "https://apis.datos.gob.ar/georef/api/provincias?campos=nombre&max=24&aplanar", true);
xmlhttp.send();

</script>

<script>
var   xmlhttp2, partido, j, txt2 = "";
xmlhttp2 = new XMLHttpRequest();
xmlhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        partido = JSON.parse(this.responseText);
        txt2 += "<option value=0>Elegir Opción</option>" + "<br>";
        for (j=0; j < 135 ; j++) {
            txt2 += "<option value="+partido['municipios'][j].id+">"+partido['municipios'][j].nombre+"</option>" + "<br>";
          }
        document.getElementById("idPartido").innerHTML = txt2;
    }
};
xmlhttp2.open("GET", "https://apis.datos.gob.ar/georef/api/municipios?campos=nombre&provincia=06&aplanar", true);
xmlhttp2.send();

</script>