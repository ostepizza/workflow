<?php
function makePage($code, $title) {
$pageTitle = $title;
$currentURL = $_SERVER['REQUEST_URI'];
$relativePathToRoot = str_repeat('../', (substr_count($currentURL, '/')-2));
echo '
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="' . $relativePathToRoot . 'assets/css/bootstrap.css">
    <title>' . $pageTitle . ' - Workflow</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="Eighth navbar example">
        <div class="container">
            <a class="navbar-brand" href="#">Workflow</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExample07">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="' . $relativePathToRoot . '">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="' . $relativePathToRoot . 'jobs/index.php">Job listings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="' . $relativePathToRoot . 'company/index.php">Companies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">test</a></li>
                        <li><a class="dropdown-item" href="#">test</a></li>
                        <li><a class="dropdown-item" href="#">testeeeee</a></li>
                    </ul>
                </li>
            </ul>
            
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="' . $relativePathToRoot . 'user/index.php">My profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="' . $relativePathToRoot . 'user/login.php">Log in</a>
                </li>
            </ul>
            </div>
        </div>
    </nav>
    <main>
        <div class="container">';
    $code();
    echo '
        </div>
    </main>
    <div class="container">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
            <p class="col-md-4 mb-0 text-muted">&copy; 2022 Workflow</p>

            <a href="/" class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
              <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"/></svg>
            </a>

            <ul class="nav col-md-4 justify-content-end">
              <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Home</a></li>
              <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Job listings</a></li>
              <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Companies</a></li>
              <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">About</a></li>
            </ul>
        </footer>
    </div>
    <script src="assets/js/bootstrap.bundle.js"></script>
</body>
</html>'
;}                     
?>