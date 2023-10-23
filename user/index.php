<?php include_once '../assets/include/template.php';

include("../assets/include/connection.php");

function display() {

$conn = createDBConnection(); //Connects to the database
?>
<!-- Content here -->
<div class="row mt-5">
    <div class="col-md-12">
        <h1>User</h1>
        <?php 
        $user_id = $_SESSION['user_id']; 

        $sql = 'SELECT `email`, `first_name`, `last_name`, `telephone`, `location`, `birthday`, `picture`, `cv` FROM `user` WHERE `id` = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result(); // Get the result set.
    
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc(); // Fetch the row as an associative array.
                echo 'Name: ' . $row['first_name'] . ' ' . $row['last_name'] . '<br>';
                echo 'Email: ' . $row['email'] . '<br>';
                echo 'Telephone: ' . $row['telephone'] . '<br>';
                echo 'Location: ' . $row['location'] . '<br>';
                echo 'Birthday: ' . $row['birthday'] . '<br>';
            } else {
                $feedbackForUser = "An error occurred.";
            }
        } else {
            $feedbackForUser = "An error occurred.";
        }
        
        ?>
    </div>
</div>
<!-- Content here -->
<?php
}

makePage('display', 'Profile', requireLogin: true);