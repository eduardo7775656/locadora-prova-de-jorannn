<?php
// 1. INICIA A SESSÃO DO PHP
session_start();

// Proteção da página: se não estiver logado, chuta de volta para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - LocaGestão</title>
    <style>
        /* Estilos locais embutidos para garantir o visual moderno sem depender da internet */
        body { font-family: 'Segoe UI', system-ui, sans-serif; background-color: #f4f6f9; margin: 0; display: flex; min-height: 100vh; color: #2d3748; }
        aside { width: 260px; background-color: #1a202c; color: white; display: flex; flex-direction: column; box-shadow: 4px 0 10px rgba(0,0,0,0.05); }
        .logo { padding: 24px; font-size: 20px; font-weight: bold; border-bottom: 1px solid #2d3748; display: flex; align-items: center; gap: 10px; background: #111827; }
        nav { flex: 1; padding: 20px; }
        nav ul { list-style: none; padding: 0; margin: 0; }
        nav li { margin-bottom: 8px; }
        nav a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #cbd5e0; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.2s; }
        nav a:hover { background-color: #2d3748; color: white; }
        .btn-logout { padding: 20px; border-top: 1px solid #2d3748; }
        .btn-logout a { color: #f56565; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; transition: background 0.2s; }
        .btn-logout a:hover { background: #2d2222; }
        main { flex: 1; padding: 40px; box-sizing: border-box; }
        header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px; }
        header h1 { margin: 0; font-size: 28px; color: #1a202c; font-weight: 700; }
        header p { margin: 5px 0 0 0; color: #718096; font-size: 14px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #a0aec0; letter-spacing: 0.5px; margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; }
        .card { background: white; padding: 24px; border-radius: 14px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .card h3 { margin: 3px 0 0 0; font-size: 22px; color: #1a202c; font-weight: 700; }
        .card p { margin: 0; font-size: 11px; color: #a0aec0; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .actions { display: flex; gap: 8px; }
        .btn-primary { background: #4c51bf; color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; transition: background 0.2s; box-shadow: 0 2px 4px rgba(76, 81, 191, 0.1); }
        .btn-primary:hover { background: #434190; }
        .btn-secondary { background: #edf2f7; color: #4a5568; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; transition: background 0.2s; }
        .btn-secondary:hover { background: #e2e8f0; }
    </style>
</head>
<body>

    <!-- BARRA LATERAL DE NAVEGAÇÃO -->
    <aside>
        <div class="logo">🚗 <span>LocaGestão</span></div>
        <nav>
            <ul>
                <li><a href="veiculo/listar.php">🚘 Frota de Veículos</a></li>
                <li><a href="cliente/listar.php">👥 Gerir Clientes</a></li>
                <li><a href="locacao/listar.php">📊 Histórico Locações</a></li>
            </ul>
        </nav>
        <div class="btn-logout">
            <a href="logout.php">🚪 Sair / Logout</a>
        </div>
    </aside>

    <!-- CONTEÚDO PRINCIPAL DO PAINEL -->
    <main>
        <header>
            <div>
                <h1>Painel de Controle</h1>
                <!-- AJUSTE AQUI: Mudado para 'usuario_nome' combinando certinho com o login.php -->
                <p>Bem-vindo de volta, <strong style="color: #4c51bf;"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'guigui'); ?></strong>!</p>
            </div>
            <div style="background: white; padding: 12px 18px; border-radius: 10px; border: 1px solid #e2e8f0; font-weight: 600; color: #4a5568; font-size: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                📅 <?php echo date('d/m/Y'); ?>
            </div>
        </header>

        <div class="section-title">Acesso Rápido aos Módulos</div>

        <!-- CARDS DOS MÓDULOS -->
        <section class="grid">
            
            <!-- CARD: VEÍCULOS -->
            <div class="card">
                <div>
                    <p>Frota</p>
                    <h3>Veículos</h3>
                </div>
                <div class="actions">
                    <a href="veiculo/inserir.php" class="btn-primary">➕ Novo</a>
                    <a href="veiculo/listar.php" class="btn-secondary">👁️ Ver Frota</a>
                </div>
            </div>

            <!-- CARD: CLIENTES -->
            <div class="card">
                <div>
                    <p>Módulo</p>
                    <h3>Clientes</h3>
                </div>
                <div class="actions">
                    <a href="cliente/inserir.php" class="btn-primary">➕ Novo</a>
                    <a href="cliente/listar.php" class="btn-secondary">👁️ Ver Todos</a>
                </div>
            </div>

            <!-- CARD: LOCAÇÕES -->
            <div class="card">
                <div>
                    <p>Contratos</p>
                    <h3>Locações</h3>
                </div>
                <div class="actions">
                    <a href="locacao/inserir.php" class="btn-primary">➕ Nova</a>
                    <a href="locacao/listar.php" class="btn-secondary">👁️ Ver Relatório</a>
                </div>
            </div>

        </section>
    </main>

</body>
</html>
