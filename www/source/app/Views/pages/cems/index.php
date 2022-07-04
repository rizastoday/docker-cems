<?= $this->extend('layouts/layout') ?>
<?= $this->section('content') ?>
    <div id="cems">
        <loading :show="control.loading"></loading>
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-3 p-3">
                    <div class="card-body d-flex flex-column">
                        <?php if( in_array('Superuser', session()->get('group')) ): ?>
                            <div class="row p-3">
                                <button class="btn btn-sm btn-primary" @click="OpenModal">New CEMS</button>
                            </div>
                        <?php endif ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped table-hover" id="table-cems">
                                <thead>
                                    <tr class="text-uppercase">
                                        <th>name</th>
                                        <th>latitude</th>
                                        <th>longitude</th>
                                        <th>dimension</th>
                                        <th>fuel</th>
                                        <th>status</th>
                                        <th>show dashboard</th>
                                        <?php if(in_array("WEB.DETAIL.CEMS", session()->get('role'))): ?>
                                            <th>option</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="modal fade" id="modalCEMS" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            New CEMS
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body modal-sync">
                        <div class="p-3">
                            <div class="card">
                                <form class="card-body" id="formCems" autocomplete="off">
                                    <div class="form-group column">
                                        <label class="col-md-12 col-form-label">CEMS Name</label>
                                        <div class="col-md-12">
                                            <input class="form-control" v-model="form.name" name="name" type="text" placeholder="Cems Name">
                                        </div>
                                    </div>
                                    <div class="form-group column">
                                        <label class="col-md-12 col-form-label">CEMS Group (optional)</label>
                                        <div class="col-md-12">
                                            <input class="form-control" v-model="form.group" name="name" type="text" placeholder="Cems Group">
                                        </div>
                                    </div>
                                    <div class="form-group column">
                                        <label class="col-md-12 col-form-label">Dimension (m)</label>
                                        <div class="col-md-12">
                                            <input class="form-control"  v-model="form.dimension" name="dimension" type="text" placeholder="Cems Dimension">
                                        </div>
                                    </div>
                                    <div class="form-group column">
                                        <label class="col-md-12 col-form-label">Fuel</label>
                                        <div class="col-md-12">
                                            <input class="form-control"  v-model="form.fuel" name="fuel" type="text" placeholder="Cems Fuel">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success float-right" @click="SaveCems">Save</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script>

        function error() {

            Swal.fire({
                title: 'Error update data! ',
                icon: 'error',
                confirmButtonText: 'oke'
            })

        }

        function success() {

            Swal.fire({
                    title: 'Succes process data!',
                    icon: 'success',
                    confirmButtonText: 'oke'
                })
                .then(function(isConfirm) {
                    if (isConfirm)
                        window.location.reload();
                })
            }

        function errorsave() {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Error update data',
                confirmButtonText: 'oke'
            })
        }
        let v = new Vue({
            el: '#cems',
            data:()=>({
                table: null,
                form: {
                    name: null,
                    group: null,
                    dimension: null,
                    fuel: null,
                },
            }),
            created(){
                this.control.loading = true
            },
            mounted(){
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
                            this.table = await $('#table-cems').DataTable({
                                processing: true,
                                serverSide: true,
                                destroy: true,
                                responsive: true,
                                order: [],
                                ajax: {
                                    url: "<?= base_url() ?>" + '/cems/ajax_list',
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
                Detail(id){
                    window.open('<?= base_url() ?>/cems/details/'+btoa(id))
                },
                OpenModal(){
                    new coreui.Modal(document.getElementById('modalCEMS')).show()
                },
                async SaveCems(){
                    swal.fire({
                        icon: 'question',
                        title: 'Save Update ?',
                        text: 'Developer tidak bertanggung jawab atas input / kesalahan ketik data dalam formulasi pelaporan . dan atas persetujuan bersama bahwa serah terima software adalah menjadi tanggung jawab pengguna . Jika terjadi pelanggaran hukum yang terjadi atas tindakan yg  disengaja ataupun tidak disengaja , sudah bukan lagi tanggung jawab developer',
                        showCancelButton: true,
                        allowOutsideClick: false
                    }).then((res) => {
                        if(res.value) {
                            swal.fire({
                                title: 'Please Waiting',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                icon: 'info'
                            })
                            let formData = new FormData();
                            if (this.form.name && this.form.dimension && this.form.fuel) {
                                formData.append("name", this.form.name ?? null)
                                formData.append("dimension", this.form.dimension ?? null)
                                formData.append("fuel", this.form.fuel ?? null)
                                formData.append("group", this.form.group ?? null)
                                formData.append("<?= csrf_token() ?>", '<?= csrf_hash() ?>')
                                
                                fetch("<?= base_url('cems/saveOnlyCems') ?>", {
                                    method: 'POST',
                                    body: formData
                                }).then(res => res.json())
                                .then(json => {
                                    if (json.status) {
                                        fetch('<?= env('NODE_URL') ?>/triggersql',{
                                            method: 'POST'
                                        })
                                        swal.close()
                                        success();
                                    } else {
                                        swal.close()
                                        errorsave(json.message);
                                    }
                                }).catch(er => {
                                    console.log(er)
                                    error();
                                })
                            }else {
                                swal.fire({
                                    title: 'Cannot Save Data',
                                    text: 'Fill all form',
                                    icon: 'error'
                                })
                            }
                        }
                    })
                }
            }
        })
    </script>
<?= $this->endSection() ?>