<?php
// index.php
require_once __DIR__ . '/models/Categoria.php';
require_once __DIR__ . '/models/Livro.php';

$mensagem = "";

// Processar formulário de novo livro via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar_livro') {
    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $isbn = trim($_POST['isbn']);
    $ano = (int)$_POST['ano'];
    $categoriaId = (int)$_POST['categoria_id'];

    if (!empty($titulo) && !empty($autor) && !empty($isbn) && $categoriaId > 0) {
        $livro = new Livro($titulo, $autor, $isbn, $ano, $categoriaId);
        if ($livro->salvar()) {
            $mensagem = "<p style='color: green; font-weight: bold;'>Livro cadastrado com sucesso!</p>";
        } else {
            $mensagem = "<p style='color: red;'>Erro ao cadastrar o livro.</p>";
        }
    } else {
        $mensagem = "<p style='color: red;'>Por favor, preencha todos os campos corretamente.</p>";
    }
}

// Carregar dados atualizados do banco
$categorias = Categoria::listarTodas();
$livros = Livro::listarTodos();

include __DIR__ . '/views/header.php';
?>

<?php if (!empty($mensagem)) echo "<div class='card'>{$mensagem}</div>"; ?>

<!-- Formulário Visual de Cadastro de Livro -->
<section class="card">
    <h2>Cadastrar Novo Livro</h2>
    <form action="index.php" method="POST">
        <input type="hidden" name="acao" value="cadastrar_livro">
        
        <div class="form-group">
            <label for="titulo">Título do Livro:</label>
            <input type="text" id="titulo" name="titulo" required placeholder="Ex: Clean Code">
        </div>

        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex: 1;">
                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" required placeholder="Ex: Robert C. Martin">
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="isbn">ISBN:</label>
                <input type="text" id="isbn" name="isbn" required placeholder="Ex: 978-0132350884">
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex: 1;">
                <label for="ano">Ano de Publicação:</label>
                <input type="number" id="ano" name="ano" required value="2024">
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="categoria_id">Categoria:</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione uma Categoria</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>">
                            <?php echo htmlspecialchars($cat['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn">Cadastrar Livro</button>
    </form>
</section>

<!-- Tabela Visual do Acervo de Livros -->
<section class="card">
    <h2>Acervo de Livros</h2>
    <?php if (count($livros) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>ISBN</th>
                    <th>Categoria</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($livros as $l): ?>
                    <tr>
                        <td><?php echo $l['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($l['titulo']); ?></strong></td>
                        <td><?php echo htmlspecialchars($l['autor']); ?></td>
                        <td><?php echo htmlspecialchars($l['isbn']); ?></td>
                        <td><?php echo htmlspecialchars($l['categoria_nome'] ?? 'Sem Categoria'); ?></td>
                        <td>
                            <?php if ($l['disponivel']): ?>
                                <span class="badge badge-disponivel">Disponível</span>
                            <?php else: ?>
                                <span class="badge badge-indisponivel">Emprestado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum livro cadastrado até o momento.</p>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/views/footer.php'; ?>