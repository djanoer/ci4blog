<?= $this->extend('backend/layout/pages-layout.php') ?>
<?= $this->section('content'); ?>

<div class="page-header">
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <div class="title">
        <h4>Categories</h4>
      </div>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="<?= route_to('admin.home') ?>">Home</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            Categories
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12 mb-4">
    <div class="card card-box">
      <div class="card-header">
        <div class="clearfix">
          <div class="pull-left">
            Categories
          </div>
          <div class="pull-right">
            <a href="" class="btn btn-default btn-sm p-0" role="button" id="add_category_btn">
              <i class="fa fa-plus-circle"></i> Add category
            </a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-sm table-borderless table-hover table-striped">
          <thead>
            <tr>
              <td scope="col">#</td>
              <td scope="col">Categories name</td>
              <td scope="col">N. of sub categories</td>
              <td scope="col">Action</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td scope="row">1</td>
              <td>---</td>
              <td>---</td>
              <td>---</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-12 mb-4">
    <div class="card card-box">
      <div class="card-header">
        <div class="clearfix">
          <div class="pull-left">
            Sub categories
          </div>
          <div class="pull-right">
            <a href="" class="btn btn-default btn-sm p-0" role="button" id="add_subcategory_btn">
              <i class="fa fa-plus-circle"></i> Add sub category
            </a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-sm table-borderless table-hover table-striped">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Sub category name</th>
              <th scope="col">Parent category</th>
              <th scope="col">N. of post(s)</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td scope="row">1</td>
              <td>---</td>
              <td>---</td>
              <td>---</td>
              <td>---</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include('modals/category-modal-form.php'); ?>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
  $(document).ready(function() {
    $(document).on('click', '#add_category_btn', function(e) {
      e.preventDefault();
      var modal = $('body').find('div#category-modal');
      var modal_title = 'Add category';
      var modal_btn_text = 'ADD';
      modal.find('.modal-title').html(modal_title);
      modal.find('.modal-footer > button.action').html(modal_btn_text);
      modal.find('input.error-text').html('');
      modal.find('input[type="text"]').val('');
      modal.modal('show');
    });

    $('#add_category_form').on('submit', function(e) {
      e.preventDefault();
      //CSRF Hash
      var csrfName = $('.ci_csrf_data').attr('name'); // CSRF token name
      var csrfHash = $('.ci_csrf_data').val(); // CSRF Hash
      var form = this;
      var modal = $('body').find('div#category-modal');
      var formdata = new FormData(form);
      formdata.append(csrfName, csrfHash);

      $.ajax({
        url: $(form).attr('action'),
        method: $(form).attr('method'),
        data: formdata,
        processData: false,
        dataType: 'json',
        contentType: false,
        cache: false,
        beforeSend: function() {
          toastr.remove();
          $(form).find('span.error-text').text('');
        },
        success: function(response) {
          //Update CSRF Hash
          $('.ci_csrf_data').val(response.token);

          if ($.isEmptyObject(response.error)) {
            if (response.status == 1) {
              $(form)[0].reset();
              modal.modal('hide');
              toastr.success(response.msg);
            } else {
              toastr.error(response.msg);
            }
          } else {
            $.each(response.error, function(prefix, val) {
              $(form).find('span.' + prefix + '_error').text(val);
            });
          }
        }
      });
    });
  });
</script>
<?= $this->endSection(); ?>



<!-- ==================================== -->

<!-- <script>
  $(document).on('click', '#add_category_btn', function(e) {
    e.preventDefault();
    var modal = $('body').find('div#category-modal');
    var modal_title = 'Add category';
    var modal_btn_text = 'ADD';
    modal.find('.modal-title').html(modal_title);
    modal.find('.modal-footer > button.action').html(modal_btn_text);
    modal.find('input.error-text').html('');
    modal.find('input[type="text"]').val('');
    modal.modal('show');
  });

  $('#add_category_form').on('submit', function(e) {
    e.preventDefault();
    //CSRF Hash
    var csrfName = $('.ci_csrf_data').attr('name'); // CSRF token name
    var csrfHash = $('.ci_csrf_data').val(); // CSRF Hash
    var form = this;
    var modal = $('body').find('div#category-modal');
    var formdata = new FormData(form);
    formdata.append(csrfName, csrfHash);

    $.ajax({
      url: $(form).attr('action'),
      method: $(form).attr('method'),
      data: formdata,
      processData: false,
      dataType: 'json',
      contentType: false,
      cache: false,
      beforeSend: function() {
        toastr.remove();
        $(form).find('span.error-text').text('');
      },
      success: function(response) {
        //Update CSRF Hash
        $('.ci_csrf_data').val(response.token);

        if ($.isEmptyObject(response.error)) {
          if (response.status == 1) {
            $(form)[0].reset();
            modal.modal('hide');
            toastr.success(response.msg);
          } else {
            toastr.error(response.msg);
          }
        } else {
          $.each(response.error, function(prefix, val) {
            $(form).find('span.' + prefix + '_error').text(val);
          });
        }
      }
    });
  });
</script> -->