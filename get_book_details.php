<?php
// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "libreria";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verifica se l'ID del libro è stato fornito nella richiesta GET
if(isset($_GET['id'])) {
    // Sanitizzazione dell'input per prevenire SQL injection
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Esecuzione della query per ottenere i dettagli del libro
    $query = "SELECT * FROM libri WHERE id = $id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Estrai i dettagli del libro
        $row = $result->fetch_assoc();

        // Restituisci i dettagli del libro come JSON
        echo json_encode($row);
    } else {
        // Se non viene trovato nessun libro con l'ID fornito, restituisci un messaggio di errore
        echo json_encode(array("error" => "Nessun libro trovato con l'ID fornito"));
    }
} else {
    // Se l'ID del libro non è stato fornito nella richiesta GET, restituisci un messaggio di errore
    echo json_encode(array("error" => "ID del libro non fornito nella richiesta"));
}

// Chiudi la connessione al database
$conn->close();
?>
