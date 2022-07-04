<?= $this->extend('layouts/layout') ?>
<?= $this->section('content') ?>
    <div id="app" class="card" v-cloak>
        <div class="card-body p-5 d-flex flex-row">
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
                        <button class="btn btn-primary" @click="saveNotification();"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mt-5">
                    <label class="mt">Sending Message</label>
                    <textarea class="form-control" name="messageText" style="height: 100px;">Just Test From Setting Menu. Skip this information</textarea>
                    <button @click="testConnection()" class="btn btn-primary float-right mt-3"><i class="fa fa-paper-plane"></i> Send</button>
                </div>
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
        new Vue({
            el: '#app',
            methods: {    
                saveNotification() {
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
                                url: "<?= base_url('/') ?>setting/saveNotification/<?= isset($notification->notificationId) ? $notification->notificationId : "0" ?>",
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
                },
                testConnection (){
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
            }
        })
    </script>
<?= $this->endSection() ?>