<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "libreria";

// Connessione al database con gestione degli errori
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Gestione delle azioni POST in modo più sicuro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    switch ($action) {
        case "add":
            $titolo = filter_input(INPUT_POST, 'titolo', FILTER_SANITIZE_STRING);
            $autore = filter_input(INPUT_POST, 'autore', FILTER_SANITIZE_STRING);
            $anno_pubblicazione = filter_input(INPUT_POST, 'anno_pubblicazione', FILTER_SANITIZE_NUMBER_INT);
            $genere = filter_input(INPUT_POST, 'genere', FILTER_SANITIZE_STRING);
            $immagine = filter_input(INPUT_POST, 'immagine', FILTER_SANITIZE_URL);

            $stmt = $conn->prepare("INSERT INTO libri (titolo, autore, anno_pubblicazione, genere, immagine) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $titolo, $autore, $anno_pubblicazione, $genere, $immagine);
            $stmt->execute();
            break;
        case "delete":
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $stmt = $conn->prepare("DELETE FROM libri WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            break;
        case "edit":
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $titolo = filter_input(INPUT_POST, 'titolo', FILTER_SANITIZE_STRING);
            $autore = filter_input(INPUT_POST, 'autore', FILTER_SANITIZE_STRING);
            $anno_pubblicazione = filter_input(INPUT_POST, 'anno_pubblicazione', FILTER_SANITIZE_NUMBER_INT);
            $genere = filter_input(INPUT_POST, 'genere', FILTER_SANITIZE_STRING);
            $immagine = filter_input(INPUT_POST, 'immagine', FILTER_SANITIZE_URL);

            $stmt = $conn->prepare("UPDATE libri SET titolo=?, autore=?, anno_pubblicazione=?, genere=?, immagine=? WHERE id=?");
            $stmt->bind_param("ssissi", $titolo, $autore, $anno_pubblicazione, $genere, $immagine, $id);
            $stmt->execute();
            break;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Libreria</title>
    <!-- Tailwind CSS Link per un design moderno e responsive -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="navbar top-0 fixed z-50 w-full p-4 bg-blue-500 hover:bg-pink-500 text-white shadow-lg transition-colors duration-300">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">Damiano Dell'Amore's Library</h1>
        <button onclick="openModal('addBookModal')" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">
            Aggiungi Libro
        </button>
    </div>
</div>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-xl font-bold mb-4">Libreria personale di Damiano Dell'Amore</h1>
        

        <!-- Modale Aggiungi Libro -->
        <div id="addBookModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-white rounded-lg p-8 m-4 max-w-lg mx-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Aggiungi un nuovo libro</h2>
                        <button onclick="closeModal('addBookModal')" class="text-gray-600 hover:text-gray-800">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                    <!-- Form per aggiungere un nuovo libro -->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <input type="hidden" name="action" value="add">
                        <!-- Campi del form -->
                        <div class="mb-4">
                            <label for="titolo" class="block mb-2">Titolo:</label>
                            <input type="text" id="titolo" name="titolo" required class="border rounded p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="autore" class="block mb-2">Autore:</label>
                            <input type="text" id="autore" name="autore" required class="border rounded p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="anno_pubblicazione" class="block mb-2">Anno di Pubblicazione:</label>
                            <input type="number" id="anno_pubblicazione" name="anno_pubblicazione" class="border rounded p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="genere" class="block mb-2">Genere:</label>
                            <input type="text" id="genere" name="genere" class="border rounded p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="immagine" class="block mb-2">Percorso Immagine:</label>
                            <input type="text" id="immagine" name="immagine" class="border rounded p-2 w-full" placeholder="URL dell'immagine">
                        </div>
                        <!-- Bottoni del form -->
                        <div class="flex justify-end">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Salva
                            </button>
                            <button type="button" onclick="closeModal('addBookModal')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Annulla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Modifica Libro -->
        <div id="editBookModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-white rounded-lg p-8 m-4 max-w-lg mx-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold">Modifica libro</h2>
                        <button onclick="closeModal('editBookModal')" class="text-gray-600 hover:text-gray-800">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>
                    <!-- Form per modificare il libro -->
                    <form id="editBookForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="editBookId">
                        <!-- Campi del form -->
                        <div class="mb-4">
                            <label for="editTitolo" class="block mb-2">Titolo:</label>
                            <input type="text" id="editTitolo" name="titolo" required class="border rounded p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="editAutore" class="block mb-2">Autore:</label>
                            <input type="text" id="editAutore" name="autore" required class="border rounded p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="editAnnoPubblicazione" class="block mb-2">Anno di Pubblicazione:</label>
                            <input type="number" id="editAnnoPubblicazione" name="anno_pubblicazione" class="border rounded p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="editGenere" class="block mb-2">Genere:</label>
                            <input type="text" id="editGenere" name="genere" class="border rounded p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="editImmagine" class="block mb-2">Percorso Immagine:</label>
                            <input type="text" id="editImmagine" name="immagine" class="border rounded p-2 w-full" placeholder="URL dell'immagine">
                        </div>
                        <!-- Bottoni del form -->
                        <div class="flex justify-end">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Salva
                            </button>
                            <button type="button" onclick="closeModal('editBookModal')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Annulla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if ($result = $conn->query("SELECT * FROM libri")) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="bg-white p-6 rounded-lg shadow-lg relative">
                        <?php if (!empty($row["immagine"])) : ?>
                            <img src="<?= htmlspecialchars($row["immagine"]) ?>" alt="Copertina di <?= htmlspecialchars($row["titolo"]) ?>" class="rounded w-full mb-4">
                        <?php endif; ?>
                        <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($row["titolo"]) ?></h2>
                        <p class="text-gray-700 mb-2">Autore: <?= htmlspecialchars($row["autore"]) ?></p>
                        <p class="text-gray-700 mb-2">Anno: <?= htmlspecialchars($row["anno_pubblicazione"]) ?></p>
                        <p class="text-gray-700">Genere: <?= htmlspecialchars($row["genere"]) ?></p>
                        <!-- Icone di modifica ed eliminazione -->
                        <div class="absolute bottom-0 right-0 mb-4 mr-4 flex space-x-2">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return confirm('Sei sicuro di voler eliminare questo libro?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                            <button onclick="editBook(<?= $row['id']; ?>)" class="text-blue-500 hover:text-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else : ?>
                <p>Nessun libro trovato.</p>
            <?php endif; ?>
        </div>

    </div>
    <!-- Footer -->
 <!-- Footer migliorato con Tailwind CSS -->
 <footer class="bg-blue-500 hover:bg-pink-500 text-white text-center p-4 absolute bottom-0 w-full transition-colors duration-300">
    &copy; 2024 Damiano Dell'Amore. Tutti i diritti riservati.
</footer>


    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }


        function editBook(id) {
            // Effettua una chiamata AJAX per ottenere i dettagli del libro dal server
            fetch(`get_book_details.php?id=${id}`)
                .then(response => response.json())
                .then(book => {
                    // Popola i campi del form nel modal
                    document.getElementById('editBookId').value = book.id;
                    document.getElementById('editTitolo').value = book.titolo;
                    document.getElementById('editAutore').value = book.autore;
                    document.getElementById('editAnnoPubblicazione').value = book.anno_pubblicazione;
                    document.getElementById('editGenere').value = book.genere;
                    document.getElementById('editImmagine').value = book.immagine;

                    // Apri il modal di modifica
                    openModal('editBookModal');
                })
                .catch(error => console.error('Errore durante il recupero dei dettagli del libro:', error));
        }
    </script>

</body>

</html>