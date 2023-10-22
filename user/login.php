<?php include_once '../assets/include/template.php';

function display() {
?>
    <!-- Oppgave her -->
<div class="row mt-5">
    <div class="col-md-2"></div>

    <div class="col-md-3">
        <h1>Log in</h1>
        <form>
        <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
        </div>
        <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="exampleCheck1">
        <label class="form-check-label" for="exampleCheck1">Check me out</label>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <div class="col-md-2"></div>

    <div class="col-md-3">
        <h2>No account?</h2>
        <a class="btn btn-primary" href="register.php" role="button">Register new account</a>
    </div>
    
    <div class="col-md-2"></div>
</div>
    <!-- Oppgave her -->
<?php
}

makePage('display', 'Log in');