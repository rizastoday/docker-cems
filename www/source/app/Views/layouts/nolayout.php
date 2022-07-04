<!DOCTYPE html>
<html lang="en">
    <head>
        <?= $this->include('layouts/_head') ?>
        <?= $this->renderSection('styles'); ?>
    </head>
    <body class="c-app bg-blue-50">

        <div class="c-wrapper">
            <div class="c-body">
                <div class="c-main p-0">
                    <div class="container-fluid">
                        <?= $this->renderSection('content'); ?>
                    </div>
                </div>
            </div>
        </div>


        <?= $this->include('layouts/_foot') ?>
        <?= $this->renderSection('scripts'); ?>

</body>

</html>