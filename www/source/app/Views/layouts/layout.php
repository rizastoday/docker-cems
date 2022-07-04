<!DOCTYPE html>
<html lang="en">
    <head>
        <?= $this->include('layouts/_head') ?>
        <?= $this->renderSection('styles'); ?>
    </head>
    <body class="c-app d-flex flex-column" data-theme="light">
    <?= $this->include('layouts/sidebar') ?>

    <div class="c-wrapper">
        <?= $this->include('layouts/header') ?>
        <div class="c-body">
            <div class="c-main p-0">
                <div class="container-fluid pt-3">
                    <?= $this->renderSection('content'); ?>
                </div>
            </div>
        </div>
        <?= $this->include('layouts/footer') ?>
    </div>


    <?= $this->include('layouts/_foot') ?>
    <?= $this->renderSection('scripts'); ?>

</body>

</html>