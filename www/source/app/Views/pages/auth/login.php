<?= $this->extend('layouts/nolayout') ?>

<?= $this->section('styles') ?>
<style>
    .float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #25d366;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        font-size: 30px;
        box-shadow: 2px 2px 3px #999;
        z-index: 100;
    }
    .float:hover{
        color: white;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="row d-flex flex-column px-lg-5" style="height: 100vh;" id="registration" v-cloak>
        <div class="col-md-10 m-auto d-flex">
            <div class="card my-auto w-100 shadow-md">
                <div class="card-body lg-px-5 sm-px-2 md-px-2 xl-px-5 d-flex flex-column justify-content-center align-items-center" style="border-radius: 0 !important;">
                    <div class="row w-100">
                        <div class="col-lg-6 col-md-12 d-flex flex-column">
                            <img src="<?= base_url() ?>/image/factory.gif" class="mx-auto w-4/5 d-none d-lg-block" >
                        </div>
                        <div class="col-lg-6 col-md-12 d-flex flex-column">
                            <form action="<?= base_url('/auth/auth_http') ?>" class="my-auto" method="POST" autocomplete="off" ref="form" @submit.prevent="Submit">
                                <div class="d-flex flex-row justify-content-between">
                                    <h3 class="my-auto">Sign in</h3>
                                    <img src="<?= base_url() ?>/image/LOGO_CEMS.png" height="75" >
                                </div>
                                <?= csrf_field() ?>
                                <div class="input-group my-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text border-gray rounded-0"> <i style="color: #aaa;" class="fa fa-at"></i> </span>
                                    </div>
                                    <input autocomplete="email" style="height: 45px;" type="email" class="form-control border-gray rounded-0" id="email" required placeholder="E-Mail" name="email" v-model="form.email">
                                </div>
                                <div class="input-group my-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text border-gray rounded-0"> <i style="color: #aaa;" class="fa fa-key"></i> </span>
                                    </div>
                                    <input autocomplete="new-password" style="height: 45px;" :type="type" class="form-control border-gray rounded-0" id="password" required placeholder="Password" name="password" v-model="form.password">
                                </div>
                                <div class="input-group mt-4">
                                    <button style="height: 45px;" :disabled="buttonProcess.toLowerCase() != 'process'" type="submit" class="text-capitalize btn btn-primary btn-sm btn-block rounded-0 outline-gray" :disabled="!control.submit" :readonly="!control.submit"> 
                                        <h5 class="font-normal" v-html="buttonProcess"></h5>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <span title="Text on WhatsApp!" class="float d-flex justify-content-center align-items-center cursor-pointer" @click="openWhatsApp">
            <svg class="svg-inline--fa fa-whatsapp fa-w-14" aria-hidden="true" focusable="false" data-prefix="fab" data-icon="whatsapp" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"></path></svg>
            <i class="fab fa-whatsapp my-float"></i> Font Awesome fontawesome.com
        </span> -->
    </div>
<?= $this->endSection(); ?>


<?= $this->section('scripts') ?>
    <script>
        new Vue({
            el: '#registration',
            data:() => ({
                alert: '<?= session()->getFlashdata('message') ?>',
                form: {
                    username: '',
                    password: '',
                },
                control: {
                    submit: false
                },
                icon: '&#128065;',
                type: 'password',
                buttonProcess: 'Process'
            }),
            mounted(){
                if(window.location.search){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Your session was expired, please Sign In',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false
                    })
                }
            },
            watch: {
                form: {
                    handler(f){
                        f.username && f.password ? this.control.submit = true : false
                    },
                    deep: true
                }
            },
            methods: {
                openWhatsApp(){
                    location.href = 'https://api.whatsapp.com/send?phone=6285161521615&amp;text=Hi%20cems.id%2C%0APerkenalkan%20saya%2C%0ANama%20%3A%20%0APerusahaan%20%3A%20%0AJabatan%20%3A%20%0AEmail%20%3A%20%0ASaya%20membutukan%20account%20demo%20untuk%20aplikasi%20cems%2C%20mohon%20bisa%20dibantu%20ya..%20%20%0A%F0%9F%99%82'
                },
                async Submit(){
                    this.buttonProcess = '<i class="fa fa-spinner fa-spin"></i> Waiting..'
                    
                    await fetch(this.$refs.form.action, {
                        method: 'POST',
                        body: new FormData(this.$refs.form)
                    }).then(res => {
                        if(res.ok){
                            let json = res.json();
                            return json
                        }else {
                            throw res
                        }
                    })
                    .then(json => {
                        if(json.status){
                            if(window.location.search){
                                location.href = `<?= '/' ?>${window.location.search.split('=')[1]}`
                            }else {
                                location.reload()
                            }
                        }else {
                            this.buttonProcess = 'Process'
                            Swal.fire({
                                icon: 'error',
                                title: json.message,
                                allowOutsideClick: false,
                            })
                        }
                    }).catch(er => {
                        this.buttonProcess = 'Process'
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Login',
                            allowOutsideClick: false,
                        })
                    })
                }
            }
        })
    </script>
<?= $this->endSection() ?>