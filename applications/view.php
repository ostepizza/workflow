<?php include_once '../assets/include/template.php';

function display() {
?>
<a href="javascript:history.back()" class="btn btn-secondary mt-5" role="button">Back</a><br>
<div class="row mt-3">
    <div class="col-md-12">
        <h1>Application</h1>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <p>
                    <b>From:</b><br>
                    Jeff Name<br>
                    <a href="mailto:#">jeffname@email.net</a><br>
                    +123123123<br>
                    Alabama<br>
                </p>
            </div>
            <div class="col-md-6">
                <p>
                    <b>To:</b><br>
                    Company Co.<br>
                    <i>regarding listing</i><br>
                    "Looking for a burger flipper"<br>
                </p>
            </div>
            <hr>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h2>I want this job</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p>
                --Introduction--<br>
                Dear Hiring Manager,<br>
                <br>
                I am writing to express my genuine interest in the Burger Flipper position at your esteemed establishment.
                Having been an admirer of your restaurant's commitment to delivering top-notch food and service,
                I am enthusiastic about the opportunity to contribute my skills and dedication to your team.
                My passion for creating delicious burgers, combined with my experience in the fast-food industry,
                makes me confident in my ability to excel in this role.<br>
                <br>
                --Skills and Experience--<br>
                Throughout my previous roles in the fast-food industry,
                I have honed my skills in burger flipping to an art. 
                I take pride in ensuring each patty is cooked to perfection, 
                delivering a mouthwatering experience to every customer. 
                My attention to detail extends beyond just flipping burgers – 
                I am adept at maintaining a clean and organized kitchen, ensuring a smooth workflow during busy hours. 
                With a strong focus on efficiency and customer satisfaction, 
                I have consistently received positive feedback for my burger-making prowess.<br>
                <br>
                --Commitment and Team Collaboration--<br>
                What sets me apart is not only my proficiency in burger flipping but also my 
                commitment to being a valuable team member. 
                I thrive in fast-paced environments and understand the importance of 
                teamwork in delivering exceptional service. I am eager to bring my positive attitude, 
                strong work ethic, and passion for creating delectable burgers to your team. 
                I am confident that my skills and enthusiasm align perfectly with the values of your establishment.<br>
                <br>
                Thank you for considering my application. I am eager for the opportunity to 
                discuss how my burger-flipping expertise and dedication can contribute to the continued success of your restaurant.<br>
                <br>
                Sincerely,<br>
                [Your Name]
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-1"></div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header text-center">
                <img src="../assets/img/user/default.jpg" alt="The default profile picture" class="img-fluid rounded border border-secondary w-50 m-2">
                <br>
                <span class="h5">Jeff Name</span>
            </div>
            <div class="card-body">
                <p class="card-text h6 text-center">Competence</p>
                <p class="card-text">
                    I am a skilled worker, who likes to eat burgers.<br>
                    I am very profound of my work, and I am always on time, most of the time.<br>
                    <br>
                    I have completed the following courses:<br>
                    - How to make a burger<br>
                    - How to eat a burger<br>
                    - How to make a cheeseburger<br>
                    - How to eat a cheeseburger
                </p>
            </div>
        </div>
    </div>
</div>


<?php

}
makePage('display', 'View Application', requireLogin: true);
?>