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
        <table class="table table-sm table-borderless table-hover table-striped" id="categories-table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Categories name</th>
              <th scope="col">N. of sub categories</th>
              <th scope="col">Action</th>
              <th scope="col">Ordering</th>
            </tr>
          </thead>
          <tbody></tbody>
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
`
<?php include('modals/category-modal-form.php'); ?>
<?php include('modals/edit-category-modal-form.php'); ?>
<?php include('modals/subcategory-modal-form.php'); ?>

<?= $this->endSection(); ?>

<?= $this->section('stylecheets') ?>
<link rel="stylesheet" href="/backend/src/plugins/datatables/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/backend/src/plugins/datatables/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="/extra-assets/jquery-ui-1.13.3/jquery-ui.min.css">
<link rel="stylesheet" href="/extra-assets/jquery-ui-1.13.3/jquery-ui.structure.min.css">
<link rel="stylesheet" href="/extra-assets/jquery-ui-1.13.3/jquery-ui.theme.min.css">
<?= $this->endSection() ?>

<?= $this->section('scripts'); ?>
<script src="/backend/src/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="/backend/src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
<script src="/backend/src/plugins/datatables/js/dataTables.responsive.min.js"></script>
<script src="/backend/src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
<script src="/extra-assets/jquery-ui-1.13.3/jquery-ui.min.js"></script>
<script>
  $(document).ready(function() {
    // Definisikan categories_DT di scope yang dapat diakses oleh semua fungsi
    var categories_DT;

    // Inisialisasi DataTable
    categories_DT = $('#categories-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: "<?= route_to('get-categories') ?>",
      dom: "Brtip",
      info: true,
      fnCreatedRow: function(row, data, index) {
        $('td', row).eq(0).html(index + 1);
        $('td', row).parent().attr('data-index', data[0]).attr('data-ordering', data[4]);
      },
      columnDefs: [{
          orderable: false,
          targets: [0, 1, 2, 3]
        },
        {
          visible: false,
          targets: 4
        }
      ],
      order: [
        [4, 'asc']
      ],
    });

    // Event listener untuk tombol add category
    $(document).on('click', '#add_category_btn', function(e) {
      e.preventDefault();
      var modal = $('body').find('div#category-modal');
      var modal_title = 'Add category';
      var modal_btn_text = 'ADD';
      modal.find('.modal-title').html(modal_title);
      modal.find('.modal-footer > button.action').html(modal_btn_text);
      modal.find('span.error-text').html('');
      modal.find('input[type="text"]').val('');
      modal.modal('show');
    });

    // Event listener untuk submit form
    $('#add_category_form').on('submit', function(e) {
      e.preventDefault();
      handleFormSubmit(this, 'div#category-modal');
    });

    // Event listener untuk tombol edit category
    $(document).on('click', '.editCategoryBtn', function(e) {
      e.preventDefault();
      var category_id = $(this).data('id');
      var url = "<?= route_to('get-category') ?>";
      $.get(url, {
        category_id: category_id
      }, function(response) {
        var modal_title = 'Edit category';
        var modal_btn_text = 'Save changes';
        var modal = $('body').find('div#edit-category-modal');
        modal.find('form').find('input[type="hidden"][name="category_id"]').val(category_id);
        modal.find('.modal-title').html(modal_title);
        modal.find('.modal-footer > button.action').html(modal_btn_text);
        modal.find('input[type="text"]').val(response.data.name);
        modal.find('span.error-text').html('');
        modal.modal('show');
      }, 'json');
    });

    // Event listener untuk submit form update
    $('#update_category_form').on('submit', function(e) {
      e.preventDefault();
      handleFormSubmit(this, 'div#edit-category-modal');
    });

    // Fungsi untuk menangani submit form
    function handleFormSubmit(form, modalSelector) {
      var csrfName = $('.ci_csrf_data').attr('name');
      var csrfHash = $('.ci_csrf_data').val();
      var modal = $('body').find(modalSelector);
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
          $(form).find('span.error-text').text('');
        },
        success: function(response) {
          $('.ci_csrf_data').val(response.token);

          if ($.isEmptyObject(response.error)) {
            if (response.status == 1) {
              form.reset();
              modal.modal('hide');
              showCustomAlert(response.msg, 'success');
              if (categories_DT) {
                categories_DT.ajax.reload(null, false);
              } else {
                console.error("categories_DT is not defined");
              }
            } else {
              showCustomAlert(response.msg, 'error');
            }
          } else {
            $.each(response.error, function(prefix, val) {
              $(form).find('span.' + prefix + '_error').text(val);
            });
          }
        },
        error: function(xhr, status, error) {
          console.error("Ajax error:", status, error);
          showCustomAlert("An error occurred: " + error, 'error');
        }
      });
    }

    $(document).on('click', '.deleteCategoryBtn', function(e) {
      e.preventDefault();
      var category_id = $(this).data('id');
      var url = "<?= route_to('delete-category') ?>";
      // alert(category_id);
      swal.fire({
        title: 'Are you sure?',
        html: 'You want to delete this category',
        showCloseButton: true,
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Yes, Delete',
        cancelButtonColor: '#d33',
        confirmButtonColor: '#3085d6',
        width: 300,
        allowOutsideClick: false
      }).then(function(result) {
        if (result.value) {
          // alert('Delete now');
          $.get(url, {
            category_id: category_id
          }, function(response) {
            if (response.status == 1) {
              categories_DT.ajax.reload(null, false);
              showCustomAlert(response.msg, 'success');
            } else {
              showCustomAlert(response.msg, 'error');
            }
          }, 'json');
        }
      });
    });

    $('table#categories-table').find('tbody').sortable({
      update: function(event, ui) {
        $(this).children().each(function(index) {
          if ($(this).attr('data-ordering') != (index + 1)) {
            $(this).attr('data-ordering', (index + 1)).addClass('updated');
          }
        });
        var positions = [];

        $('.updated').each(function() {
          positions.push([$(this).attr('data-index'), $(this).attr('data-ordering')]);
          $(this).removeClass('updated');
        });

        var url = "<?= route_to('reorder-categories') ?>";
        $.get(url, {
          positions: positions
        }, function(response) {
          if (response.status == 1) {
            categories_DT.ajax.reload(null, false);
            showCustomAlert(response.msg, 'success');
          }
        }, 'json');
      }
    });

    $(document).on('click', '#add_subcategory_btn', function(e) {
      e.preventDefault();
      var modal_title = 'Add Sub Category';
      var modal_btn_text = 'ADD';
      var modal = $('body').find('div#sub-category-modal');
      var select = modal.find('select[name="parent_cat"]');
      var url = "<?= route_to('get-parent-categories') ?>";
      $.getJSON(url, {
        parent_category_id: null
      }, function(response) {
        select.find('option').remove();
        select.html(response.data);
      });
      modal.find('.modal-title').html(modal_title);
      modal.find('.modal-footer > button.action').html(modal_btn_text);
      modal.find('input[type="text"]').val('');
      modal.find('textarea').html('');
      modal.find('span.error-text').html('');
      modal.modal('show');
    });

    $(document).on('submit', '#add_subcategory_form', function(e) {
      e.preventDefault();
      //CSRF
      var csrfName = $('.ci_csrf_data').attr('name'); //CSRF token name
      var csrfHash = $('.ci_csrf_data').val(); //CSRF hash
      var form = this;
      var modal = $('body').find('div#sub-category-modal');
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
          $(form).find('span.error-text').text('');
        },
        success:function(response){
          //Update CSRF hash
          $('.ci_csrf_data').val(response.token);

          if($.isEmptyObject(response.error)){
            if (response.status == 1){
              $(form)[0].reset();
              modal.modal('hide');
              showCustomAlert(response.msg, 'success');
            }
          }else{
            $.each(response.error, function(prefix, val){
              $(form).find('span.'+prefix+'_error').text(val);
            });
          }
        }
      });
    });

  });
</script>
<?= $this->endSection(); ?>