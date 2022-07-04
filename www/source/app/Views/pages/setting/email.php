<?= $this->extend('layouts/layout') ?>
<?= $this->section('content') ?>
    <div id="email" v-cloak>
        <loading :show="control.loading"></loading>
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-3 p-3">
                    <div class="card-body table-responsive">
                        <button class="btn btn-primary text-capitalize my-3" data-toggle="modal" data-target="#modalEmail">
                            <i class="fa fa-plus"></i>
                            <span>add email</span>
                        </button>
                        <table class="table table-sm table-bordered table-striped table-hover">
                            <thead>
                                <tr class="text-uppercase">
                                    <th>name</th>
                                    <th>email</th>
                                    <th>timestamp</th>
                                    <th>option</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEmail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">E-Mail Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form ref="form" autocomplete="off">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" v-model="form.name" name="name">
                            </div>
                            <div class="form-group">
                                <label>E-Mail</label>
                                <input type="hidden" v-model="form.id" name="id">
                                <input type="email" class="form-control" v-model="form.email" name="name">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click.prevent="Close">Close</button>
                        <button type="button" class="btn btn-primary" @click.prevent="Submit">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script>
        let v = new Vue({
            el: '#email',
            data:()=>({
                table: null,
                modal: null,
                master: {
                    cems: JSON.parse('<?= json_encode($cems) ?>')
                },
                form: {
                    id: null,
                    email: null,
                    name: null
                }
            }),
            mounted(){
                this.control.loading = true
                this.modal = new coreui.Modal(document.getElementById('modalEmail'), {
                    backdrop: 'static',
                    show: false
                })
                this.GetData()
                .then(()=>this.control.loading = false)
                .catch((er) => {
                    swal.fire({
                        icon: 'error',
                        title: 'Failed getting data',
                        allowOutsideClick: false,
                    })
                })
            },
            methods:{
                GetData(){
                    return new Promise(async (resolve, reject) => {
                        try{
                            this.table = await $('.table').DataTable({
                                processing: true,
                                serverSide: true,
                                destroy: true,
                                responsive: true,
                                order: [[2, 'desc']],
                                columns: [{
                                    data: 'name'
                                },{
                                    data: 'email'
                                },{
                                    data: 'created_at'
                                },{
                                    data: 'id'
                                }],
                                columnDefs: [
                                    {
                                        targets: [-1],
                                        render: function(data){
                                            return `
                                                <div class="d-flex flex-row justify-content-around">
                                                    <button class="btn btn-primary btn-sm" onclick="v.Edit(${data})">
                                                        <i class="fa fa-edit"></i>
                                                        <span>Edit</span>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" onclick="v.Remove(${data})">
                                                        <i class="fa fa-times"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </div>
                                            `
                                        }
                                    }
                                ],
                                ajax: {
                                    url: "<?= base_url() . '/setting/ajax_list_email' ?>",
                                    type: "POST",
                                    data: {},
                                }

                            });
                            resolve();
                        }catch(er){
                            console.log(er)
                            reject(er);
                        }
                    })
                },
                Submit(){
                    let { email, name } = this.form
                    if(_.map({ email, name }, (x,y) => x).every(z => z != null && z != '')){
                        if(ValidateEmail(email)){
                            swal.fire({
                                icon: 'question',
                                title: 'Save Data?',
                                allowOutsideClick: false,
                                showCancelButton: true,
                            }).then(async res => {
                                if(res.value){
                                    swal.fire({
                                        icon: 'info',
                                        title: 'Please Wait',
                                        showConfirmButton: false,
                                        allowOutsideClick: false
                                    })
                                    let f = new FormData();
                                    f.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                                    f.append('email', email)
                                    f.append('name', name)
                                    if(this.form.id){
                                        f.append('id', this.form.id);
                                    }
                                    

                                    await fetch(`<?= base_url('setting/saveEmail') ?>`, {
                                        method: 'POST',
                                        body: f
                                    }).then((res) => res.json()).then(res => {
                                        if(res.status){
                                            fetch('<?= env('NODE_URL') ?>/triggeremail',{
                                                method: 'POST'
                                            })
                                            swal.fire({
                                                icon: 'success',
                                                title: 'Success Save Data',
                                                allowOutsideClick: false
                                            }).then(res => {
                                                location.reload()
                                            })
                                        }else { 
                                            swal.fire({
                                                icon: 'error',
                                                title: res.message,
                                                allowOutsideClick: false
                                            })
                                        }
                                    }).catch(er => {
                                        console.log(er)
                                        swal.fire({
                                            icon: 'error',
                                            title: 'Error Saving Data',
                                            allowOutsideClick: false
                                        })
                                    })
                                }
                            })
                        }else {
                            swal.fire({
                                icon: 'error',
                                title: 'E-Mail not valid',
                                allowOutsideClick: false
                            })
                        }
                    }else {
                        swal.fire({
                            icon: 'error',
                            title: 'Fill all form',
                            allowOutsideClick: false
                        })
                    }
                },
                Close(){
                    this.form = {
                        id: null,
                        email: null,
                        name: null
                    }
                },
                Edit(ids){
                    this.modal.show();
                    let { email, id, name } = _.filter(this.table.data(), x => {
                        return x.id == ids
                    })[0]
                    let data = { email, id, name }
                    Object.assign(this.form, data)
                },
                Remove(id){
                    swal.fire({
                        icon: 'question',
                        title: 'Delete E-Mail ?',
                        showCancelButton: true,
                        allowOutsideClick: false
                    }).then(async res => {
                        if(res.value){
                            swal.fire({
                                icon: 'info',
                                title: 'Please Wait',
                                showConfirmButton: false,
                                allowOutsideClick: false
                            })
                            let f = new FormData();
                            f.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                            f.append('id', id);
                            await fetch(`<?= base_url('setting/deleteEmail') ?>`, {
                                method: 'POST',
                                body: f
                            }).then((res) => res.json()).then(res => {
                                if(res.status){
                                    fetch('<?= env('NODE_URL') ?>/triggeremail',{
                                        method: 'POST'
                                    })
                                    swal.fire({
                                        icon: 'success',
                                        title: 'Success Delete Data',
                                        allowOutsideClick: false
                                    }).then(res => {
                                        location.reload()
                                    })
                                }else {
                                    swal.fire({
                                        icon: 'error',
                                        title: res.message,
                                        allowOutsideClick: false
                                    })
                                }
                            }).catch(er => {
                                console.log(er)
                                swal.fire({
                                    icon: 'error',
                                    title: 'Error Delete Data',
                                    allowOutsideClick: false
                                })
                            })
                        }
                    })
                }

            }
        })
    </script>
<?= $this->endSection() ?>