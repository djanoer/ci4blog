<?= $this->extend('backend/layout/pages-layout.php') ?>
<?= $this->section('content'); ?>

<div class="page-header">
  <div class="row">
    <div class="col-md-12 col-sm-12">
      <div class="title">
        <h4>Profile</h4>
      </div>
      <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="<?= route_to('admin.home') ?>">Home</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            Profile
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
    <div class="pd-20 card-box height-100-p">
      <div class="profile-photo">
        <a href="modal" class="edit-avatar"><i class="fa fa-pencil"></i></a>
        <img src="<?= get_user()->picture == null ? '/images/users/default-avatar.jpg' : '/images/users/'.get_user()->picture ?>" alt="" class="avatar-photo ci-avatar-photo">

      </div>
      <h5 class="text-center h5 mb-0 ci-user-name"><?= get_user()->name ?></h5>
      <p class="text-center text-muted font-14 ci-user-email">
      <?= get_user()->email ?>
      </p>
      
    </div>
  </div>
  <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
    <div class="card-box height-100-p overflow-hidden">
      <div class="profile-tab height-100-p">
        <div class="tab height-100-p">
          <ul class="nav nav-tabs customtab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#personal_details" role="tab">Personal details</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#change_password" role="tab">Change password</a>
            </li>
          </ul>
          <div class="tab-content">
            <!-- Timeline Tab start -->
            <div class="tab-pane fade show active" id="personal_details" role="tabpanel">
              <div class="pd-20">
                <form action="<?= route_to('update-personal-details') ?>">
                  <?= csrf_field(); ?>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="">Nama</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter full name">
                        <span class="text-danger error-text name_error"></span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter username">
                        <span class="text-danger error-text username_error"></span>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="">Bio</label>
                    <textarea name="bio" id="" cols="30" rows="10" class="form-control" placeholder="Bio....."></textarea>
                    <span class="text-danger error-text bio_error"></span>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
            <!-- Timeline Tab End -->
            <!-- Tasks Tab start -->
            <div class="tab-pane fade" id="change_password" role="tabpanel">
              <div class="pd-20 profile-task-wrap">
                ----- Change password ------
              </div>
            </div>
            <!-- Tasks Tab End -->

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>