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

// Ativa a exibição de erros na tela para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mensagem = "";

// 4. LÓGICA DE CADASTRO DO VEÍCULO (Processa quando o formulário for enviado)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura os dados enviados pelo formulário do veículo
    $placa        = $_POST['placa'] ?? '';
    $chassi       = $_POST['chassi'] ?? '';
    $id_modelo    = $_POST['id_modelo'] ?? ''; 
    $id_categoria = $_POST['id_categoria'] ?? ''; 
    $ano          = $_POST['ano'] ?? ''; 
    $cor          = $_POST['cor'] ?? NULL; // Pode ser vazio (Null | YES)
    $diaria       = $_POST['diaria'] ?? ''; 

    // Validação dos campos obrigatórios conforme seu banco
    if (!empty($placa) && !empty($chassi) && !empty($id_modelo) && !empty($id_categoria) && !empty($ano) && !empty($diaria)) {
        
        // QUERY CORRIGIDA COM OS NOMES EXATOS DAS COLUNAS DO PRINT
        $sql = "INSERT INTO veiculo (id_categoria, id_modelo, placa, chassi, ano, cor, diaria) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // "iisissd" significa: inteiro, inteiro, string, string, inteiro, string, decimal (double)
            $stmt->bind_param("iisissd", $id_categoria, $id_modelo, $placa, $chassi, $ano, $cor, $diaria);
            
            if ($stmt->execute()) {
                // Cadastro feito com sucesso! Redireciona para a listagem da frota
                header("Location: listar.php");
                exit();
            } else {
                // Tratamento para evitar duplicidade (Chave Única de Placa/Chassi)
                if ($conn->errno == 1062) {
                    $mensagem = "Esta Placa ou este Chassi já estão registrados no sistema.";
                } else {
                    $mensagem = "Erro ao cadastrar veículo: " . $stmt->error;
                }
            }
            $stmt->close();
        } else {
            $mensagem = "Erro na estrutura da tabela 'veiculo': " . $conn->error;
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos obrigatórios!";
    }
}

// Busca os modelos e categorias correspondentes para preencher as caixas de seleção
$modelos = $conn->query("SELECT id_modelo, nome FROM modelo_veiculo");
$categorias = $conn->query("SELECT id_categoria, nome FROM categoria_veiculo");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Veículo - LocaGestão</title>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background-color: #f4f6f9; margin: 0; padding: 40px 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; box-sizing: border-box; }
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
        .alert { background-color: #fff5f5; border-left: 4px solid #f56565; color: #c53030; padding: 12px; font-size: 13px; border-radius: 0 8px 8px 0; margin-bottom: 25px; line-height: 1.4; }
    </style>
</head>
<body>

    <div class="card">
        <div class="card-header">
            <span style="font-size: 40px;">🚘</span>
            <h1>Novo Veículo</h1>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert">⚠️ <strong>Aviso do Sistema:</strong><br><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <form method="POST" action="inserir.php">
            <div class="form-group">
                <label>Placa do Veículo *</label>
                <input type="text" name="placa" required class="form-control" placeholder="Ex: ABC1D23">
            </div>

            <div class="form-group">
                <label>Chassi *</label>
                <input type="text" name="chassi" required class="form-control" placeholder="Digite o número do chassi">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ano *</label>
                    <input type="number" name="ano" required class="form-control" placeholder="Ex: 2024">
                </div>
                <div class="form-group">
                    <label>Cor</label>
                    <input type="text" name="cor" class="form-control" placeholder="Ex: Preto">
                </div>
            </div>

            <div class="form-group">
                <label>Valor da Diária (R$) *</label>
                <input type="number" step="0.01" name="diaria" required class="form-control" placeholder="Ex: 120.00">
            </div>

            <div class="form-group">
                <label>Modelo *</label>
                <select name="id_modelo" required class="form-control">
                    <option value="">Selecione o modelo...</option>
                    <?php if($modelos): ?>
                        <?php while($m = $modelos->fetch_assoc()): ?>
                            <option value="<?php echo $m['id_modelo']; ?>"><?php echo htmlspecialchars($m['nome']); ?></option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Categoria *</label>
                <select name="id_categoria" required class="form-control">
                    <option value="">Selecione a categoria...</option>
                    <?php if($categorias): ?>
                        <?php while($c = $categorias->fetch_assoc()): ?>
                            <option value="<?php echo $c['id_categoria']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group" style="padding-top: 10px;">
                <button type="submit" class="btn-submit">Cadastrar Carro</button>
                <a href="../index.php" class="btn-cancel">Voltar para o Painel</a>
            </div>
        </form>
    </div>

</body>
</html>
