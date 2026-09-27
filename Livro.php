<?php
// models/Livro.php
require_once __DIR__ . '/../config/Database.php';

class Livro {
    private $id;
    private $titulo;
    private $autor;
    private $isbn;
    private $anoPublicacao;
    private $disponivel;
    private $categoriaId;
    private $db;

    public function __construct($titulo = null, $autor = null, $isbn = null, $anoPublicacao = null, $categoriaId = null, $id = null, $disponivel = true) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->isbn = $isbn;
        $this->anoPublicacao = $anoPublicacao;
        $this->categoriaId = $categoriaId;
        $this->disponivel = $disponivel;
        $this->db = Database::getConexao();
    }

    // Getters e Setters
    public function getId() { return $this->id; }
    public function getTitulo() { return $this->titulo; }
    public function getAutor() { return $this->autor; }
    public function getIsbn() { return $this->isbn; }
    public function getAnoPublicacao() { return $this->anoPublicacao; }
    public function isDisponivel() { return $this->disponivel; }
    public function getCategoriaId() { return $this->categoriaId; }

    // CRUD: Salvar novo Livro
    public function salvar() {
        $sql = "INSERT INTO livros (titulo, autor, isbn, ano_publicacao, categoria_id) 
                VALUES (:titulo, :autor, :isbn, :ano_publicacao, :categoria_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo' => $this->titulo,
            ':autor' => $this->autor,
            ':isbn' => $this->isbn,
            ':ano_publicacao' => $this->anoPublicacao,
            ':categoria_id' => $this->categoriaId
        ]);
    }

    // CRUD: Listar livros com nome da categoria associada (JOIN)
    public static function listarTodos() {
        $db = Database::getConexao();
        $sql = "SELECT l.*, c.nome AS categoria_nome 
                FROM livros l 
                LEFT JOIN categorias c ON l.categoria_id = c.id 
                ORDER BY l.titulo ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    // CRUD: Buscar por Filtro (Título ou Autor)
    public static function buscar($termo) {
        $db = Database::getConexao();
        $sql = "SELECT l.*, c.nome AS categoria_nome 
                FROM livros l 
                LEFT JOIN categorias c ON l.categoria_id = c.id 
                WHERE l.titulo LIKE :termo OR l.autor LIKE :termo";
        $stmt = $db->prepare($sql);
        $stmt->execute([':termo' => '%' . $termo . '%']);
        return $stmt->fetchAll();
    }

    // Método de Negócio: Alterar disponibilidade
    public static function atualizarStatus($id, $status) {
        $db = Database::getConexao();
        $stmt = $db->prepare("UPDATE livros SET disponivel = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}