  <div class="content">
    <?php if (isset($_SESSION['success'])) : ?>
      <div class="error success">
        <h3>
          <?php
          echo $_SESSION['success'];
          unset($_SESSION['success']);
          ?>
        </h3>
      </div>
    <?php endif ?>

    <!-- welcome USERNAME  -->
    <?php if (isset($_SESSION['username'])) : ?>
      <p style="font-size: 24px; color: #ffd700; text-align: center;  padding: 10px;  background: radial-gradient(circle, #5F9EA0, ); box-shadow: 0 4px 8px rgba(255, 255, 255, 0.2);">Welcome <strong style="color: #ffd700; font-size: 28px;">
          <?php echo $_SESSION['username']; ?>
        </strong></p>
    <?php endif ?>
  </div>