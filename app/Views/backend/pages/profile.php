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
        <img src="vendors/images/photo1.jpg" alt="" class="avatar-photo">

      </div>
      <h5 class="text-center h5 mb-0">Ross C. Lopez</h5>
      <p class="text-center text-muted font-14">
        Lorem ipsum dolor sit amet
      </p>
      <div class="profile-info">
        <h5 class="mb-20 h5 text-blue">Contact Information</h5>
        <ul>
          <li>
            <span>Email Address:</span>
            FerdinandMChilds@test.com
          </li>
          <li>
            <span>Phone Number:</span>
            619-229-0054
          </li>
          <li>
            <span>Country:</span>
            America
          </li>
          <li>
            <span>Address:</span>
            1807 Holden Street<br>
            San Diego, CA 92115
          </li>
        </ul>
      </div>
      <div class="profile-social">
        <h5 class="mb-20 h5 text-blue">Social Links</h5>
        <ul class="clearfix">
          <li>
            <a href="#" class="btn" data-bgcolor="#3b5998" data-color="#ffffff" style="color: rgb(255, 255, 255); background-color: rgb(59, 89, 152);"><i class="fa fa-facebook"></i></a>
          </li>
          <li>
            <a href="#" class="btn" data-bgcolor="#1da1f2" data-color="#ffffff" style="color: rgb(255, 255, 255); background-color: rgb(29, 161, 242);"><i class="fa fa-twitter"></i></a>
          </li>
          <li>
            <a href="#" class="btn" data-bgcolor="#007bb5" data-color="#ffffff" style="color: rgb(255, 255, 255); background-color: rgb(0, 123, 181);"><i class="fa fa-linkedin"></i></a>
          </li>
          <li>
            <a href="#" class="btn" data-bgcolor="#f46f30" data-color="#ffffff" style="color: rgb(255, 255, 255); background-color: rgb(244, 111, 48);"><i class="fa fa-instagram"></i></a>
          </li>
          <li>
            <a href="#" class="btn" data-bgcolor="#3d464d" data-color="#ffffff" style="color: rgb(255, 255, 255); background-color: rgb(61, 70, 77);"><i class="fa fa-dropbox"></i></a>
          </li>
        </ul>
      </div>
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
                ------ Personal details ------
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