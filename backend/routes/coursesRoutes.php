<?php
// controla las peticiones - tabla materias

require_once("./config/databaseConfig.php");
require_once("./routes/routesFactory.php");
require_once("./controllers/coursesController.php");

routeRequest($conn);
?>