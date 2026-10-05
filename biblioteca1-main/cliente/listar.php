<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: ../login.php"); exit; }
require_once("../conexao.php");
$conn->set_charset("utf8mb4");

// Busca apenas as colunas corretas existentes no banco
$sql = "SELECT id_cliente, nome, cpf, data_nascimento, telefone, email, cidade FROM cliente ORDER BY id_cliente DESC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerir Clientes - LocaGestão</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px; color: #2d3748; }
        .container { background: white; max-width: 1100px; margin: 0 auto; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header-lista { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; }
        .header-lista h1 { margin: 0; font-size: 26px; color: #1a202c; font-weight: 700; }
        .btn-novo { background: #4c51bf; color: white; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; }
        .btn-voltar { background: #edf2f7; color: #4a5568; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; margin-right: 10px; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        th { background-color: #f7fafc; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 11px; color: #718096; border-bottom: 2px solid #e2e8f0; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; color: #4a5568; }
        tr:hover { background-color: #f8fafc; }
        .sem-dados { text-align: center; padding: 40px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-lista">
            <h1>👥 Gerir Clientes</h1>
            <div>
                <a href="../index.php" class="btn-voltar">⬅️ Voltar ao Painel</a>
                <a href="inserir.php" class="btn-novo">➕ Novo Cliente</a>
            </div>
        </div>
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Nascimento</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Cidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $c['id_cliente']; ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($c['nome']); ?></strong></td>
                            <td><?php echo htmlspecialchars($c['cpf']); ?></td>
                            <td><?php echo $c['data_nascimento'] ? date('d/m/Y', strtotime($c['data_nascimento'])) : '---'; ?></td>
                            <td><?php echo htmlspecialchars($c['telefone'] ?? '---'); ?></td>
                            <td><?php echo htmlspecialchars($c['email'] ?? '---'); ?></td>
                            <td><?php echo htmlspecialchars($c['cidade'] ?? '---'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="sem-dados">Nenhum cliente cadastrado no momento.</div>
        <?php endif; ?>
    </div>
</body>
</html>
