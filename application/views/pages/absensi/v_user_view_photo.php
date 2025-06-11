<!-- Leaflet  -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous">

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

  body {}

  table,
  th,
  td {
    border: 0px solid black;
  }

  table.center {
    margin-left: auto;
    margin-right: auto;
  }

  .button1 {
    background-color: #4CAF50;
  }

  table,
  table {
    border-collapse: separate;
    border-spacing: 0 1em;
  }

  .image-box {
    position: relative;
    display: inline-block;
    margin-left: 20%;
    margin-top: 5px;
  }

  .image-box img {
    display: block;
    max-width: 100%;
    height: auto;
    border-radius: 5px;
  }

  .image-box:hover img {
    filter: blur(0.5px);
    cursor: pointer;
    box-shadow: 0px 0px 10px #5073fb;

  }

  .edit-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: none;
    cursor: pointer;
    color: darkblue;
    font-size: 5rem;

  }

  .image-box:hover .edit-icon {
    display: block;
  }

  .image-box {
    position: relative;
    display: inline-block;
    height: 20rem;
    width: 15rem;
  }

  .image-box img {
    display: block;
    max-width: 100%;
    height: auto;
    border-radius: 5px;
  }

  .image-box:hover img {
    filter: blur(1.5px);
    cursor: pointer;
    transform: scale(1.1);
    box-shadow: 0px 0px 10px #5073fb;
  }

  /* Green */
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Foto User</h1>
      <div class="card shadow mb-4">
        <div class="card-header">
          <p class="card-title">
            <strong>Form Foto User</strong>
          </p>
        </div>
        <div class="card-body">
          <div align="center">
            <font style="font-size:17px;">
              Foto User
              <hr />
            </font>
          </div>
          <form action="<?= base_url('absensi/add_photo') ?>" method="POST">
            <!-- <input type="hidden" name="<?= $mode ?>" value="<?= $mode ?>"> -->
            <input type="hidden" value="<?= $user->id  ?>" name="id">
            <table>
              <tr>
                <th width="300">Username</th>
                <td width="300">
                  <input type="text>" name="username" class="form-control" value="<?= $user->username  ?>" readonly>
                </td>
              </tr>
              <tr>
                <th width="200">Nama</th>
                <td>
                  <input type="text" name="nama" class="form-control" value="<?= $user->nama ?>" readonly>
                </td>
              </tr>
              <?php if (!empty($user->userImage)) { ?>
                <tr>
                  <div id="image-gallery" class="row">
                    <?php
                    $images = json_decode($user->userImage, true); // Decode the JSON array
                    $imagePath = 'resources/labels/' . $user->username . '/';
                    foreach ($images as $image) : ?>
                      <div class="user-image col-md-2">
                        <img src="<?= base_url($imagePath . $image) ?>" alt="User Image" style="width: 100px; margin: 5px;">
                      </div>
                    <?php endforeach; ?>
                    <!-- <img src="<?= base_url() ?>resources/images/default.png" alt="Default Image"> -->
                  </div>
                </tr>
              <?php } else { ?>
                <tr>
                  <div>
                    <div class="form-title-image">
                      <p>Take Pictures</p>
                    </div>
                    <div id="open_camera" class="image-box" onclick="takeMultipleImages()">
                      <img src="<?= base_url() ?>resources/images/default.png" alt="Default Image">
                    </div>
                    <div id="multiple-images"></div>
                  </div>
                </tr>
              <?php } ?>
              <!-- Delete all images button -->
              <?php if (!empty($user->userImage)) { ?>
                <button type="button" id="delete-images" class="btn btn-danger" onclick="deleteUserImages('<?= $user->username ?>')">Delete All Images</button>
              <?php } ?>
              <tr>
                <th>
                  <a href="<?= base_url('app/user') ?>" class="btn btn-warning"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
                  <?php if (empty($user->userImage)) { ?>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  <?php } ?>
                </th>
              </tr>
            </table>
          </form>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->