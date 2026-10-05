<?php
// 1. INICIA A SESSÃO DO PHP NESSA SUBPASTA
session_start();

// 2. PROTEÇÃO DA PÁGINA: Se não estiver logado, joga para a tela de login na pasta raiz
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

// 3. INCLUI A CONEXÃO VOLTANDO UMA PASTA CORRETAMENTE
require_once("../conexao.php");

// Ativa a exibição de erros na tela para debug se necessário
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 4. BUSCA OS VEÍCULOS NO BANCO FAZENDO JOIN COM MODELO E CATEGORIA
$sql = "SELECT v.id_veiculo, v.placa, v.chassi, v.ano, v.cor, v.diaria, v.status,
               m.nome AS modelo, c.nome AS categoria
        FROM veiculo v
        INNER JOIN modelo_veiculo m ON v.id_modelo = m.id_modelo
        INNER JOIN categoria_veiculo c ON v.id_categoria = c.id_categoria
        ORDER BY v.id_veiculo DESC";

$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frota de Veículos - LocaGestão</title>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px; color: #2d3748; }
        .container { background: white; max-width: 1100px; margin: 0 auto; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header-lista { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; }
        .header-lista h1 { margin: 0; font-size: 26px; color: #1a202c; font-weight: 700; }
        .btn-novo { background: #4c51bf; color: white; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; transition: background 0.2s; }
        .btn-novo:hover { background: #434190; }
        .btn-voltar { background: #edf2f7; color: #4a5568; padding: 12px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; transition: background 0.2s; margin-right: 10px; }
        .btn-voltar:hover { background: #e2e8f0; }
        
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        th { background-color: #f7fafc; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 11px; color: #718096; border-bottom: 2px solid #e2e8f0; }
        td { padding: 15px; border-bottom: 1px solid #edf2f7; color: #4a5568; }
        tr:hover { background-color: #f8fafc; }
        
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; display: inline-block; }
        .status-disponivel { background-color: #c6f6d5; color: #22543d; }
        .status-locado { background-color: #feebc8; color: #744210; }
        .status-manutencao { background-color: #fed7d7; color: #742a2a; }
        
        .sem-dados { text-align: center; padding: 40px; color: #a0aec0; font-size: 16px; font-weight: 500; }
    </style>
</head>
<body>

    <div class="container">
        <div class="header-lista">
            <div>
                <h1>🚘 Frota de Veículos</h1>
            </div>
            <div>
                <a href="../index.php" class="btn-voltar">⬅️ Voltar ao Painel</a>
                <a href="inserir.php" class="btn-novo">➕ Cadastrar Novo</a>
            </div>
        </div>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Modelo</th>
                        <th>Categoria</th>
                        <th>Placa</th>
                        <th>Chassi</th>
                        <th>Ano</th>
                        <th>Cor</th>
                        <th>Diária</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($carro = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $carro['id_veiculo']; ?></strong></td>
                            <td><?php echo htmlspecialchars($carro['modelo']); ?></td>
                            <td><?php echo htmlspecialchars($carro['categoria']); ?></td>
                            <td><span style="font-family: monospace; font-size: 15px; font-weight: 600;"><?php echo htmlspecialchars($carro['placa']); ?></span></td>
                            <td><small style="color: #718096; font-family: monospace;"><?php echo htmlspecialchars($carro['chassi']); ?></small></td>
                            <td><?php echo htmlspecialchars($carro['ano']); ?></td>
                            <td><?php echo htmlspecialchars($carro['cor'] ?? 'Não informada'); ?></td>
                            <td><strong>R$ <?php echo number_format($carro['diaria'], 2, ',', '.'); ?></strong></td>
                            <td>
                                <?php 
                                $status_classe = 'status-disponivel';
                                if (strtolower($carro['status']) == 'locado') { $status_classe = 'status-locado'; }
                                elseif (strtolower($carro['status']) == 'manutencao') { $status_classe = 'status-manutencao'; }
                                ?>
                                <span class="status-badge <?php echo $status_classe; ?>">
                                    <?php echo htmlspecialchars($carro['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="sem-dados">
                <span style="font-size: 48px; display: block; margin-bottom: 10px;">🔍</span>
                Nenhum veículo cadastrado na frota até o momento.
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
<?php $conn->close(); ?>
