<?php
include_once '../assets/include/template.php';

function display()
{
?>
    <!-- Content here -->
    <div class="row mt-4">
        <!-- Headline of the job listing -->
        <div class="col-md-8">
        <button class="btn btn-secondary">Go back to job listings</button>
            <!-- Add management buttons if it belongs to a company user is a part of-->
        </div>
    </div>
    <!-- Job name description -->
    <div class="row mt-5">
        <div class="col-md-7">
            <h4>Job description</h4>
            <hr>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum mollis augue non nisi faucibus, a tempor quam venenatis. Nullam nec porta ligula. Ut fringilla turpis at elit tincidunt tempor. Nulla at venenatis justo. Fusce rhoncus vestibulum nunc placerat accumsan. Nullam mattis velit porttitor lorem luctus posuere. Praesent ac nunc eu erat elementum euismod viverra in magna. Phasellus congue ex quis augue iaculis, quis fringilla mauris elementum. Nam tristique massa a arcu ultricies placerat. Proin enim arcu, blandit id lectus fringilla, volutpat congue lorem.

                In scelerisque laoreet lorem, nec blandit odio commodo in. Ut non purus purus. Vestibulum tristique sollicitudin dignissim. Duis dictum, orci sed iaculis lobortis, quam orci eleifend felis, ac convallis tortor nunc non diam. Donec eu lacinia ipsum. Sed euismod varius felis, id scelerisque nibh ornare euismod. Aliquam non mattis ante. Nam eleifend quam venenatis lacus malesuada dapibus. Vestibulum in justo vel nisi commodo eleifend nec at sapien. Curabitur at ornare ex. Sed nec dui libero. Cras ac commodo metus. Aenean non libero laoreet eros porta suscipit. Donec rutrum, ligula elementum auctor molestie, mauris justo vestibulum urna, dignissim luctus ipsum neque a neque.

                Cras molestie erat mauris, eu cursus libero sodales a. Mauris risus massa, sodales eu finibus at, fermentum consequat mi. Donec pellentesque purus quis dolor iaculis finibus. Nullam ac magna posuere lorem consectetur vehicula ut sed felis. Nulla a consequat sapien. Curabitur mi ante, vestibulum vel accumsan lacinia, malesuada rutrum odio. Mauris consequat dui eu consectetur ultricies. Fusce sed tincidunt eros. Nam mollis dapibus ipsum, at cursus nunc. Pellentesque id nibh at turpis egestas lobortis a sed magna. Phasellus felis ligula, facilisis eu tempor vitae, tempus at sapien. Pellentesque eleifend et libero non feugiat. Aliquam a viverra enim, non porta massa. Proin vel pulvinar est.</p>

        </div>
        <!-- Whitespace begin -->
        <div class="col-md-1">
        </div>
        <!-- Whitespace end, Card section begin -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header text-center">
                    About employer
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title">Big buisness</h5>
                    <p>We here to do big bizniz</p>
                    <p class="card-text">Frontend Developer</p>
                    <hr>
                    <p class="card-text">Deadline: 11.11.2011</p>
                    <p class="card-text">Categories: ???</p>
                    <hr>

                    <div class="col-md-4 mx-auto">
                        <button class="btn btn-primary">Apply for job</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row mt-5">
        <div class="col-md-2">
            <p>Published date: 11.11.2011</p>
        </div>

<?php
}

makePage('display', 'Job listing');
