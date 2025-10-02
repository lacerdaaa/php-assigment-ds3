
<?php

$pageTitle = "Projeto PHP";


$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "db_aula";       

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
-
$sql = "SELECT id, nome, email FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1><?php echo $pageTitle; ?></h1>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<thead><tr><th>ID</th><th>Nome</th><th>E-mail</th></tr></thead>";
        echo "<tbody>";

        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["nome"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p class='no-results'>Nenhum resultado encontrado.</p>";
    }

    $conn->close();
    ?>
</div>
</body>
</html>
