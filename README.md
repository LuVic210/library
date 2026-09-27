# 📚 Library Management System

A full-stack web application developed for managing, organizing, categorizing, and borrowing library books. Built using Object-Oriented Programming (OOP) principles in PHP, MySQL database via XAMPP, and a responsive HTML5/CSS3 frontend.

---

## 🛠️ Technical Documentation

### 1. Frontend (User Interface)

The frontend was designed with a focus on usability, simplicity, and responsiveness, utilizing semantic HTML5 for structure and custom CSS3 for styling.

#### Modular Architecture (Views):
* **`header.php`**: Contains the global header, main navigation bar, and CSS style imports.
* **`footer.php`**: Standard footer included across all pages of the application.
* **`index.php` (Main Dashboard)**: Displays the book registration form and a dynamically updated table of the library catalog.
* **`views/emprestimos.php`**: Interface for selecting readers/books, logging checkouts, and managing returns.

#### Styling & Layout (`public/css/style.css`):
* **Flexbox Layout**: Used for clean alignment and structural positioning of forms and card components.
* **Responsive Tables**: Provides clear display of catalog entries and borrow/return transaction history.
* **Visual Feedback (Badges)**: Color-coded UI components indicating book availability (*Green = Available*, *Red = Borrowed*) and loan status (*Pending* / *Returned*).

---

### 2. Backend (Business Logic & OOP)

The backend is built in PHP adhering to Object-Oriented Programming (OOP) paradigms and clean architectural practices.

#### Database Connection (`config/Database.php`):
* Implements the **PDO (PHP Data Objects)** API to support database transactions and prevent SQL Injection attacks using prepared statements.
* Applies the **Singleton Pattern** to ensure a single, efficient database connection instance throughout the request lifecycle.

#### Model Layer (`models/`):
* **`Usuario.php` / `Leitor.php` / `Administrador.php`**: User entities mapping implemented using Object-Oriented **Inheritance**.
* **`Categoria.php`**: Encapsulates CRUD methods for organizing books by genre/subject.
* **`Livro.php`**: Represents the main book entity, containing logic for saving, searching, fetching with category `JOIN`s, and toggling availability status.
* **`Emprestimo.php`**: Houses the core business logic for checkouts and returns. Uses **PDO Transactions** (`beginTransaction`, `commit`, `rollBack`) to guarantee atomic execution when logging loan records and updating book availability simultaneously.

---

### 3. Database Architecture (MySQL on XAMPP)

The relational database model was created with MySQL/MariaDB and executed locally using the XAMPP stack.

* **Database Name**: `biblioteca_db` (Encoding: `utf8mb4_unicode_ci`)

#### Table Structures & Relationships:
1. **`categorias`**: Stores book genres/categories.
   * `id` (**PK**), `nome`, `descricao`
2. **`usuarios`**: Unified table for readers and administrators.
   * `id` (**PK**), `nome`, `email`, `senha`, `tipo`, `matricula_telefone`
3. **`livros`**: Stores all registered book titles.
   * `id` (**PK**), `titulo`, `autor`, `isbn`, `ano_publicacao`, `disponivel`, `categoria_id` (**FK**)
   * **Relationship**: $1:N$ with `categorias` via foreign key `categoria_id`.
4. **`emprestimos`**: Logs borrow and return transactions.
   * `id` (**PK**), `usuario_id` (**FK**), `livro_id` (**FK**), `data_emprestimo`, `data_devolucao_prevista`, `data_devolucao_real`, `status`
   * **Relationship**: $1:N$ with both `usuarios` and `livros`.

---

### 4. Compilation & Execution Summary

The project successfully completed the full software development lifecycle:

1. **System Modeling**: UML Diagrams (Use Case & Class) and Entity-Relationship Diagrams (ERD) established the theoretical framework before coding.
2. **Environment**: A local XAMPP environment (Apache + MySQL) integrated with VS Code enabled seamless execution, interpretation, and testing of PHP scripts and SQL queries.
3. **Layer Integration**: Strict separation of concerns between database persistent storage, PHP business rules, and HTML/CSS presentation layers produced a clean, maintainable, and scalable codebase.
4. **Validation**: All functional tests—including category management, book cataloging, checkout operations, and return tracking—were successfully validated against functional and non-functional requirements.
