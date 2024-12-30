<?php require 'database.php'; 

$db = new Database();
$tasksToDo = $db->getTasksByStatus('a faire');
$tasksInProgress = $db->getTasksByStatus('en cours');
$tasksDone = $db->getTasksByStatus('done');?> 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                class='hover:text-[#007bff] text-gray-500  block font-semibold text-[15px]'>Creer task</a>
            </li>
            <li class='max-lg:border-b border-gray-300 max-lg:py-3 px-3'><a href='taches'
              class='hover:text-[#007bff] text-[#007bff]  block font-semibold text-[15px]'>Consulter tasks</a>
            </li>
          </ul>
        </div>
        
      </div>
    </header>
<body>
    
    <div class="task-section container mt-8 p-6 grid grid-cols-3 gap-6">
        <div class="task-category ">
            <h2 class="text-2xl font-semibold text-blue-600">À Faire</h2>
            <div class="task-list grid grid-cols-1">
                <?php foreach ($tasksToDo as $task): ?>
                    <div class="task bg-white p-4 rounded-lg shadow-md hover:shadow-xl transition duration-300 my-1">
                        <h3 class="text-xl font-semibold text-blue-500"><?php echo htmlspecialchars($task['titre']); ?></h3>
                        <p class="text-gray-600"><?php echo htmlspecialchars($task['description']); ?></p>
                        <p><strong>Assigné à :</strong> <?php echo htmlspecialchars($task['assigner']); ?></p>
                        <p><strong>Statut :</strong> <?php echo htmlspecialchars($task['status']); ?></p>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="task-category ">
            <h2 class="text-2xl font-semibold text-blue-600">En Cours</h2>
            <div class="task-list grid grid-cols-1 ">
                <?php foreach ($tasksInProgress as $task): ?>
                    <div class="task bg-white p-4 rounded-lg shadow-md hover:shadow-xl transition duration-300 my-1">
                        <h3 class="text-xl font-semibold text-blue-500"><?php echo htmlspecialchars($task['titre']); ?></h3>
                        <p class="text-gray-600"><?php echo htmlspecialchars($task['description']); ?></p>
                        <p><strong>Assigné à :</strong> <?php echo htmlspecialchars($task['assigner']); ?></p>
                        <p><strong>Statut :</strong> <?php echo htmlspecialchars($task['status']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="task-category ">
            <h2 class="text-2xl font-semibold text-blue-600">Terminé</h2>
            <div class="task-list grid grid-cols-1 ">
                <?php foreach ($tasksDone as $task): ?>
                    <div class="task bg-white p-4 rounded-lg shadow-md hover:shadow-xl transition duration-300 my-1">
                        <h3 class="text-xl font-semibold text-blue-500"><?php echo htmlspecialchars($task['titre']); ?></h3>
                        <p class="text-gray-600"><?php echo htmlspecialchars($task['description']); ?></p>
                        <p><strong>Assigné à :</strong> <?php echo htmlspecialchars($task['assigner']); ?></p>
                        <p><strong>Statut :</strong> <?php echo htmlspecialchars($task['status']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
