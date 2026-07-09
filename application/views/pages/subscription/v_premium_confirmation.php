<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<style>
  .col-xs-3 {
    width: 25%;
    background-color: #004e81;
  }


  .btn_footer_panel .tag_ {
    padding-top: 37px;
  }

  tr>th {
    /* background-color: #e91f62; */
    background-color: #3e51b4;
    color: white;
  }

  .col-centered {
    float: none;
    margin: 0 auto;
  }

  .dt-length label {
    margin-left: 8px;
    /* Adjust this value (e.g., 5px, 10px, 0.5em) as needed */
  }

  table.dataTable>thead>tr>th {
    padding: 0 5px 0 5px;
    height: 30px;
  }

  table.dataTable>tbody>tr>td {
    padding: 1px 5px 1px 5px;
  }

  .btn-di-td {
    padding: 0.125rem 0.25rem;
  }
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 col-12">
      <h1 class="page-title">Premium Confirmation List</h1>
      <div class="card shadow mb-4">
        <!-- <div class="card-header d-flex justify-content-between align-items-center">
          <p class="card-title"><strong>List Cabang</strong></p>
        </div> -->
        <div class="card-body" id="user">
          <!-- <div class="d-flex justify-content-end align-items-center"> -->
          <div class="d-flex align-items-center">
          </div>
          <div class="table-responsive">
            <table id="user-table" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">Nama Perusahaan</th>
                  <th class="text-center">Paket</th>
                  <th class="text-center">Bulan</th>
                  <th class="text-center">Tanggal Mulai</th>
                  <th class="text-center">Tanggal Selesai</th>
                  <th class="text-center">Metode Bayar</th>
                  <th class="text-center">Nominal</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">#</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div> <!-- .col-12 -->
  </div> <!-- .row -->
</div> <!-- .container-fluid -->

<div class="modal fade" id="approval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Approval Premium</h4>
      </div>
      <div class="modal-body">
        <form id="approval_form">
          <div class="row">
            <input type="hidden" name="id_approval" id="id_approval">
            <div class="form-group ml-3">
              <label class="form-label">Opsi Approval</label>
              <select name="confirmation" id="confirmation" class="form-control w-100 ">
                <option selected disabled>:: Pilih Opsi </option>
                <option value="1">Setuju</option>
                <option value="2">Tidak Setuju</option>
              </select>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="confirm_premium()">Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Edit Premium</h4>
      </div>
      <div class="modal-body">
        <form id="edit_form">
          <div class="row">
            <input type="hidden" name="id_edit" id="id_edit">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Paket</label>
                <select name="paket" id="upd_paket" class="form-control w-100 ">
                  <option selected disabled>:: Pilih Opsi </option>
                  <option value="Bangsawan Muda">Bangsawan Muda</option>
                  <option value="Kesatria Sejati">Kesatria Sejati</option>
                  <option value="Raja Sultan">Raja Sultan</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Durasi (Bulan)</label>
                <input type="number" name="total_bulan" id="upd_total_bulan" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Tanggal Mulai</label>
                <input type="datetime-local" name="tanggal_mulai" id="upd_tanggal_mulai" class="form-control">
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" id="upd_tanggal_selesai" class="form-control">
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success text-white d-none" id="btn-reaktif">Re-Aktif</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="confirm_update()">Save</button>
      </div>
    </div>
  </div>
</div>