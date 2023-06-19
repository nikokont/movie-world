<?php include "header.php" ?>



<body>

    <?php include "navbar.php" ?>

    <div class="form-div">

        <form class="client-form primary-background-color" id="login-form" method="post">

            <h2 style="margin-bottom:50px;color:#fff;">Log In</h2>

            <span style="color:red;"><?= check_message(); ?> </span>

            <div class="mb-4 form-group">
                <label for="user-name" class="col-form-label">Enter Email:</label>
                <input type="email" name="email" class="form-control" id="user-email" placeholder="Add email...">
            </div>

            <div class="mb-2 form-group">
                <label for="user-password" class="col-form-label">Enter Password:</label>
                <input type="password" name="password" class="form-control" id="user-password" placeholder="Add password...">
            </div>


            <button class="btn submit-form-button">Submit</button>

            <small class="signup-link">Dont have account?? <a style="color:#fff;" href="<?= ROOT ?>/signup">Sign up here</a></small>

        </form>

    </div>

    <?php include "footer.php" ?>

</body>