<?php
require 'database.php';
class Task {
    private $id;
    private $titre;
    private $description;
    private $status;
    private $assigner;

    public function __construct($titre, $description, $status = 'a faire', $assigner = null) {
        $this->titre = $titre;
        $this->description = $description;
        $this->status = $status;
        $this->assigner = $assigner;
    }

    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getStatus() { return $this->status; }
    public function getAssigner() { return $this->assigner; }

    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setStatus($status) {
        $validStatuses = ['a faire', 'en cours', 'done'];
        if (in_array($status, $validStatuses)) {
            $this->status = $status;
        } else {
            throw new Exception("Statut invalide");
        }
    }
    public function assignTo($user) { $this->assigner = $user; }
}

class Bug extends Task {
    private $priority;

    public function __construct($titre, $description, $priority, $status = 'a faire', $assigner = null) {
        parent::__construct($titre, $description, $status, $assigner);
        $this->setPriority($priority);
    }

    public function getPriority() { return $this->priority; }
    public function setPriority($priority) {
        $validPriorities = ['Low', 'Medium', 'High'];
        if (in_array($priority, $validPriorities)) {
            $this->priority = $priority;
        } else {
            throw new Exception("Priorité invalide");
        }
    }
    public function saveToDatabase(Database $db) {
        $db->saveBug($this);
    }
}

class Feature extends Task {
    private $deadline;

    public function __construct($titre, $description, $deadline, $status = 'a faire', $assigner = null) {
        parent::__construct($titre, $description, $status, $assigner);
        $this->deadline = $deadline;
    }

    public function getDeadline() { return $this->deadline; }
    public function setDeadline($deadline) { $this->deadline = $deadline; }
    public function saveToDatabase(Database $db) {
        $db->saveFeature($this);
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title'], $_POST['description'], $_POST['status'], $_POST['assigner'], $_POST['type'])) {
        $db = new Database();
        
        $title = $_POST['title'];
        $description = $_POST['description'];
        $status = $_POST['status'];
        $assigner = $_POST['assigner'];
        $type = $_POST['type'];

        if ($type === 'bug' && isset($_POST['priority'])) {
            $task = new Bug($title, $description, $_POST['priority'], $status, $assigner);
            $task->saveToDatabase($db);
            header("Location: taches.php");
            
        } elseif ($type === 'feature' && isset($_POST['deadline'])) {
            $task = new Feature($title, $description, $_POST['deadline'], $status, $assigner);
            $task->saveToDatabase($db);
            header("Location: taches.php");

        } else {
            $task = new Task($title, $description, $status, $assigner);
            $db->saveTask($task);
            header("Location: taches.php");

        }
    }
    
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleFields() {
            var type = document.getElementById("type").value;
            document.getElementById("priorityField").style.display = (type === "bug") ? "block" : "none";
            document.getElementById("deadlineField").style.display = (type === "feature") ? "block" : "none";
        }
    </script>
</head>
<header class='flex shadow-md py-4 px-4 sm:px-10 bg-white font-[sans-serif] min-h-[70px] tracking-wide relative z-50'>
      <div class='flex flex-wrap items-center justify-between gap-5 w-full'>

        <div id="collapseMenu"
          class='max-lg:hidden lg:!block max-lg:before:fixed max-lg:before:bg-black max-lg:before:opacity-50 max-lg:before:inset-0 max-lg:before:z-50'>
          <button id="toggleClose" class='lg:hidden fixed top-2 right-4 z-[100] rounded-full bg-white w-9 h-9 flex items-center justify-center border'>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 fill-black" viewBox="0 0 320.591 320.591">
              <path
                d="M30.391 318.583a30.37 30.37 0 0 1-21.56-7.288c-11.774-11.844-11.774-30.973 0-42.817L266.643 10.665c12.246-11.459 31.462-10.822 42.921 1.424 10.362 11.074 10.966 28.095 1.414 39.875L51.647 311.295a30.366 30.366 0 0 1-21.256 7.288z"
                data-original="#000000"></path>
              <path
                d="M287.9 318.583a30.37 30.37 0 0 1-21.257-8.806L8.83 51.963C-2.078 39.225-.595 20.055 12.143 9.146c11.369-9.736 28.136-9.736 39.504 0l259.331 257.813c12.243 11.462 12.876 30.679 1.414 42.922-.456.487-.927.958-1.414 1.414a30.368 30.368 0 0 1-23.078 7.288z"
                data-original="#000000"></path>
            </svg>
          </button>

          <ul
            class='lg:flex gap-x-5 max-lg:space-y-3 max-lg:fixed max-lg:bg-white max-lg:w-1/2 max-lg:min-w-[300px] max-lg:top-0 max-lg:left-0 max-lg:p-6 max-lg:h-full max-lg:shadow-md max-lg:overflow-auto z-50'>
            <li class='max-lg:border-b border-gray-300 max-lg:py-3 px-3'>
              <a href='index.php'
                class='hover:text-[#007bff] text-[#007bff] block font-semibold text-[15px]'>Creer task</a>
            </li>
            <li class='max-lg:border-b border-gray-300 max-lg:py-3 px-3'><a href='taches.php'
              class='hover:text-[#007bff] text-gray-500 block font-semibold text-[15px]'>Consulter tasks</a>
            </li>
          </ul>
        </div>
        
      </div>
    </header>
<body class="bg-gray-100 text-gray-800 font-sans">
    <div class="container mx-auto p-6 w-[80%]">
        <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-lg grid ">
            <div class="grid grid-cols-[20%70%]">
            <label for="title" class="block text-lg font-medium">Titre :</label>
            <input type="text" id="title" name="title" required class="w-full p-3 mb-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            
            <label for="description" class="block text-lg font-medium">Description :</label>
            <textarea id="description" name="description" rows="4" required class="w-full p-3 mb-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            
            <label for="status" class="block text-lg font-medium">Statut :</label>
            <select id="status" name="status" class="w-full p-3 mb-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="a faire">À faire</option>
                <option value="en cours">En cours</option>
                <option value="done">Terminé</option>
            </select>
            
            <label for="assigner" class="block text-lg font-medium">Assigné à :</label>
            <input type="text" id="assigner" name="assigner" class="w-full p-3 mb-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            
            <label for="type" class="block text-lg font-medium">Type de tâche :</label>
            <select id="type" name="type" onchange="toggleFields()" class="w-full p-3 mb-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="task">Tâche normale</option>
                <option value="bug">Bug</option>
                <option value="feature">Feature</option>
            </select>
            
            <div id="priorityField" class="hidden ">
                <label for="priority" class="block text-lg font-medium">Priorité :</label>
                <select id="priority" name="priority" class="w-full p-3 mb-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <div id="deadlineField" class="hidden">
                <label for="deadline" class="block text-lg font-medium">Deadline :</label>
                <input type="date" id="deadline" name="deadline" class="w-full p-3 mb-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            </div>
            <button type="submit" class="w-[45%] bg-green-700 text-white m-auto p-3  rounded-md hover:bg-green-800 transition duration-300">Créer</button>
        </form>
    </div>
   
</body>
</html>
