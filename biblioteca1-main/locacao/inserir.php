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

$mensagem = "";

// 4. LÓGICA DE CADASTRO DO CONTRATO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cliente    = $_POST['id_cliente'] ?? '';
    $id_veiculo    = $_POST['id_veiculo'] ?? '';
    $data_retirada = $_POST['data_retirada'] ?? '';
    $valor_diaria  = $_POST['valor_diaria'] ?? '';
    $valor_total   = $_POST['valor_total'] ?? NULL;
    $observacoes   = $_POST['observacoes'] ?? NULL;
    
    // Pega o ID do usuário funcionário que está logado no sistema
    $id_usuario    = $_SESSION['usuario_id']; 

    // Validação dos campos obrigatórios conforme seu banco de dados
    if (!empty($id_cliente) && !empty($id_veiculo) && !empty($data_retirada) && !empty($valor_diaria)) {
        
        // Inicia transação para garantir que os dois comandos aconteçam juntos com segurança
        $conn->begin_transaction();

        try {
            // 1. Insere o contrato de locação com as colunas exatas do print
            $sql = "INSERT INTO locacao (id_cliente, id_veiculo, id_usuario, data_retirada, valor_diaria, valor_total, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            // "iiisdds" significa: int, int, int, string, double, double, string
            $stmt->bind_param("iiisdds", $id_cliente, $id_veiculo, $id_usuario, $data_retirada, $valor_diaria, $valor_total, $observacoes);
            $stmt->execute();

            // 2. Atualiza o status do veículo escolhido para 'locado' no banco de dados
            $sql_veiculo = "UPDATE veiculo SET status = 'locado' WHERE id_veiculo = ?";
            $stmt_v = $conn->prepare($sql_veiculo);
            $stmt_v->bind_param("i", $id_veiculo);
            $stmt_v->execute();

            // Confirma todas as alterações no banco de dados
            $conn->commit();
            header("Location: listar.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback(); // Cancela tudo em caso de qualquer falha
            $mensagem = "Erro ao processar locação: " . $e->getMessage();
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos obrigatórios!";
    }
}

// Carrega os clientes e apenas os carros que estão com status 'disponivel'
$clientes = $conn->query("SELECT id_cliente, nome FROM cliente ORDER BY nome ASC");
$veiculos = $conn->query("SELECT v.id_veiculo, v.placa, v.diaria, m.nome AS modelo FROM veiculo v INNER JOIN modelo_veiculo m ON v.id_modelo = m.id_modelo WHERE v.status = 'disponivel'");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Locação - LocaGestão</title>
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
        .btn-submit { width: 100%; padding: 14px; background: #4c51bf; color: white; border: none; font-weight: 600; font-size: 15px; border-radius: 10px; cursor: pointer; }
        .btn-cancel { display: block; text-align: center; width: 100%; padding: 14px; background: #edf2f7; color: #4a5568; font-weight: 500; font-size: 14px; text-decoration: none; border-radius: 10px; margin-top: 12px; }
        .alert { background-color: #fff5f5; border-left: 4px solid #f56565; color: #c53030; padding: 12px; font-size: 13px; border-radius: 0 8px 8px 0; margin-bottom: 25px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header"><span style="font-size: 40px;">📊</span><h1>Nova Locação</h1></div>
        <?php if (!empty($mensagem)): ?><div class="alert">⚠️ <?php echo htmlspecialchars($mensagem); ?></div><?php endif; ?>
        
        <form method="POST" action="inserir.php">
            <div class="form-group">
                <label>Selecionar Cliente *</label>
                <select name="id_cliente" required class="form-control">
                    <option value="">Escolha o cliente...</option>
                    <?php if($clientes): ?>
                        <?php while($c = $clientes->fetch_assoc()): ?>
                            <option value="<?php echo $c['id_cliente']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Selecionar Carro Disponível *</label>
                <select name="id_veiculo" required class="form-control">
                    <option value="">Escolha o veículo...</option>
                    <?php if($veiculos): ?>
                        <?php while($v = $veiculos->fetch_assoc()): ?>
                            <option value="<?php echo $v['id_veiculo']; ?>">
                                <?php echo htmlspecialchars($v['modelo'] . " [" . $v['placa'] . "] - R$ " . number_format($v['diaria'], 2, ',', '.')); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Data de Retirada *</label>
                <input type="date" name="data_retirada" required class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Valor da Diária (R$) *</label>
                    <input type="number" step="0.01" name="valor_diaria" required class="form-control" placeholder="Ex: 150.00">
                </div>
                <div class="form-group">
                    <label>Valor Total Contrato</label>
                    <input type="number" step="0.01" name="valor_total" class="form-control" placeholder="Ex: 450.00 (Opcional)">
                </div>
            </div>

            <div class="form-group">
                <label>Observações</label>
                <textarea name="observacoes" class="form-control" rows="2" placeholder="Ex: Devolver com tanque cheio..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Fechar Contrato de Locação</button>
            <a href="../index.php" class="btn-cancel">Voltar para o Painel</a>
        </form>
    </div>
</body>
</html>
