var comunas;
document.addEventListener("DOMContentLoaded", function () {
    // Realizar una consulta AJAX al archivo cargarDatos.php
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "cargarDatos.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Parsear la respuesta JSON
            var datos = JSON.parse(xhr.responseText);
            // Llenar el select de Regiones
            var selectRegiones = document.getElementById("region");
            llenarSelect(selectRegiones, datos.regiones);

            // Llenar el select de Comunas segun la region seleccionada
            comunas = datos.comunas;
            cargarComunas();
            // Llenar el select de Candidatos
            var selectCandidatos = document.getElementById("candidato");
            llenarSelect(selectCandidatos, datos.candidatos);
        }
    };
    xhr.send();
});

function llenarSelect(selectElement, opciones , id = 0) {
    // Limpiar opciones existentes
    selectElement.innerHTML = "";

    // Crear nuevas opciones
    for (var key in opciones) {
        if (opciones.hasOwnProperty(key)) {
            var option = document.createElement("option");
            option.value = key;
            option.text = opciones[key];
            selectElement.appendChild(option);
        }
    }
}

function mostrarMensajeError(mensaje) {
    $('#mensaje').html('<div class="alert alert-danger">' + mensaje + '</div>');
}
function mostrarMensajeExito(mensaje) {
    $('#mensaje').html('<div class="alert alert-warning">' + mensaje + '</div>');
}

// Función para validar el Nombre y Apellido
function validarNombreApellido() {
    var nombreApellido = $('#nombreApellido').val();
    var nombreApellido = document.getElementById('nombreApellido').value;
    if (nombreApellido.trim() === '') {
        mostrarMensajeError('Nombre y Apellido no pueden quedar en blanco.');
        return false;
    }
    
    return true;
}

function validarAlias() {
    // Validar alias (más de 5 caracteres, letras y números)
    const aliasInput = document.getElementById("alias");
    const aliasRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/;

    if (!aliasRegex.test(aliasInput.value)) {
        mostrarMensajeError('Alias debe tener más de 5 caracteres y contener letras y números.');
        return false;
    }

    return true;
}

function validarRUT() {
    // Validar RUT (Formato Chile)
    const rutInput = document.getElementById("rut");
    const rutRegex = /^\d{1,2}\.\d{3}\.\d{3}-[\d|kK]$/;

    if (!rutRegex.test(rutInput.value)) {
        mostrarMensajeError('Ingrese un RUT válido (Formato Chile).');
        return false;
    }

    return true;
}

function validarEmail() {
    // Validar correo según estándar
    const emailInput = document.getElementById("email");
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(emailInput.value)) {
        mostrarMensajeError('Ingrese un correo electrónico válido.');
        return false;
    }

    return true;
}

function validarCheckbox() {
    // Validar al menos dos opciones seleccionadas
    const checkboxes = document.querySelectorAll('input[name="comoSeEntero[]"]:checked');

    if (checkboxes.length < 2) {
        mostrarMensajeError("Seleccione al menos dos opciones en 'Como se enteró de Nosotros'.");
        return false;
    }

    return true;
}

function enviarFormulario() {
    if(!validarNombreApellido() || !validarAlias() || !validarRUT() || ! validarEmail() ||!validarCheckbox()){
        return false;
    }
    // Obtén los datos del formulario
    var formData = $('#formularioVotacion').serialize();
    $('#mensaje').empty();
    // Realiza la petición AJAX
    $.ajax({
        type: 'POST',
        url: 'process.php',
        data: formData,
        dataType: 'json',
        success: function(data) {
            mostrarMensajeExito(data.mensaje);
            if (data.status === 'success') {
                window.location.href = 'exito.php';
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            mostrarMensajeError(jqXHR.responseText);
        }
    });
}
// funcion para cargar de Comunas segun la region
function cargarComunas() {
    // Limpiar opciones existentes
    var selectComunas = document.getElementById("comuna");
    selectComunas.innerHTML = "";
    
    var selectRegiones = document.getElementById("region");
    var selectedRegionId = selectRegiones.value;
    
    // Filtrar las comunas por el ID de la región seleccionada
    for (var key in comunas) {
        if (comunas.hasOwnProperty(key) && comunas[key].region_id === selectedRegionId) {
            var option = document.createElement("option");
            option.value = comunas[key].id;
            option.text = comunas[key].nombre;
            selectComunas.appendChild(option);
        }
    }
}