<?= $this->extend('layouts/layout') ?>
<?= $this->section('content') ?>
    <div id="app" class="card">
        <div class="card-body p-5 d-flex flex-row">
            <div class="col-md-6" id="formCompanyProfile">
                <input type="hidden" name="companyProfileId" v-model="form.companyProfileId"/>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Name</label>
                    <div class="col-md-8">
                        <input class="form-control" name="companyName" type="text" placeholder="Enter your Company Name" v-model="form.name">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Telephone</label>
                    <div class="col-md-8">
                        <input class="form-control" name="noTelp" type="text" placeholder="(xxxx) xxxxxxxx" v-model="form.noTelp">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Address</label>
                    <div class="col-md-8">
                        <textarea class="form-control" name="address" v-model="form.address"></textarea>
                        <button type="button" class="btn btn-primary mt-3" @click.prevent="Save"><i class="fa fa-paper-plane"></i> Save</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <img class="w-100" src="<?= '/' ?>image/setting.svg" alt="Setting Icon" />
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script>
        new Vue({
            el: '#app',
            data:()=>({
                form: {
                    companyProfileId: '<?= isset($company_profile->companyProfileId) ? $company_profile->companyProfileId : "" ?>',
                    name: '<?= isset($company_profile->name) ? $company_profile->name : "" ?>',
                    noTelp: '<?= isset($company_profile->noTelp) ? $company_profile->noTelp : "" ?>',
                    address: '<?= isset($company_profile->address) ? $company_profile->address : "" ?>'
                }
            }),
            methods: {
                Save() {
                    swal.fire({
                        title: 'Save the data ?',
                        text: 'Make sure to check the data before processing',
                        icon: 'question',
                        showCancelButton: true,
                        allowOutsideClick: false
                    }).then(async (res) => {
                        if(res.value){
                            swal.fire({
                                title: 'Please Wait',
                                text: 'Data is processing',
                                icon: 'info',
                                showConfirmButton: false,
                                allowOutsideClick: false
                            })
                            let data = new FormData();
                            _.map(this.form, (value, key) => {
                                data.append(key, value);
                            })
                            data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                            await fetch('<?= base_url('/'); ?>setting/saveCompanyProfile/<?= isset($company_profile->companyProfileId) ? $company_profile->companyProfileId : "0" ?>', {
                                method: 'POST',
                                body: data
                            }).then(res => res.json()).then(res => {
                                if (res.status == 200) {
                                    swal.fire({
                                        icon: 'success',
                                        title: res.Message,
                                        allowOutsideClick: false
                                    }).then(() => {
                                        window.location.reload();
                                    })
                                } else {
                                    error("Error Saved Data");
                                }
                            }).catch(() => {
                                this.error()
                            })
                        }
                    })
                }
            }
        })
    </script>
<?= $this->endSection() ?>