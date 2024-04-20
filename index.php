<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bdleonel";

function insertarRegistro($servername, $username, $password, $dbname) {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["registrar"])) {
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Conexion fallida: " . $conn->connect_error);
        }

        $nombre = $_POST["nombre"];
        $apellido_paterno = $_POST["ap_paterno"];
        $apellido_materno = $_POST["ap_materno"];
        $email = $_POST["correo"];
        
        $sql = "INSERT INTO persona (nombre, ap_paterno, ap_materno, correo, tipo) VALUES (?, ?, ?, ?, 'Cliente')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $apellido_paterno, $apellido_materno, $email);
        
        if ($stmt->execute()) {
            echo '<div class="alert alert-success" role="alert">¡Nuevo registro insertado exitosamente!</div>';
            header("Location: index.php");
        } else {
            echo '<div class="alert alert-danger" role="alert">Error: ' . $stmt->error . '</div>';
        }
        
        $stmt->close();
        $conn->close();
    }
}

function getUsuarios($servername, $username, $password, $dbname){
    $conn = new mysqli($servername, $username, $password, $dbname);
    $sql = "SELECT persona_id,nombre,ap_paterno,ap_materno,correo,tipo FROM persona";
    $result = $conn->query($sql);
    $cont=1;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>". $cont ."</td>";
            echo "<td>" . $row["nombre"] . "</td>";
            echo "<td>" . $row["ap_paterno"] . "</td>";
            echo "<td>" . $row["ap_materno"] . "</td>";
            echo "<td>" . $row["correo"] . "</td>";
            echo "<td>" . $row["tipo"] . "</td>";
            echo "<td class='d-flex gap-1'>
                <button class='btn btn-primary btn-editar' name='editar' data-bs-toggle='modal' data-bs-target='#modalEditar' data-id='" . $row["persona_id"] . "' data-nombre='" . $row["nombre"] . "' data-ap-paterno='" . $row["ap_paterno"] . "' data-ap-materno='" . $row["ap_materno"] . "' data-correo='" . $row["correo"] . "' data-tipo='" . $row["tipo"] . "'>Editar</button>
                <form id='" .  $row["persona_id"] . "' method='POST'>
                    <input id='id_" .  $row["persona_id"] . "' name='id' type='hidden' value='" .  $row["persona_id"] . "'>
                    <button type='submit' name='eliminar'  class='btn btn-danger'>Eliminar</button>
                </form>
                
                
                <button id='verCuentasForm' class='btn btn-success' name='verCuentasForm' data-bs-toggle='modal' data-bs-target='#modalverCuentas_" . $row['persona_id'] . "'>Cuentas</button>
                <button id='verCuentasForm' class='btn btn-primary' name='verCuentasForm' data-bs-toggle='modal' data-bs-target='#modalagCuentas_" . $row['persona_id'] . "'>+Cuenta</button>
                </td>";
            echo '<div class="modal fade" id="modalverCuentas_' . $row["persona_id"] . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">VER CUENTAS</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">';
            echo verCuentas($row["persona_id"]);
            echo'
                    </div>
                </div>
            </div>
        </div> ';
        echo '<div class="modal fade" id="modalagCuentas_' . $row["persona_id"] . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">AGREGAR CUENTAS</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">';
        echo'
        <form action="" method="POST" class="row g-3 needs-validation">
                <input name="id" type="hidden" value="' . $row["persona_id"] . '"class="form-control" id="id_p" required>
                <div class="col-md-4">
                    <label for="nombre-editar" class="form-label">Numero de cuenta</label>
                    <input name="numero_cuenta" type="number" class="form-control" id="numero_cuenta" required>
                    <div class="invalid-feedback">
                        Es necesario llenar este campo
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="ap-paterno-editar" class="form-label">Saldo</label>
                    <input name="saldo" type="number" class="form-control" id="saldo" required>
                    <div class="invalid-feedback">
                        Es necesario llenar este campo
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="ap-materno-editar" class="form-label">Tipo</label>
                    <input name="tipo" type="text" class="form-control" id="tipo" required>
                    <div class="invalid-feedback">
                        Es necesario llenar este campo
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" name="registrarCuenta" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
        ';
        echo'
                        </div>
                    </div>
                </div>
            </div> ';
            echo "</tr>";
            $cont++;
        }
    } else {
        echo "No results found.";
    }
    $conn->close();
}

