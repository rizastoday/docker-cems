<div class="c-sidebar c-sidebar-black c-sidebar-fixed c-sidebar-unfoldable c-sidebar-lg-show" id="sidebar" ref="sidebar">
    <div class="c-sidebar-brand" style="background: transparent !important;">
        <h2 class="c-sidebar-brand-full">
            <img src="<?= base_url() ?>/image/LOGO_CEMS.png" width="125">
        </h2>
        <h2 class="c-sidebar-brand-minimized">
            <img src="<?= base_url() ?>/image/LOGO_CEMS-mini.png" width="25">
        </h2>
    </div>
    <ul class="c-sidebar-nav">
        <?php if(in_array("WEB.SIDEBAR.DASHBOARD", session()->get('role'))): ?>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="<?= base_url('/dashboard/' )?>">
                    <i class="c-sidebar-nav-icon fa fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
        <?php endif; ?>
        <?php if(in_array("WEB.SIDEBAR.TRENDING", session()->get('role'))): ?>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="<?= base_url('/dashboard/trending') ?>">
                    <i class="c-sidebar-nav-icon fa fa-chart-line"></i> Trending
                </a>
            </li>
        <?php endif; ?>
        <?php if(in_array("WEB.SIDEBAR.REPORTING", session()->get('role'))): ?>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="<?= base_url('/dashboard/reporting') ?>">
                    <i class="c-sidebar-nav-icon fa fa-share-square"></i> Reporting
                </a>
            </li>
        <?php endif; ?>
        <?php if(in_array("WEB.SIDEBAR.SYNCLOG", session()->get('role'))): ?>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="<?= base_url('/dashboard/sync') ?>">
                    <i class="c-sidebar-nav-icon fa fa-sync"></i> Syncronize Log
                </a>
            </li>
        <?php endif; ?>
        <?php if(in_array("WEB.SIDEBAR.SYNCLOG", session()->get('role'))): ?>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="<?= base_url('/dashboard/sync_error') ?>">
                    <i class="c-sidebar-nav-icon fa fa-times"></i> Error Log
                </a>
            </li>
        <?php endif; ?>
        <?php if(in_array("WEB.SIDEBAR.ALARM", session()->get('role'))): ?>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="<?= base_url('/dashboard/deviation') ?>">
                    <i class="c-sidebar-nav-icon fa fa-exclamation-circle"></i> Alarm
                </a>
            </li>
        <?php endif; ?>
        <?php if(in_array("WEB.SIDEBAR.SETTING", session()->get('role'))): ?>
            <li class="c-sidebar-nav-dropdown text-white">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="c-sidebar-nav-icon fa fa-cogs"></i> Setting
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <?php if(in_array("WEB.SIDEBAR.SETTING.COMPANY", session()->get('role'))): ?>
                        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="<?= base_url() ?>/setting/companyProfile"> Company Profile</a></li>
                    <?php endif; ?>
                    <?php if(in_array("WEB.SIDEBAR.SETTING.CEMS", session()->get('role'))): ?>
                        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="<?= base_url() ?>/cems/index"> Cems</a></li>
                    <?php endif; ?>
                    <?php if(in_array("WEB.SIDEBAR.SETTING.MAINTENANCE.SCHEDULE", session()->get('role'))): ?>
                        <!-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="<?= base_url() ?>/setting/schedule"> Maintenance Schedule</a></li> -->
                    <?php endif; ?>
                    <?php if(in_array("WEB.SIDEBAR.SETTING.PARAMETER", session()->get('role'))): ?>
                        <!-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="<?= base_url() ?>/setting/parameter"> Parameter</a></li> -->
                    <?php endif; ?>
                    <?php if(in_array("WEB.SIDEBAR.SETTING.SISPEK", session()->get('role'))): ?>
                        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="<?= base_url() ?>/setting/sispek"> SISPEK</a></li>
                    <?php endif; ?>
                    <?php if(in_array("WEB.SIDEBAR.SETTING.EMAIL", session()->get('role'))): ?>
                        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="<?= base_url() ?>/setting/email-list"> E-Mail Notification</a></li>
                    <?php endif; ?>
                    <?php if(in_array("WEB.SIDEBAR.SETTING.NOTIFICATION", session()->get('role'))): ?>
                        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="<?= base_url() ?>/setting/notification"> Notification</a></li>
                    <?php endif; ?>
                    <?php if( in_array('Superuser', session()->get('group')) ): ?>
                        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="<?= base_url() ?>/setting/account-management"> Account Management</a></li>
                    <?php endif ?>
                    <?php if(in_array("WEB.SIDEBAR.SETTING.HISTORY", session()->get('role'))): ?>
                        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="<?= base_url() ?>/history/index"> History</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
    <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent" data-class="c-sidebar-unfoldable"></button>
</div>