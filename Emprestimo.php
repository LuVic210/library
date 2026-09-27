<?php
// models/Emprestimo.php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Livro.php';

class Emprestimo {
    private $id;
    private $usuarioId;
    private $livroId;
    private $dataEmprestimo;
    private $dataDevolucaoPrevista;
    private $dataDevolucaoReal;
    private $status;
    private $db;

    public function __construct($usuarioId = null, $livroId = null, $diasPrazo = 14) {
        $this->usuarioId = $usuarioId;
        $this->livroId = $livroId;
        $this->dataEmprestimo = date('Y-m-d');
        $this->dataDevolucaoPrevista = date('Y-m-d', strtotime("+$diasPrazo days"));
        $this->status = 'ATIVO';
        $this->db = Database::getConexao();
    }

    // Regra de Negócio: Registrar Empréstimo
    public function registrar() {
        try {
            $this->db->beginTransaction();

            // 1. Inserir o empréstimo
            $sql = "INSERT INTO emprestimos (usuario_id, livro_id, data_emprestimo, data_devolucao_prevista, status) 
                    VALUES (:usuario_id, :livro_id, :data_emprestimo, :data_prevista, :status)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $this->usuarioId,
                ':livro_id' => $this->livroId,
                ':data_emprestimo' => $this->dataEmprestimo,
                ':data_prevista' => $this->dataDevolucaoPrevista,
                ':status' => $this->status
            ]);

            // 2. Atualizar status do livro para INDISPONÍVEL (0)
            Livro::atualizarStatus($this->livroId, 0);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Regra de Negócio: Dar baixa / Devolver Livro
    public static function devolver($emprestimoId, $livroId) {
        $db = Database::getConexao();
        try {
            $db->beginTransaction();

            // 1. Atualizar o empréstimo com a data real de devolução e status
            $dataHoje = date('Y-m-d');
            $sql = "UPDATE emprestimos 
                    SET data_devolucao_real = :data_real, status = 'DEVOLVIDO' 
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':data_real' => $dataHoje,
                ':id' => $emprestimoId
            ]);

            // 2. Liberar o livro voltando status para DISPONÍVEL (1)
            Livro::atualizarStatus($livroId, 1);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    // Listar histórico de empréstimos com JOIN
    public static function listarTodos() {
        $db = Database::getConexao();
        $sql = "SELECT e.*, u.nome AS usuario_nome, l.titulo AS livro_titulo 
                FROM emprestimos e
                JOIN usuarios u ON e.usuario_id = u.id
                JOIN livros l ON e.livro_id = l.id
                ORDER BY e.status ASC, e.data_emprestimo DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }
}