function borrarCuenta(){
    global $servername, $username, $password, $dbname;
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])) {
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        $id = $_POST["id"];
        $sql = "DELETE FROM persona WHERE persona_id = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            echo "Error en la preparación de la consulta: " . $conn->error;
            return;
        }
        
        $stmt->bind_param("i", $id);
        echo var_dump($_POST);
        if ($stmt->execute()) {
            //echo "Registro eliminado exitosamente.";
            header("Location: index.php");
            
        } else {
            echo "Error al eliminar el registro: " . $stmt->error;
            
        }
        $stmt->close();
        $conn->close();
    }
}
borrarCuenta();

function agregarCuenta(){
    global $servername, $username, $password, $dbname;
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["registrarCuenta"]) ) {
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        $numcuenta = $_POST["numero_cuenta"];
        $saldo = $_POST["saldo"];
        $tipo = $_POST["tipo"];
        $id = $_POST["id_p"];
        $sql = "INSERT INTO cuentaBancaria ( numero_cuenta,saldo, persona_id, tipo) VALUES (?, ?, ?, ?) ";
        $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiis", $numcuenta, $saldo,$id,$tipo);
            if ($stmt->execute()) {
                echo '<div class="alert alert-success" role="alert">¡Nuevo registro insertado exitosamente!</div>';
                header("Location: index.php");
            } else {
                echo '<div class="alert alert-danger" role="alert">Error: ' . $stmt->error . '</div>';
            }
            
            $stmt->close();
            $conn->close();
    }
}
agregarCuenta();

function verCuentas($id){
    global $servername, $username, $password, $dbname;
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
            echo "conexion fallida";
        }
        $sql = "SELECT numero_cuenta, saldo, tipo FROM cuentaBancaria WHERE persona_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }
        $stmt->bind_result($numero_cuenta, $saldo, $tipo);
        echo '<div>';  // Open the modal body tag
        while ($stmt->fetch()) {
            echo "Número de cuenta: " . $numero_cuenta . "<br>";
            echo "Saldo: " . $saldo . "<br>";
            echo "Tipo: " . $tipo . "<br><br><hr/>";
        }
        echo '</div>'; // Close the modal body tag
        $stmt->close();
        $conn->close();
    
}


// Función para editar usuario
// Función para editar usuario
function editarUsuario(){
    global $servername, $username, $password, $dbname;
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar"])) {
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        $nombre = $_POST["nombre"];
        $apellido_paterno = $_POST["ap_paterno"];
        $apellido_materno = $_POST["ap_materno"];
        $email = $_POST["correo"];
        $tipo = $_POST["tipo"];
        $id = $_POST["id"]; // Obtener el ID del usuario
        
        $sql = "UPDATE persona SET nombre = ?, ap_paterno = ?, ap_materno = ?, correo = ?, tipo = ? WHERE persona_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nombre, $apellido_paterno, $apellido_materno, $email, $tipo, $id);
        echo var_dump($_POST);
        if ($stmt->execute()) {
            header("Location: index.php");
            //echo '<div class="alert alert-success" role="alert">¡Registro actualizado exitosamente!</div>';
            //echo '<script>window.location.reload();</script>'; 
        } else {
            echo '<div class="alert alert-danger" role="alert">Error: ' . $stmt->error . '</div>';
        }
        
        $stmt->close();
        $conn->close();
    }
}

editarUsuario();

// Configuración de la base de datos
?>

