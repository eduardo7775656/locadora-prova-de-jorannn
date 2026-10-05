<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: ../login.php"); exit; }
require_once("../conexao.php");
$conn->set_charset("utf8mb4");

// Query com as junções e o nome correto do campo 'data_retirada'
$sql = "SELECT l.id_locacao, l.data_retirada, l.valor_diaria, l.valor_total, l.status,
               c.nome AS cliente, v.placa, m.nome AS modelo 
        FROM locacao l
        INNER JOIN cliente c ON l.id_cliente = c.id_cliente
        INNER JOIN veiculo v ON l.id_veiculo = v.id_veiculo
        INNER JOIN modelo_veiculo m ON v.id_modelo = m.id_modelo
        ORDER BY l.id_locacao DESC";

$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Locações - LocaGestão</title>
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
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .badge-aberta { background: #feebc8; color: #744210; }
        .sem-dados { text-align: center; padding: 40px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-lista">
            <h1>📊 Histórico de Locações</h1>
            <div>
                <a href="../index.php" class="btn-voltar">⬅️ Voltar ao Painel</a>
                <a href="inserir.php" class="btn-novo">➕ Nova Locação</a>
            </div>
        </div>
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Contrato</th>
                        <th>Cliente</th>
                        <th>Veículo / Placa</th>
                        <th>Data Retirada</th>
                        <th>Valor Diária</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($l = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#CON-<?php echo $l['id_locacao']; ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($l['cliente']); ?></strong></td>
                            <td><?php echo htmlspecialchars($l['modelo'] . " [" . $l['placa'] . "]"); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($l['data_retirada'])); ?></td>
                            <td><strong>R$ <?php echo number_format($l['valor_diaria'], 2, ',', '.'); ?></strong></td>
                            <td><span class="badge badge-aberta"><?php echo htmlspecialchars($l['status']); ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="sem-dados">Nenhuma locação registrada até o momento.</div>
        <?php endif; ?>
    </div>
</body>
</html>
