<?= $this->extend('layouts/layout') ?>
<?= $this->section('content') ?>
    <div id="parameter" v-cloak>
        <loading :show="control.loading"></loading>
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-3 p-3">
                    <div class="card-body table-responsive">
                        <button class="btn btn-primary text-capitalize my-3" data-toggle="modal" data-target="#modalParameter">
                            <i class="fa fa-plus"></i>
                            <span>add parameter</span>
                        </button>
                        <table class="table table-sm table-bordered table-striped table-hover" id="table-parameter">
                            <thead>
                                <tr class="text-uppercase">
                                    <th>name</th>
                                    <th>type</th>
                                    <th>timestamp</th>
                                    <th>option</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalParameter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Parameter Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form ref="form">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label>Parameter Name</label>
                                <input type="hidden" v-model="form.parameterId" name="parameterId">
                                <input type="text" class="form-control" v-model="form.name" name="name">
                            </div>
                            <div class="form-group">
                                <label>Select Type</label>
                                <select class="form-control" v-model="form.type" name="type">
                                    <option v-for="item in master.input" :value="item">
                                        {{ item }}
                                    </option>
                                </select>
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
            el: '#parameter',
            data:()=>({
                table: null,
                modal: null,
                form: {
                    parameterId: null,
                    name: null,
                    type: null
                }
            }),
            mounted(){
                this.control.loading = true
                this.modal = new coreui.Modal(document.getElementById('modalParameter'), {
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
                            this.table = await $('#table-parameter').DataTable({
                                processing: true,
                                serverSide: true,
                                destroy: true,
                                responsive: true,
                                order: [[2, 'desc']],
                                columns: [{
                                    data: 'name'
                                },{
                                    data: 'type'
                                },{
                                    data: 'created_at'
                                },{
                                    data: 'parameterId'
                                }],
                                columnDefs: [
                                    {
                                        targets: [-1],
                                        render: function(data){
                                            return `
                                                <div class="d-flex flex-row">
                                                    <button class="btn btn-info btn-sm" onclick="v.Edit(${data})">
                                                        <i class="fa fa-edit"></i>
                                                        <span>Edit</span>
                                                    </button>
                                                </div>
                                            `
                                        }
                                    }
                                ],
                                ajax: {
                                    url: "<?= base_url() . '/setting/ajax_list' ?>",
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
                    let { name, type } = this.form
                    if(_.map({ name, type }, (x,y) => x).every(z => z != null && z != '')){
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

                                let form = new FormData();
                                this.form.parameterId ? form.append("parameterId", this.form.parameterId) : false
                                form.append("name", this.form.name)
                                form.append("type", this.form.type)

                                await fetch(`<?= base_url() . '/setting/saveParameter' ?>`, {
                                    method: 'POST',
                                    body: form
                                }).then((res) => res.json()).then(res => {
                                    if(res.status){
                                        swal.fire({
                                            icon: 'success',
                                            title: 'Success Save Data',
                                            allowOutsideClick: false
                                        }).then(res => {
                                            this.form = {
                                                name: null,
                                                type: null
                                            }
                                            location.reload()
                                            this.modal.hide()
                                            this.table.draw()
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
                            title: 'Field all form',
                            allowOutsideClick: false
                        })
                    }
                },
                Close(){
                    this.form = {
                        parameterId: null,
                        name: null,
                        type: null
                    }
                },
                Edit(id){
                    this.modal.show();
                    let { name, type, parameterId } = _.filter(this.table.data(), x => {
                        return x.parameterId == id
                    })[0]
                    let data = { name, type, parameterId }
                    Object.assign(this.form, data)
                }

            }
        })
    </script>
<?= $this->endSection() ?>