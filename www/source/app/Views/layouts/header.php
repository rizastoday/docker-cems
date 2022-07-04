<header id="header" class="c-header c-header-light glass c-header-fixed d-flex flex-row justify-content-between p-3">
   <button id="btn-sidebar2" class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
      <span class="sidebar-icon c-icon c-icon-lg font-weight-bold cil-menu"></span>
   </button>
   <a class="c-header-brand d-lg-none c-header-brand-xs-up-center" href="#">
      <img src="<?= '/' ?>image/LOGO_CEMS.png" width="125">
   </a>
   <div class="d-flex flex-row">
      <button id="btn-sidebar" class="c-header-toggler c-class-toggler mfs-3 d-md-down-none m-auto" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
         <span class="sidebar-icon c-icon c-icon-lg font-weight-bold cil-menu"></span>
      </button>
      <h4 class="font-weight-bold my-auto d-md-down-none ml-3 text-uppercase"><?= $title ?></h4>
   </div>
   <ul class="c-header-nav">
      <li class="c-header-nav-item dropdown">
         <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <div class="c-avatar">
                <i class="c-avatar-img cil-bell fa-2x list-notification-icon" data-toggle="tooltip" data-placement="top" title="Notification on This Page"></i>
            </div>
         </a>
         <div class="dropdown-menu dropdown-menu-right pt-0" id="list-notification">
            <span class="dropdown-item font-weight-bold">
               No Notification
            </span>
         </div>
      </li>
      <li class="c-header-nav-item dropdown">
         <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <div class="c-avatar">
                <i class="c-avatar-img cil-user fa-2x"></i>
            </div>
         </a>
         <div class="dropdown-menu dropdown-menu-right">
            <span class="dropdown-item" id="profile-modal">
                <i class="cil-user c-icon mr-2"></i>
               Profile
            </span>
            <!-- <span class="dropdown-item cursor-pointer" onclick="changeTheme()" >
               <i class=" cil-moon c-icon mr-2" id="icon-theme" data-toggle="tooltip" data-placement="top" title="Change Theme"></i>
               Change Theme
            </span> -->
            <hr class="my-0"/>
            <a class="dropdown-item" href="<?= '/' ?>auth/logout">
                <i class="cil-account-logout c-icon mr-2"></i>
               Logout
            </a>
         </div>
      </li>
   </ul>
</header>


<div class="modal fade" id="modalProfile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">
                  User Profile
               </h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
         </div>
         <div class="modal-body modal-sync">
            <div class="p-3">
               <div class="card">
                  <div class="card-body">
                     <table class="table table-sm" style="border: none !important">
                           <tr>
                              <td>Username</td>
                              <td> <?= session()->get('user_name') ?> </td>
                           </tr>
                           <tr>
                              <td>E-Mail</td>
                              <td> <?= session()->get('email') ?> </td>
                           </tr>
                           <tr>
                              <td>Group Role</td>
                              <td> <?= implode(',', session()->get('group')) ?> </td>
                           </tr>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<?= $this->section('scripts') ?>
<script>
   document.querySelector('#profile-modal').onclick = () => {
      new coreui.Modal(document.getElementById('modalProfile'), {
            backdrop: 'static'
        }).show()
   }
</script>
<?= $this->endSection() ?>