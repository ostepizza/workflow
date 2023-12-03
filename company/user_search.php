<?php
include_once '../assets/include/template.php';

// Include and establish connection with DB
include_once '../assets/include/DBHandler.php';
$dbhu = new DBHandlerUser();

function display()
{
    global $dbhu;
?>
    <form action="" method="post">
        <div class="row mt-5">
            <div class="col-md-1">
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label for="exampleInputEmail1">Search for users</label>
                    <input type="text" class="form-control" name="searchfield" placeholder="...">
                </div>
            </div>
            <div class="col-md-3 mt-4">
                <button class="btn btn-primary" name="search" type="submit">Search</button>
            </div>
        </div>
    </form>
    <br>


    <?php
    if (isset($_POST["search"])) {
        if ($searched = $dbhu->searchUserCompetence($_POST["searchfield"])) {
            foreach ($searched as $search) {
                echo '
                    <div class="card mb-3">
                        <div class="card-header">
                            <b>'  . $search["first_name"] . ' ' . $search["last_name"] . '</b> (<a href="mailto:' . $search["email"] . '">' . $search["email"] . '</a>)' .  '
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <img src="../assets/img/user/default.jpg" alt="The default user profile picture" class="img-fluid rounded">
                                </div>
                                <div class="col-md-10" id="vl">
                                    <p class="card-text">' . $search["competence"] . '</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
            }
        } else {
            echo "No results...";
        }
    }
}

makePage('display', 'User Search');
