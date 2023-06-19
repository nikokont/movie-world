<?php include "header.php" ?>

<body>

    <?php include "navbar.php" ?>

    <div class="form-div">



        <form class="client-form primary-background-color" id="signup-form" method="post">

            <h2 style="margin-bottom:50px;color:#fff;">Sign Up</h2>

            <span style="color:red;"><?= check_message(); ?> </span>


            <div class="mb-2 form-group">
                <label for="user-address" class="col-form-label">Your Username:</label>
                <input type="text" name="username" class="form-control" id="userName" placeholder="Enter your username...">
            </div>


            <div class="mb-2 form-group">
                <label for="user-email" class="col-form-label">Email:</label>
                <input type="email" name="user-email" class="form-control" id="userEmail" placeholder="Enter your email...">
            </div>

            <div class="mb-2 form-group">
                <label for="user-password" class="col-form-label">Enter A Password:</label>
                <input type="password" name="user-password" class="form-control" id="userPassword">
            </div>


            <button type="submit" class="btn  submit-form-button mt-4">Submit</button>

            <small class="signup-link">Already have account?? <a style="color:#fff;" href="<?= ROOT ?>/login">Log In here</a></small>
        </form>


        <?php

        if (isset($_SESSION['error']) && $_SESSION['error'] !== "") { ?>

            <div class="form-errors">
                <span style="color:red; font-size:220px; font-weight:600;"><?php check_message(); ?> </span>
            </div>


        <?php } ?>

    </div>

    <script>

    </script>



    <?php include "footer.php" ?>