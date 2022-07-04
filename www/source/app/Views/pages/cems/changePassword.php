<?= $this->extend('layouts/layout') ?>
<?= $this->section('content') ?>
    <div id="password">
        <!-- <loading :show="control.loading"></loading> -->
        <div class="card">
            <div class="card-body p-5">
                <div class="row">
                    <?php session_start(); ?>
                    <form class="col-md-6 order-2 order-lg-1" action="<?= '/' ?>/cems/updatePassword" ref="form" @submit.prevent="Submit">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label>Current Password</label>
                            <input class="form-control" :class="model.password != '' ? '' : 'border-danger'" type="password" v-model="model.password" name="password">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input class="form-control" type="password" v-model="model.new_password" name="new_password">
                        </div>
                        <div class="form-group">
                            <label>Complete Captcha</label>
                            <div class="g-recaptcha" name="captcha" data-sitekey="<?= env('site_key') ?>"></div>
                        </div>
                        <div class="form-group pt-5">
                            <button class="btn btn-success btn-block" ref="btn" disabled type="submit">Process</button>
                        </div>
                    </form>
                    <div class="col-md-6 order-1 order-lg-2">
                        <img class="w-100" src="<?= '/' ?>image/password.svg" height="400" alt="Setting Icon" />
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src='https://www.google.com/recaptcha/api.js' async defer></script>
    <script>
        new Vue({
            el: '#password',
            data:()=>({
                model: {
                    password: '',
                    new_password: ''
                }
            }),
            watch: {
                model: {
                    handler(e){
                        if(( e.password != '' & e.password != null ) && ( e.new_password != null && e.new_password != '' )){
                            this.$refs.btn.disabled = false
                        }else {
                            this.$refs.btn.disabled = true
                        }
                    },
                    deep: true
                }
            },
            methods: {
                Submit(){
                    if(( this.model.password != '' & this.model.password != null ) && ( this.model.new_password != null && this.model.new_password != '' )){
                        
                        swal.fire({ 
                            icon: 'question', 
                            title: 'Save Update ?',
                            allowOutsideClick: false, 
                            showCancelButton: true
                        }).then(async (res) => {
                            if(res.value){
                                swal.fire({ 
                                    icon: 'info', 
                                    title: 'Please Wait',
                                    allowOutsideClick: false, 
                                    showConfirmButton: false
                                })

                                
                                await fetch(this.$refs.form.action, {
                                    method: 'POST',
                                    body: new FormData(this.$refs.form)
                                }).then(res => {
                                    if(res.ok){
                                        let json = res.json();
                                        // console.log(json)
                                        return json
                                    }else {
                                        // console.log(res)
                                        throw res
                                    }
                                })
                                .then(json => {
                                    if(json.status){
                                        Swal.fire({ 
                                            icon: 'success',
                                            title: json.message,
                                            allowOutsideClick: false,
                                        }).then((res) => {
                                            window.location.href = '<?= '/' ?>auth/logout'
                                        })
                                    }else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: json.message,
                                            allowOutsideClick: false,
                                        }).then((res) => {
                                            location.reload()
                                        })
                                    }
                                }).catch(er => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed to Update Password',
                                        allowOutsideClick: false,
                                    }).then((res) => {
                                        location.reload()
                                    })
                                })
                            }
                        })

                    }else {
                        swal.fire({
                            icon: 'error',
                            title: 'Fill all field!',
                            allowOutsideClick: false
                        })
                    }
                }
            }

        })
    </script>
<?= $this->endSection() ?>
