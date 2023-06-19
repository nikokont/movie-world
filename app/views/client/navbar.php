<nav class="navbar">


  <div class="navbar-logo">
    <a class="navbar-brand" href="<?= ROOT ?>"><span style="color:white;">MOVIE</span> <span style="color:red;">WORLD</span></a>
  </div>

  <div class=navbar-links>
  
    <?php if (isset($data['logged-user'])) : ?>
      <li class="nav-item welcome-message" >Welcome back, <?= $data['logged-user']->username; ?> </li>
      <li class="nav-item"><a class="nav-link" href="logout">Logout</a></li>

    <?php else : ?>
      <li class="nav-item"><a class="nav-link" href="login">Login</a></li>
      <li class="nav-item"><a class="nav-link" href="signup">Signup</a></li>
    <?php endif  ?>

  </div>


</nav>