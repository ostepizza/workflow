<?php
session_start();

function makePage($code, $title="Page", $userFeedback=NULL, $userFeedbackColor="primary", $requireLogin=false, $requireNoUser=false) {

$pageTitle = $title;
$currentURL = $_SERVER['REQUEST_URI'];
$relativePathToRoot = str_repeat('../', (substr_count($currentURL, '/')-2));

if (($requireLogin && empty($_SESSION['user_id'])) || ($requireNoUser && !empty($_SESSION['user_id']))) {
    header('Location: ' . $relativePathToRoot . '403.php');
}

echo '
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="' . $relativePathToRoot . 'assets/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>' . $pageTitle . ' - Workflow</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="' . $relativePathToRoot . '">Workflow</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="' . $relativePathToRoot . 'index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="' . $relativePathToRoot . 'jobs/index.php">Job listings</a>
                </li>
            </ul>';
            
            if (!empty($_SESSION['user_id'])) {
                echo '
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Hello, ' . $_SESSION['user_fname'] . ' ' . $_SESSION['user_lname'] . '!</a>
                        <ul class="dropdown-menu">
                            <li class="nav-item">
                                <a class="dropdown-item" href="' . $relativePathToRoot . 'user/index.php">My profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="dropdown-item" href="' . $relativePathToRoot . 'user/logout.php">Log out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                ';
            } else {
                echo '
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="' . $relativePathToRoot . 'user/login.php">Log in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="' . $relativePathToRoot . 'user/register.php">Register</a>
                    </li>
                </ul>
                ';
            }

            echo '
            </div>
        </div>
    </nav>
    <main>
        <div class="container">';
    if (isset($userFeedback)) {
        echo '
        <div class="alert alert-' . $userFeedbackColor . ' mt-3" role="alert">
            ' . $userFeedback . '
        </div>
        ';
    }
    $code();
    echo '
        </div>
    </main>
    <div class="container">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
            <p class="col-md-4 mb-0 text-muted">&copy; ' . date("Y") . ' Workflow</p>

            <ul class="nav col-md-4 justify-content-end">
                <li class="nav-item">
                    <a class="nav-link px-2 text-muted" href="' . $relativePathToRoot . 'index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 text-muted" href="' . $relativePathToRoot . 'jobs/index.php">Job listings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 text-muted" href="' . $relativePathToRoot . 'company/index.php">Company</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 text-muted" href="' . $relativePathToRoot . 'about.php">About</a>
                </li>
            </ul>
        </footer>
    </div>
    <script src="' . $relativePathToRoot . 'assets/js/bootstrap.bundle.js"></script>
    <script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll(\'[data-bs-toggle="tooltip"]\'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    </script>
</body>
</html>'
;}                     
?>