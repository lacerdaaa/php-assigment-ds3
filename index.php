
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

$feedback = [
    'type' => '',
    'message' => ''
];

$formId = null;
$formNome = '';
$formEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($action === 'create' || $action === 'update') {
        if ($nome === '' || $email === '') {
            $feedback = [
                'type' => 'error',
                'message' => 'Informe nome e e-mail.'
            ];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $feedback = [
                'type' => 'error',
                'message' => 'Digite um e-mail válido.'
            ];
        }

        $formNome = $nome;
        $formEmail = $email;
    }

    if ($action === 'create' && $feedback['type'] !== 'error') {
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $email);

        if ($stmt->execute()) {
            $feedback = [
                'type' => 'success',
                'message' => 'Usuário cadastrado com sucesso.'
            ];
            $formNome = '';
            $formEmail = '';
        } else {
            $feedback = [
                'type' => 'error',
                'message' => 'Erro ao cadastrar: ' . $conn->error
            ];
        }

        $stmt->close();
    }

    if ($action === 'update') {
        $formId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($formId <= 0 && $feedback['type'] !== 'error') {
            $feedback = [
                'type' => 'error',
                'message' => 'Usuário inválido para atualizar.'
            ];
        }

        if ($feedback['type'] !== 'error') {
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $email, $formId);

            if ($stmt->execute()) {
                $feedback = [
                    'type' => 'success',
                    'message' => 'Usuário atualizado com sucesso.'
                ];
                $formId = null;
                $formNome = '';
                $formEmail = '';
            } else {
                $feedback = [
                    'type' => 'error',
                    'message' => 'Erro ao atualizar: ' . $conn->error
                ];
            }

            $stmt->close();
        }
    }

    if ($action === 'delete') {
        $deleteId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($deleteId <= 0) {
            $feedback = [
                'type' => 'error',
                'message' => 'Usuário inválido para excluir.'
            ];
        } else {
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $deleteId);

            if ($stmt->execute()) {
                $feedback = [
                    'type' => 'success',
                    'message' => 'Usuário excluído com sucesso.'
                ];
            } else {
                $feedback = [
                    'type' => 'error',
                    'message' => 'Erro ao excluir: ' . $conn->error
                ];
            }

            $stmt->close();
        }
    }
}

if ($formId === null && isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];

    if ($editId > 0) {
        $stmt = $conn->prepare("SELECT id, nome, email FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $editId);

        if ($stmt->execute()) {
            $resultEdit = $stmt->get_result();

            if ($resultEdit->num_rows > 0) {
                $rowEdit = $resultEdit->fetch_assoc();
                $formId = $rowEdit['id'];
                $formNome = $rowEdit['nome'];
                $formEmail = $rowEdit['email'];
            } else {
                $feedback = [
                    'type' => 'error',
                    'message' => 'Usuário não encontrado.'
                ];
            }
        }

        $stmt->close();
    }
}

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

    <?php if ($feedback['message'] !== ''): ?>
        <div class="feedback <?php echo $feedback['type']; ?>">
            <?php echo htmlspecialchars($feedback['message']); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="user-form">
        <h2><?php echo $formId ? 'Editar usuário' : 'Novo usuário'; ?></h2>
        <?php if ($formId): ?>
            <input type="hidden" name="id" value="<?php echo $formId; ?>">
        <?php endif; ?>
        <input type="hidden" name="action" value="<?php echo $formId ? 'update' : 'create'; ?>">

        <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($formNome); ?>" placeholder="Maria da Silva">
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formEmail); ?>" placeholder="email@email.com">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary">
                <?php echo $formId ? 'Atualizar' : 'Cadastrar'; ?>
            </button>
            <?php if ($formId): ?>
                <a href="index.php" class="btn secondary">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row["id"]; ?></td>
                        <td><?php echo htmlspecialchars($row["nome"]); ?></td>
                        <td><?php echo htmlspecialchars($row["email"]); ?></td>
                        <td class="actions">
                            <a href="?edit=<?php echo $row["id"]; ?>" class="btn small">Editar</a>
                            <form method="post" class="inline-form" onsubmit="return confirm('Deseja excluir este usuário?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                                <button type="submit" class="btn danger small">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-results">Nenhum resultado encontrado.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</div>
</body>
</html>
