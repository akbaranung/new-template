<style>
  .open-memo {
    cursor: pointer;
  }

  .info-row {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 8px;
  }

  .label {
    width: 100px;
    font-weight: bold;
  }

  .colon {
    width: 10px;
  }

  .value {
    flex: 1;
    min-width: 150px;
  }

  .uppy-StatusBar-actions {
    display: none !important;
  }
</style>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12">
      <h1 class="page-title">TELLO</h1>
      <div class="card shadow mb-4">
        <div class="card-header text-center">
          <p class="card-title"><strong>Card Detail</strong></p>
          <p class="btn btn-success" style="width: fit-content;"><?= $detail_task['task_name'] ?></p>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <a href="<?= site_url('task/task_view/' . $detail_task['id_task']) ?>" class="btn btn-warning mb-3"><i class="fe fe-chevron-left"></i> Back</a>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
              <div class="info-row">
                <span class="label">Card Name</span>
                <span class="colon">:</span>
                <span class="value"><?= $detail_task['task_name'] ?></span>
              </div>
              <div class="info-row">
                <span class="label">Responsible</span>
                <span class="colon">:</span>
                <span class="value"><?= $detail_task['nama'] ?></span>
              </div>
              <div class="info-row">
                <span class="label">Description</span>
                <span class="colon">:</span>
                <span class="value" style="white-space: pre-line;"><?= $detail_task['description'] ?></span>
              </div>
              <div class="info-row">
                <span class="label">Start Date</span>
                <span class="colon">:</span>
                <span class="value"><?= tgl_indo(date('Y-m-d', strtotime($detail_task['start_date']))) ?></span>
              </div>
              <div class="info-row">
                <span class="label">Due Date</span>
                <span class="colon">:</span>
                <span class="value"><?= tgl_indo(date('Y-m-d', strtotime($detail_task['due_date']))) ?></span>
              </div>
              <div class="info-row">
                <span class="label">Attachment</span>
                <span class="colon">:</span>
                <span class="value">
                  <?php
                  if ($detail_task['attachment']) {
                    $attach = explode(';', $detail_task['attachment']);
                    echo "<ol>";
                    foreach ($attach as $att) {
                      $url = site_url('uploads/task_comment/' . $att);
                      echo "<li><a href='" . $url . "'>" . $att . "</a></li>"
                  ?>
                  <?php }
                    echo "</ol>";
                  } else {
                    echo "-";
                  } ?>
                </span>
              </div>
              <hr>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
              <div>
                <h3 class="text-center">Activity</h3>
                <form id="form-comment">
                  <input type="hidden" name="id_detail" id="id_detail" value="<?= $detail_task['id_detail'] ?>">
                  <div id="drag-drop-area"></div>
                  <div class="form-group">
                    <textarea name="comment" id="coment" class="form-control mt-4" placeholder="comment"></textarea>
                  </div>
                  <div>
                    <button type="submit" class="btn btn-warning" id="btn-submit-activity">Add Activity</button>
                  </div>
                </form>
                <div id="result"></div>
              </div>
            </div>
          </div>
          <?php foreach ($task_comment_member as $x) {
            if ($x->member == $task_comment['responsible']) {
          ?>
              <div class="row justify-content-center mt-3">
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                  <div style="margin-left: 20px;">
                    <span style="font-size:14px;"> <?= date('d M Y H:i:s', strtotime($x->date_created)) ?></span>
                  </div>
                  <div class="card p-2" style="background-color: #4CAF50;color:aliceblue;border-radius:20px;">
                    <span>
                      <div>
                        <?= $x->nama ?> :
                        <p style="white-space: pre-line;">
                          <b> <?= $x->comment_member ?></b>
                        </p>
                      </div>
                    </span>
                    <?php if ($x->attachment != null) {
                      $attach = explode(';', $x->attachment);
                    ?>
                      <hr>
                      Attachment :
                      <b>
                        <?php foreach (explode(';', $x->attachment_name) as $key => $xx) { ?>
                          <a style="color: white;" href="<?= base_url('uploads/task_comment/' . $attach[$key]) ?>" download>
                            <?= $xx . " || " ?>
                          </a>
                      <?php }
                      } ?>

                      </b>
                  </div>
                </div>
              </div>
            <?php } else { ?>
              <div class="row justify-content-center mt-3">
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                  <div>
                    <span style="font-size:14px;"> <?= date('d M Y H:i:s', strtotime($x->date_created)) ?></span>
                  </div>
                  <div class="card p-2" style="background-color: #4287f5;color:aliceblue;border-radius:20px;">
                    <span>
                      <div>
                        <?= $x->nama ?> :
                        <p style="white-space: pre-line;">
                          <b> <?= $x->comment_member ?></b>
                        </p>
                      </div>
                    </span>
                    <?php if ($x->attachment != null) {
                      $attach = explode(';', $x->attachment);
                    ?>
                      <hr>
                      Attachment :
                      <b> <?php foreach (explode(';', $x->attachment_name) as $key => $xx) { ?>
                          <a style="color: white;" href="<?= base_url('uploads/task_comment/' . $attach[$key]) ?>" download>
                            <?= $xx . " || " ?>
                          </a>
                      <?php }
                        } ?>

                      </b>
                  </div>
                </div>
              </div>
          <?php }
          } ?>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->