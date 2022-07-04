<?= $this->extend('layouts/layout') ?>

<?= $this->section('styles') ?>
<style>
    .list-item:hover {
        background-color: rgb(240, 243, 245) !important;
        transition: .25s ease-in-out all;
        border-radius: 1000px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card" id="setting">
    <div class="card-body p-5">

        <div class="row">
            <?php if ($type == "company-profile") { ?>
                <div class="col-md-6" id="formCompanyProfile">
                    <input type="hidden" name="companyProfileId" value="<?= isset($company_profile->companyProfileId) ? $company_profile->companyProfileId : "" ?>" />
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Name</label>
                        <div class="col-md-8">
                            <input class="form-control" name="companyName" type="text" placeholder="Enter your Company Name" value="<?= isset($company_profile->name) ? $company_profile->name : "" ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Telephone</label>
                        <div class="col-md-8">
                            <input class="form-control" name="noTelp" type="text" placeholder="(xxxx) xxxxxxxx" value="<?= isset($company_profile->noTelp) ? $company_profile->noTelp : "" ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Address</label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="address"><?= isset($company_profile->address) ? $company_profile->address : "" ?></textarea>
                            <button type="button" class="btn btn-success mt-3" onclick="saveCompanyProfile();"><i class="fa fa-paper-plane"></i> Save</button>
                        </div>
                    </div>
                </div>
            <?php } ?>


            <?php if ($type == "sispek") { ?>
                <div class="col-md-6" id="formSispek">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Base URL</label>
                        <div class="col-md-8">
                            <input class="form-control" name="baseURL" type="text" value="<?= isset($sispek->baseURL) ? $sispek->baseURL : "" ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">App Id</label>
                        <div class="col-md-8">
                            <input class="form-control" name="appId" type="text" value="<?= isset($sispek->appId) ? $sispek->appId : "" ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">App Secret</label>
                        <div class="col-md-8">
                            <input class="form-control" name="appSecret" type="password" value="<?= isset($sispek->appSecret) ? $sispek->appSecret : "" ?>">
                            <button class="btn btn-success mt-3" onclick="saveSispek()"><i class="fa fa-paper-plane"></i> Save</button>
                        </div>
                    </div>
                </div>
            <?php } ?>


            <?php if ($type == "notification") { ?>
                <div class="col-md-6" id="formNotification">
                    <div>
                        <h5>Telegram Bot</h5>
                        <span>Update carefully</span>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Name</label>
                            <div class="col-md-8">
                                <input class="form-control" name="telegramName" type="text" placeholder="Bot Telegram Account" value="<?= isset($notification->telegramName) ? $notification->telegramName : "" ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Token</label>
                            <div class="col-md-8">
                                <input class="form-control" name="telegramToken" type="text" placeholder="Telegram Token" value="<?= isset($notification->telegramToken) ? $notification->telegramToken : "" ?>">
                            </div>
                        </div>

                        <h5 class="mt-5">Chanel Setting</h5>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Name</label>
                            <div class="col-md-8">
                                <input class="form-control" name="chanelName" type="text" placeholder="Chanel Name" value="<?= isset($notification->chanelName) ? $notification->chanelName : "" ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Chat Id</label>
                            <div class="col-md-8">
                                <input class="form-control" name="chatId" type="text" placeholder="Chanel id" value="<?= isset($notification->chatId) ? $notification->chatId : "" ?>">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-success" onclick="saveNotification();"><i class="fa fa-check"></i> Save</button>
                        </div>

                        <div class="form-group mt-5">
                            <label class="mt">Sending Message</label>
                            <textarea class="form-control" name="messageText" style="height: 100px;">Just Test From Setting Menu. Sklip this information</textarea>
                            <button onclick="testConnection()" class="btn btn-info mt-3"><i class="fa fa-paper-plane"></i> Send</button>
                        </div>
                    </div>
                </div>
            <?php } ?>


            <?php if ($type == "report") { ?>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Name</label>
                        <div class="col-md-8">
                            <input class="form-control" name="reportName" type="text" placeholder="Report Name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Time Format</label>
                        <div class="col-md-8">
                            <input class="form-control" name="timeFormat" type="text" placeholder="YYYY-MM-DD HH:mm:ss">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Data Group</label>
                        <div class="col-md-8">
                            <input class="form-control" name="dataGroup" type="number" placeholder="In Minute">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Monthly Report</label>
                        <div class="col-md-8">
                            <label class="c-switch c-switch-pill c-switch-success">
                                <input class="c-switch-input" name="monthlyReport" type="checkbox" checked=""><span class="c-switch-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Data Duration</label>
                        <div class="col-md-8">
                            <input class="form-control" name="dataGroup" type="number">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Template</label>
                        <div class="col-md-8">
                            <input class="form-control" name="template" type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Sheet Name</label>
                        <div class="col-md-8">
                            <input class="form-control" name="sheetName" type="text">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h4>Fields</h4>
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label">Time Format</label>
                        <div class="col-md-8">
                            <input class="form-control" name="fieldTimeFormat" type="text" placeholder="mm">
                        </div>
                    </div>

                    <table class="table table-sm table-bordered table-striped table-hover" id="tableField">
                        <thead>
                            <tr>
                                <th style="width: 50px;text-align: center;">#</th>
                                <th>Function</th>
                                <th>Fields</th>
                                <th>Alias</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">
                                    <input type="hidden" name="fieldId[]" value="${uuidv4()}" />
                                    <input type="hidden" name="addUpdate[]" value="add" />
                                    <button class="btn text-danger"><i class="fa fa-times"></i></button>
                                </td>
                                <td><input type="text" name="fieldFunc[]" placeholder="Type here" class="input-transparent" value="mean" /></td>
                                <td><input type="text" name="filedName[]" placeholder="Type here" class="input-transparent" value="SO2" /></td>
                                <td><input type="text" name="aliasName[]" placeholder="Type here" class="input-transparent" value="SO2" /></td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <input type="hidden" name="fieldId[]" value="${uuidv4()}" />
                                    <input type="hidden" name="addUpdate[]" value="add" />
                                    <button class="btn text-danger"><i class="fa fa-times"></i></button>
                                </td>
                                <td><input type="text" name="fieldFunc[]" placeholder="Type here" class="input-transparent" value="mean" /></td>
                                <td><input type="text" name="filedName[]" placeholder="Type here" class="input-transparent" value="NO" /></td>
                                <td><input type="text" name="aliasName[]" placeholder="Type here" class="input-transparent" value="NO" /></td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <input type="hidden" name="fieldId[]" value="${uuidv4()}" />
                                    <input type="hidden" name="addUpdate[]" value="add" />
                                    <button class="btn text-danger"><i class="fa fa-times"></i></button>
                                </td>
                                <td><input type="text" name="fieldFunc[]" placeholder="Type here" class="input-transparent" value="mean" /></td>
                                <td><input type="text" name="filedName[]" placeholder="Type here" class="input-transparent" value="O2" /></td>
                                <td><input type="text" name="aliasName[]" placeholder="Type here" class="input-transparent" value="O2" /></td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <input type="hidden" name="fieldId[]" value="${uuidv4()}" />
                                    <input type="hidden" name="addUpdate[]" value="add" />
                                    <button class="btn text-danger"><i class="fa fa-times"></i></button>
                                </td>
                                <td><input type="text" name="fieldFunc[]" placeholder="Type here" class="input-transparent" value="mean" /></td>
                                <td><input type="text" name="filedName[]" placeholder="Type here" class="input-transparent" value="CO" /></td>
                                <td><input type="text" name="aliasName[]" placeholder="Type here" class="input-transparent" value="CO" /></td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <input type="hidden" name="fieldId[]" value="${uuidv4()}" />
                                    <input type="hidden" name="addUpdate[]" value="add" />
                                    <button class="btn text-danger"><i class="fa fa-times"></i></button>
                                </td>
                                <td><input type="text" name="fieldFunc[]" placeholder="Type here" class="input-transparent" value="mean" /></td>
                                <td><input type="text" name="filedName[]" placeholder="Type here" class="input-transparent" value="PARTICULATE" /></td>
                                <td><input type="text" name="aliasName[]" placeholder="Type here" class="input-transparent" value="PARTICULATE" /></td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    <input type="hidden" name="fieldId[]" value="${uuidv4()}" />
                                    <input type="hidden" name="addUpdate[]" value="add" />
                                    <button class="btn text-danger"><i class="fa fa-times"></i></button>
                                </td>
                                <td><input type="text" name="fieldFunc[]" placeholder="Type here" class="input-transparent" value="mean" /></td>
                                <td><input type="text" name="filedName[]" placeholder="Type here" class="input-transparent" value="CO2_MG" /></td>
                                <td><input type="text" name="aliasName[]" placeholder="Type here" class="input-transparent" value="CO2_MG" /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php } ?>


            <?php if ($type != "report") { ?>
                <div class="col-md-6">
                    <img class="w-100" src="<?= '/' ?>image/setting.svg" alt="Setting Icon" />
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function waiting(){
        swal.fire({
            icon: 'info',
            title: 'Processing..',
            text: 'Please Wait',
            allowOutsideClick: false,
            showConfirmButton: false
        })
    }
    const success = (str) => new Promise((resolve) => {
        swal.fire({
            icon: 'success',
            title: str,
            allowOutsideClick: false,
        })
        resolve()
    })
    function error(str){
        swal.fire({
            icon: 'error',
            title: str,
            text: 'Error Occured',
            allowOutsideClick: false,
        })
    }
    function warn(str, txt){ 
        swal.fire({
            icon: 'warning',
            title: str,
            text: txt,
            allowOutsideClick: false,
        })
    }

    function testConnection (){
        if(document.querySelector('[name=messageText]').value != ''){
            waiting()
            fetch(`${notifTelegram}?message=${document.querySelector('[name=messageText]').value}`)
            .then(() => {
                success('Success Send Test Notification')
            })
            .catch(() => {
                error('Cannot Send Notification')
            })
        }else{
            warn('Cannot Send Notification', 'Fill Message Form')
        }
    }


    function saveCompanyProfile() {
        var formData = {
            "companyProfileId": $("#formCompanyProfile input[name='companyProfileId']").val(),
            "name": $("#formCompanyProfile input[name='companyName']").val(),
            "noTelp": $("#formCompanyProfile input[name='noTelp']").val(),
            "address": $("#formCompanyProfile textarea[name='address']").val()
        }
        formData[document.querySelector('meta[name=token]').content] = document.querySelector('meta[name=hash]').content
        
        waiting();

        $.ajax({
            url: "<?= '/'; ?>setting/saveCompanyProfile/<?= isset($company_profile->companyProfileId) ? $company_profile->companyProfileId : "0" ?>",
            type: "POST",
            data: formData,
            dataType: 'json',
            success: function(json) {
                if (json.status == 200) {
                    swal.fire({
                        icon: 'success',
                        title: json.Message,
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
                    })
                } else {
                    error("Error Saved Data");
                }
            },
            error: function(errormessage) {
                error("Cannot Process Your Request, Please Reload Page and Try Again");
            }
        });
    }

    function saveSispek() {
        var formData = {
            "baseURL": $("#formSispek input[name='baseURL']").val(),
            "appId": $("#formSispek input[name='appId']").val(),
            "appSecret": $("#formSispek input[name='appSecret']").val()
        }
        formData[document.querySelector('meta[name=token]').content] = document.querySelector('meta[name=hash]').content
        waiting();
        $.ajax({
            url: "<?= '/'; ?>setting/saveSispek/<?= isset($sispek->sispekId) ? $sispek->sispekId : "0" ?>",
            type: "POST",
            data: formData,
            dataType: 'json',
            success: function(json) {
                if (json.status == 200) {
                    fetch('<?= env('NODE_URL') ?>/triggersql',{
                        method: 'POST'
                    })
                    swal.fire({
                        icon: 'success',
                        title: json.Message,
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
                    })
                } else {
                    error("Error Saved Data");
                }
            },
            error: function(errormessage) {
                error("Cannot Process Your Request, Please Reload Page and Try Again");
            }
        });
    }

    function saveNotification() {
        swal.fire({
            icon: 'question',
            title: 'Update Notification Info',
            allowOutsideClick: false,
            showCancelButton: true
        }).then((res) => {
            if(res.value){
                var formData = {
                    "telegramName": $("#formNotification input[name='telegramName']").val(),
                    "telegramToken": $("#formNotification input[name='telegramToken']").val(),
                    "chanelName": $("#formNotification input[name='chanelName']").val(),
                    "chatId": $("#formNotification input[name='chatId']").val()
                }
                formData[document.querySelector('meta[name=token]').content] = document.querySelector('meta[name=hash]').content

                waiting();
                $.ajax({
                    url: "<?= '/'; ?>setting/saveNotification/<?= isset($notification->notificationId) ? $notification->notificationId : "0" ?>",
                    type: "POST",
                    data: formData,
                    dataType: 'json',
                    success: function(json) {
                        if (json.status == 200) {
                            // alert(json.Message);
                            swal.fire({
                                icon: 'success',
                                title: json.Message,
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.reload();
                            })
                        } else {
                            alert("Error Saved Data");
                        }
                    },
                    error: function(errormessage) {
                        alert("Cannot Process Your Request, Please Reload Page and Try Again");
                    }
                });
            }
        })
    }

    new Vue({
        el: '#setting'
    })
</script>

<?= $this->endSection() ?>