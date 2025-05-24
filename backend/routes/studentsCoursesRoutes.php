<?php
require_once("./config/databaseConfig.php");
require_once("./routes/routesFactory.php");
require_once("./controllers/studentsCoursesController.php");

routeRequest($conn);
