<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bdleonel";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexion fallida : " . $conn->connect_error);
    }
    $nombre = $_POST["nombre"];
    $apellido_paterno = $_POST["ap_paterno"];
    $apellido_materno = $_POST["ap_materno"];
    $email = $_POST["correo"];
    
    $sql = "INSERT INTO persona (nombre, ap_paterno, ap_materno, correo,tipo) VALUES (?, ?, ?, ?,'Cliente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $apellido_paterno, $apellido_materno, $email);
    if ($stmt->execute()) {
        echo "¡Nuevo registro insertado exitosamente!";
        } else {
        echo "Error: " . $stmt->error;
        }
    $stmt->close();
    $conn->close();
?>