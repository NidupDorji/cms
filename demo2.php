<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <header>Header</header>

  <div class="course-material-container">
    <div class="container">
      <div class="left-panel">
        left panel
      </div>
      <div class="right-panel">
        <div class="video-section">
          video section
        </div>
        <div class="content-tabs">
          <button class="nav-button" data-content="Transcript content">
            <span>Transcript</span>
          </button>
          <button class="nav-button" data-content="Materials content">
            <span>materials</span>
          </button>
          <button class="nav-button" data-content="Downloads content">
            <span>downloads</span>
          </button>
          <button class="nav-button" data-content="Discuss content ">
            <span>discuss</span>
          </button>
        </div>
        <div class="display-section">
          display section
        </div>
        <div class="feedback">like,share</div>
      </div>
    </div>
  </div>
  <footer>Footer</footer>
  <script>
    // Get all buttons and display section
    const buttons = document.querySelectorAll('.nav-button');
    const displaySection = document.querySelector('.display-section');

    // Add event listeners to all buttons
    buttons.forEach(button => {
      button.addEventListener('click', () => {
        // Change content of the display section based on the button clicked
        displaySection.textContent = button.getAttribute('data-content');
      });
    });
  </script>
</body>

</html>

<style>
  body {
    background-color: burlywood;
  }

  header {
    background-color: yellow;
    padding: 30px;
    margin-bottom: 20px;
  }

  footer {
    background-color: yellow;
    padding: 30px;
    margin-top: 10px;
  }

  .course-material-container {
    background-color: white;
    border: 1px solid black;
    padding: 4px;
    height: 100%;

  }

  .container {
    background-color: cornflowerblue;
    display: flex;
    border: 1px solid black;
    padding: 4px;
    height: 100vh;
  }

  .left-panel {
    background-color: red;
    border: 1px solid black;
    width: 25%;
    padding: 20px;
    overflow-y: auto;
  }

  .right-panel {
    background-color: yellow;
    border: 1px solid black;
    width: 75%;
  }

  .video-section {
    background-color: green;
    border: 1px solid black;
    height: 65%;
  }

  .content-tabs {
    background-color: whitesmoke;
    padding: 15px;
  }

  .display-section {
    background-color: darkturquoise;
    padding: 20px;
    border: 1px solid black;
    height: 20%;
    overflow-y: auto;
  }

  button {
    border: none;
    border: none;
    cursor: pointer;
    margin-right: 10px;
  }


  button:hover span,
  button:focus span {
    text-decoration: underline;
  }

  .feedback {
    /* flex-grow: 1; */
  }
</style>