<!-- Aquí comienza el HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Leonel Moises Chauca Maydana</a>
            <div class="d-flex"> <p>Ejercicio 2 </p></div>
        </div>
    </nav>

    <div class="container" style="margin-top: 30px;">
        <form method="post" class="row g-3 needs-validation" style="max-width:800px; margin:30px auto; width:80%" novalidate>
            <div class="col-md-4">
                <label for="validationCustom01" class="form-label">Nombres</label>
                <input name="nombre" type="text" class="form-control" id="validationCustom01" required>
                <div class="invalid-feedback">
                    Es necesario llenar este campo
                </div>
            </div>
            <div class="col-md-4">
                <label for="validationCustom02" class="form-label">Apellido Paterno</label>
                <input name="ap_paterno" type="text" class="form-control" id="validationCustom02" required>
                <div class="invalid-feedback">
                    Es necesario llenar este campo
                </div>
            </div>
            <div class="col-md-4">
                <label  for="validationCustom02" class="form-label">Apellido Materno</label>
                <input name="ap_materno" type="text" class="form-control" id="validationCustom02" required>
                <div class="invalid-feedback">
                    Es necesario llenar este campo
                </div>
            </div>
            <div class="col-md-4">
                <label for="validationCustomUsername" class="form-label">Correo</label>
                <div class="input-group has-validation">
                <span class="input-group-text" id="inputGroupPrepend">@</span>
                <input name="correo" type="email" class="form-control" id="validationCustomUsername" aria-describedby="inputGroupPrepend" required>
                <div class="invalid-feedback">
                    Se necesita un correo válido
                </div>
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" name="registrar" type="submit">Registrar</button>
            </div>
            <?php
                echo insertarRegistro($servername, $username, $password, $dbname);
            ?>
            
        </form>
        <div class="table-responsive" style="width: 97%; max-width:1000px; margin:0 auto">
        <table class="table">
                <thead class="table-primary">
                    <tr>
                        <th>N.</th>
                        <th>Nombre</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>correo</th>
                        <th>tipo</th>
                        <th>Accion</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        echo getUsuarios($servername, $username, $password, $dbname);
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">EDITAR DATOS</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" class="row g-3 needs-validation">
                        <input name="id" type="hidden" class="form-control" id="id" required>
                        <div class="col-md-4">
                            <label for="nombre-editar" class="form-label">Nombres</label>
                            <input name="nombre" type="text" class="form-control" id="nombre-editar" required>
                            <div class="invalid-feedback">
                                Es necesario llenar este campo
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="ap-paterno-editar" class="form-label">Apellido Paterno</label>
                            <input name="ap_paterno" type="text" class="form-control" id="ap-paterno-editar" required>
                            <div class="invalid-feedback">
                                Es necesario llenar este campo
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="ap-materno-editar" class="form-label">Apellido Materno</label>
                            <input name="ap_materno" type="text" class="form-control" id="ap-materno-editar" required>
                            <div class="invalid-feedback">
                                Es necesario llenar este campo
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="correo-editar" class="form-label">Correo</label>
                            <input name="correo" type="email" class="form-control" id="correo-editar" required>
                            <div class="invalid-feedback">
                                Se necesita un correo válido
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo-editar" class="form-label">Tipo</label>
                            <input name="tipo" type="text" class="form-control" id="tipo-editar" required>
                            <div class="invalid-feedback">
                                Se necesita un tipo válido
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" name="editar" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    
    
        
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', (event) => {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    const botonesEditar = document.querySelectorAll('.btn-editar');
    botonesEditar.forEach(boton => {
        boton.addEventListener('click', () => {
            const personaId = boton.getAttribute('data-id');
            const nombre = boton.getAttribute('data-nombre');
            const apepaterno = boton.getAttribute('data-ap-paterno');
            const apematerno = boton.getAttribute('data-ap-materno');
            const correo = boton.getAttribute('data-correo');
            const tipo = boton.getAttribute('data-tipo');
            document.getElementById('id').value = personaId;                
            document.getElementById('nombre-editar').value = nombre;
            document.getElementById('ap-paterno-editar').value = apepaterno;
            document.getElementById('ap-materno-editar').value = apematerno;
            document.getElementById('correo-editar').value = correo;
            document.getElementById('tipo-editar').value = tipo;
        });
    });

    function eliminarUsuario(event) {
        event.preventDefault(); // Prevenir el envío predeterminado del formulario
        // Aquí puedes hacer cualquier otra acción que necesites, como mostrar un mensaje de confirmación
        // o enviar una solicitud AJAX para eliminar el usuario sin recargar la página
    }
</script>
</body>
</html>