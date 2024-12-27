<?php
class Task {
    private $id;
    private $titre;
    private $description;
    private $status;
    private $assigner;

    public function __construct($titre, $description, $status = 'to_do') {
        $this->titre = $titre;
        $this->description = $description;
        $this->status = $status;
        $this->assigner = null;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getAssigner() {
        return $this->assigner;
    }

    public function setTitre($titre) {
        $this->titre = $titre;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setStatus($status) {
        $validStatuses = ['to_do', 'in_progress', 'done'];
        if (in_array($status, $validStatuses)) {
            $this->status = $status;
        } else {
            throw new Exception("Invalid status");
        }
    }

    public function assignTo($user) {
        $this->assigner = $user;
    }
}

class Bug extends Task {
    private $priority;

    public function __construct($titre, $description, $priority, $status = 'to_do') {
        parent::__construct($titre, $description, $status);
        $this->setPriority($priority);
    }

    public function getPriority() {
        return $this->priority;
    }

    public function setPriority($priority) {
        $validPriorities = ['Low', 'Medium', 'High'];
        if (in_array($priority, $validPriorities)) {
            $this->priority = $priority;
        } else {
            throw new Exception("Invalid priority");
        }
    }
}

class Feature extends Task {
    private $deadline;

    public function __construct($titre, $description, $deadline, $status = 'to_do') {
        parent::__construct($titre, $description, $status);
        $this->deadline = $deadline;
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function setDeadline($deadline) {
        $this->deadline = $deadline;
    }
}

class Database {
    private $host = 'localhost';
    private $dbname = 'tasks';
    private $username = 'root';
    private $password = '';
    private $connection;

    public function connect() {
        if (!$this->connection) {
            try {
                $this->connection = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return $this->connection;
    }

    public function saveTask(Task $task) {
        $sql = "INSERT INTO tasks (titre, description, status, assigner) VALUES (:titre, :description, :status, :assigner)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            ':titre' => $task->getTitre(),
            ':description' => $task->getDescription(),
            ':status' => $task->getStatus(),
            ':assigner' => $task->getAssigner()
        ]);
    }

    public function saveBug(Bug $bug) {
        $sql = "INSERT INTO bugs (titre, description, priority, status, assigner) VALUES (:titre, :description, :priority, :status, :assigner)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            ':titre' => $bug->getTitre(),
            ':description' => $bug->getDescription(),
            ':priority' => $bug->getPriority(),
            ':status' => $bug->getStatus(),
            ':assigner' => $bug->getAssigner()
        ]);
    }

    public function saveFeature(Feature $feature) {
        $sql = "INSERT INTO features (titre, description, deadline, status, assigner) VALUES (:titre, :description, :deadline, :status, :assigner)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            ':titre' => $feature->getTitre(),
            ':description' => $feature->getDescription(),
            ':deadline' => $feature->getDeadline(),
            ':status' => $feature->getStatus(),
            ':assigner' => $feature->getAssigner()
        ]);
    }

    public function getTasksByUser($user) {
        $sql = "SELECT * FROM tasks WHERE assigner = :assigner";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([':assigner' => $user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTaskStatus($id, $status) {
        $sql = "UPDATE tasks SET status = :status WHERE id = :id";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([':status' => $status, ':id' => $id]);
    }
}

public function saveTaskFromForm($title, $description, $type, $priority, $extra, $status) {
    try {
        if ($type === 'bug' && !$priority) {
            throw new Exception("Priorité non définie !");
        }

        if ($type === 'bug' && !in_array($priority, ['Low', 'Medium', 'High'])) {
            throw new Exception("Priorité invalide !");
        }

        // Création de la tâche selon le type
        if ($type === 'bug') {
            $task = new Bug($title, $description, $priority, $status); // Ajout du statut
            $this->saveBug($task);
        } elseif ($type === 'feature') {
            $task = new Feature($title, $description, $extra, $status); // Ajout du statut
            $this->saveFeature($task);
        } else {
            $task = new Task($title, $description, $status); // Ajout du statut
            $this->saveTask($task);
        }

        return "Tâche créée avec succès !";
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TaskFlow</title>
    <link rel="stylesheet" href="styles/style.css">
    <script>
        function togglePriorityField() {
            const type = document.getElementById('type').value;
            const priorityField = document.getElementById('priorityField');
            const deadlineField = document.getElementById('deadlineField');
            if (type === 'bug') {
                priorityField.style.display = 'block';
                deadlineField.style.display = 'none';
            } else if (type === 'feature') {
                deadlineField.style.display = 'block';
                priorityField.style.display = 'none';
            } else {
                priorityField.style.display = 'none';
                deadlineField.style.display = 'none';
            }
        }
    </script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 28px;
        }
        .task-form {
            display: flex;
            flex-direction: column;
        }
        .task-form label {
            margin: 10px 0 5px;
            color: #555;
        }
        .task-form input,
        .task-form textarea,
        .task-form select {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .task-form button {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
        }
        .task-form button:hover {
            background-color: #45a049;
        }
        #priorityField, #deadlineField {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Créer une Tâche</h1>
        <form action="" method="POST" class="task-form">
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" required>

            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="type">Type :</label>
            <select id="type" name="type" onchange="togglePriorityField()">
                <option value="simple">Simple</option>
                <option value="bug">Bug</option>
                <option value="feature">Feature</option>
            </select>

            <div id="priorityField" style="display:none;">
                <label for="priority">Priorité :</label>
                <select id="priority" name="priority">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <div id="deadlineField" style="display:none;">
                <label for="deadline">Deadline :</label>
                <input type="date" id="deadline" name="extra">
            </div>

            <label for="status">Statut :</label>
            <select id="status" name="status">
                <option value="to_do">À faire</option>
                <option value="in_progress">En cours</option>
                <option value="done">Terminé</option>
            </select>
            <button type="submit">Créer</button>
        </form>
    </div>

    <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $priority = $_POST['priority'] ?? null;
    $extra = $_POST['extra'] ?? null;
    $status = $_POST['status']; 

    $db = new Database();
    $result = $db->saveTaskFromForm($title, $description, $type, $priority, $extra, $status);

    echo "<script>alert('$result');</script>";
}
    ?>
</body>
</html>
