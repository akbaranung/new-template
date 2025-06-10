<style>
  .col-xs-3 {
    width: 25%;
    background-color: #004e81;
  }

  .row {
    margin-left: 0px;
  }

  .container-fluid {
    padding-right: 0px;
    padding-left: 0px
  }

  .btn_footer_panel .tag_ {
    padding-top: 37px;
  }


  .justify-content-center {
    display: block;
    justify-content: center;
  }


  /*video*/
  canvas {
    position: absolute;

  }

  .video-container {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Styles for mobile devices */
  @media (max-width: 768px) {
    .video-container {
      display: block;
      /* Or you can omit this if you don't want flex behavior */
      margin-left: 20px;
      /* Adjust the margin as needed */
    }
  }

  #video {
    border-radius: 10px;
    box-shadow: #000;
  }
</style>

<div class="container-fluid">
  <div class="container">
    <div class="main--content">
      <div id="messageDiv" class="messageDiv" style="display:none;"> </div>
      <h5 id="lokasi_sekarang"></h5>
      <!-- <button class="btn" id="ShowUser" onclick="getLocation()">Tampilkan Posisi</button> -->
      <!-- <button class="btn" id="ShowUser" onclick="updateTable()">Tampilkan User</button> -->
      <div class="attendance-button">
        <button hidden id="startButton" class="add">Launch Facial Recognition</button>
        <button id="endButton" class="add" style="display:none">End Attendance Process</button>
        <button hidden id="endAttendance" class="add">END Attendance Taking</button>
      </div>

      <div class="video-container" style="display:flex">
        <video id="video" class="video-class" width="320" height="240" autoplay muted></video>
        <canvas id="overlay"></canvas>
      </div>

      <div class="table-container">

        <div id="studentTableContainer">

        </div>

      </div>
      <p id="location"></p>
    </div>
  </div>
</div> <!-- .container-fluid -->