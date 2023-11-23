<?php include_once '../assets/include/template.php';

function display()
{
?>
    <div class="row mt-5">
        <div class="col-md-4">
        <button class="btn btn-secondary">Go back</button>
        </div>
        <div class="col-md-4">
            <h1>Application to blabla</h1>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-8">
            <div class="form-group">
                <label for="exampleFormControlInput1">Title *</label>
                <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="...">
            </div>
        </div>
    </div>

    <div class="row md-5">
        <div class="col-md-8">
            <div class="form-group">
                <label for="exampleFormControlTextarea1">Description *</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="16" placeholder="Write here..."></textarea>
            </div>
        </div>
        <div class="col-md-1">
        </div>
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header text-center">
                    About employer
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title">Sample Text</h5>
                    <p>Sample text</p>
                    <p>Sample text</p>
                    <p>Sample text</p>
                    <hr>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            By clicking this, you verify that you've read through the application atleast once. *
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        <label class="form-check-label" for="flexCheckDefault">
                            Other terms that ive decided to make to a textbox >:D *
                        </label>
                    </div>
                    <hr>

                    <div class="col-md-7 mx-auto">
                        <button class="btn btn-primary">Send application</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
}

makePage('display', 'Job listings');
