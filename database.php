<?php 
    class Database {
    private $host = 'localhost';
    private $dbname = 'taskflow';
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
        $sql = "INSERT INTO bugs (titre, description, status, assigner, priority) VALUES (:titre, :description, :status, :assigner, :priority)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            ':titre' => $bug->getTitre(),
            ':description' => $bug->getDescription(),
            ':status' => $bug->getStatus(),
            ':assigner' => $bug->getAssigner(),
            ':priority' => $bug->getPriority()
        ]);
    }

    public function saveFeature(Feature $feature) {
        $sql = "INSERT INTO features (titre, description, status, assigner, deadline) VALUES (:titre, :description, :status, :assigner, :deadline)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            ':titre' => $feature->getTitre(),
            ':description' => $feature->getDescription(),
            ':status' => $feature->getStatus(),
            ':assigner' => $feature->getAssigner(),
            ':deadline' => $feature->getDeadline()
        ]);
    }
    
        public function getTasksByStatus($status) {
            $sql = "
                SELECT 'task' AS type, t.id, t.titre, t.description, t.status, t.assigner FROM tasks t WHERE t.status = :status
                UNION ALL SELECT 'bug' AS type, b.id, b.titre, b.description, b.status,b.assigner FROM bugs b WHERE b.status = :status UNION ALL
                SELECT 'feature' AS type, f.id, f.titre, f.description,f.status, f.assigner FROM features f WHERE f.status = :status
            ";
        
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([':status' => $status]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
}
?>