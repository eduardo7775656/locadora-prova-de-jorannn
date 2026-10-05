<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: ../login.php"); exit; }
require_once("../conexao.php");

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome            = $_POST['nome'] ?? '';
    $cpf             = $_POST['cpf'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? NULL;
    $telefone        = $_POST['telefone'] ?? NULL;
    $email           = $_POST['email'] ?? NULL;
    $cidade          = $_POST['cidade'] ?? NULL;

    if (!empty($nome) && !empty($cpf)) {
        // QUERY CORRIGIDA: Usa apenas as colunas reais do seu banco de dados
        $sql = "INSERT INTO cliente (nome, cpf, data_nascimento, telefone, email, cidade) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssss", $nome, $cpf, $data_nascimento, $telefone, $email, $cidade);
            if ($stmt->execute()) {
                header("Location: listar.php");
                exit();
            } else {
                if ($conn->errno == 1062) {
                    $mensagem = "Erro: Este CPF já está cadastrado no sistema.";
                } else {
                    $mensagem = "Erro ao cadastrar cliente: " . $stmt->error;
                }
            }
            $stmt->close();
        } else {
            $mensagem = "Erro na estrutura da tabela 'cliente': " . $conn->error;
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos obrigatórios (Nome e CPF)!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Cliente - LocaGestão</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; box-sizing: border-box; }
        .card { background: white; width: 100%; max-width: 460px; padding: 35px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; box-sizing: border-box; }
        .card-header { text-align: center; margin-bottom: 25px; }
        .card-header h1 { font-size: 24px; color: #1a202c; margin: 10px 0 0 0; font-weight: 700; }
        .form-group { margin-bottom: 18px; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        .form-group label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #4a5568; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px; background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; color: #2d3748; outline: none; box-sizing: border-box; }
        .form-control:focus { border-color: #4c51bf; background: white; }
        .btn-submit { width: 100%; padding: 14px; background: #4c51bf; color: white; border: none; font-weight: 600; font-size: 15px; border-radius: 10px; cursor: pointer; }
        .btn-cancel { display: block; text-align: center; width: 100%; padding: 14px; background: #edf2f7; color: #4a5568; font-weight: 500; font-size: 14px; text-decoration: none; border-radius: 10px; box-sizing: border-box; margin-top: 12px; }
        .alert { background-color: #fff5f5; border-left: 4px solid #f56565; color: #c53030; padding: 12px; font-size: 13px; border-radius: 0 8px 8px 0; margin-bottom: 25px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header"><span style="font-size: 40px;">👥</span><h1>Novo Cliente</h1></div>
        <?php if (!empty($mensagem)): ?><div class="alert">⚠️ <?php echo htmlspecialchars($mensagem); ?></div><?php endif; ?>
        
        <form method="POST" action="inserir.php">
            <div class="form-group">
                <label>Nome Completo *</label>
                <input type="text" name="nome" required class="form-control" placeholder="Digite o nome completo">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>CPF *</label>
                    <input type="text" name="cpf" required class="form-control" placeholder="000.000.000-00">
                </div>
                <div class="form-group">
                    <label>Data de Nascimento</label>
                    <input type="date" name="data_nascimento" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label>Telefone</label>
                <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000">
            </div>
            
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" class="form-control" placeholder="exemplo@email.com">
            </div>

            <div class="form-group">
                <label>Cidade</label>
                <input type="text" name="cidade" class="form-control" placeholder="Digite a cidade">
            </div>

            <button type="submit" class="btn-submit">Cadastrar Cliente</button>
            <a href="../index.php" class="btn-cancel">Voltar para o Painel</a>
        </form>
    </div>
</body>
</html>
