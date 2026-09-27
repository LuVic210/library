<?php
// views/emprestimos.php
require_once __DIR__ . '/../models/Emprestimo.php';
require_once __DIR__ . '/../models/Livro.php';
require_once __DIR__ . '/../config/Database.php';

$mensagem = "";
$db = Database::getConexao();

// Ação de Devolução
if (isset($_GET['acao']) && $_GET['acao'] === 'devolver' && isset($_GET['id']) && isset($_GET['livro_id'])) {
    if (Emprestimo::devolver((int)$_GET['id'], (int)$_GET['livro_id'])) {
        $mensagem = "<p style='color: green; font-weight: bold;'>Devolução registrada com sucesso!</p>";
    } else {
        $mensagem = "<p style='color: red;'>Erro ao registrar devolução.</p>";
    }
}

// Ação de Novo Empréstimo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'novo_emprestimo') {
    $usuarioId = (int)$_POST['usuario_id'];
    $livroId = (int)$_POST['livro_id'];

    if ($usuarioId > 0 && $livroId > 0) {
        $emprestimo = new Emprestimo($usuarioId, $livroId);
        if ($emprestimo->registrar()) {
            $mensagem = "<p style='color: green; font-weight: bold;'>Empréstimo realizado com sucesso!</p>";
        } else {
            $mensagem = "<p style='color: red;'>Erro ao realizar empréstimo.</p>";
        }
    } else {
        $mensagem = "<p style='color: red;'>Selecione o usuário e o livro.</p>";
    }
}

// Buscar dados
$usuarios = $db->query("SELECT id, nome FROM usuarios ORDER BY nome ASC")->fetchAll();
$livrosDisponiveis = $db->query("SELECT id, titulo FROM livros WHERE disponivel = 1 ORDER BY titulo ASC")->fetchAll();
$historico = Emprestimo::listarTodos();

include __DIR__ . '/header.php';
?>

<?php if (!empty($mensagem)) echo "<div class='card'>{$mensagem}</div>"; ?>

<!-- Form de Empréstimo -->
<section class="card">
    <h2>Registrar Empréstimo de Livro</h2>
    <form action="emprestimos.php" method="POST">
        <input type="hidden" name="acao" value="novo_emprestimo">

        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex: 1;">
                <label for="usuario_id">Usuário / Leitor:</label>
                <select id="usuario_id" name="usuario_id" required>
                    <option value="">Selecione um Leitor</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="livro_id">Livro (Apenas Disponíveis):</label>
                <select id="livro_id" name="livro_id" required>
                    <option value="">Selecione uma Obra</option>
                    <?php foreach ($livrosDisponiveis as $l): ?>
                        <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['titulo']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn">Confirmar Empréstimo</button>
    </form>
</section>

<!-- Tabela do Histórico -->
<section class="card">
    <h2>Histórico de Empréstimos</h2>
    <?php if (count($historico) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Leitor</th>
                    <th>Livro</th>
                    <th>Data Retirada</th>
                    <th>Devolução Prevista</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historico as $e): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($e['usuario_nome']); ?></td>
                        <td><strong><?php echo htmlspecialchars($e['livro_titulo']); ?></strong></td>
                        <td><?php echo date('d/m/Y', strtotime($e['data_emprestimo'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($e['data_devolucao_prevista'])); ?></td>
                        <td>
                            <?php if ($e['status'] === 'ATIVO'): ?>
                                <span class="badge badge-indisponivel">Em Aberto</span>
                            <?php else: ?>
                                <span class="badge badge-disponivel">Devolvido</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($e['status'] === 'ATIVO'): ?>
                                <a href="emprestimos.php?acao=devolver&id=<?php echo $e['id']; ?>&livro_id=<?php echo $e['livro_id']; ?>" 
                                   class="btn" style="background-color: #27ae60; text-decoration: none; padding: 4px 8px; font-size: 0.85rem;">
                                   Dar Baixa
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum empréstimo registrado.</p>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/footer.php'; ?>