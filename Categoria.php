<?php
// models/Categoria.php
require_once __DIR__ . '/../config/Database.php';

class Categoria {
    private $id;
    private $nome;
    private $descricao;
    private $db;

    public function __construct($nome = null, $descricao = null, $id = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->db = Database::getConexao();
    }

    // Getters e Setters
    public function getId() { return $this->id; }
    public function getNome() { return $this->nome; }
    public function setNome($nome) { $this->nome = $nome; }
    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }

    // CRUD: Cadastrar Categoria
    public function salvar() {
        $sql = "INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome' => $this->nome,
            ':descricao' => $this->descricao
        ]);
    }

    // CRUD: Listar todas as categorias
    public static function listarTodas() {
        $db = Database::getConexao();
        $stmt = $db->query("SELECT * FROM categorias ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    // CRUD: Buscar por ID
    public static function buscarPorId($id) {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // CRUD: Excluir Categoria
    public static function deletar($id) {
        $db = Database::getConexao();
        $stmt = $db->prepare("DELETE FROM categorias WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}