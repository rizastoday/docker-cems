<?= $this->extend('layouts/layout') ?>

<?= $this->section('styles') ?>
    <style>
        table tbody tr td{
            white-space: nowrap !important;
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div id="history">
        <loading :show="control.loading"></loading>
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-3 p-3">
                    <div class="card-body d-flex flex-column table-responsive">
                        <table class="table table-sm table-bordered table-striped table-hover">
                            <thead>
                                <tr class="text-uppercase">
                                    <th>Address</th>
                                    <th>User</th>
                                    <th>Activity</th>
                                    <th>Data</th>
                                    <th>Data 2</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script>
        let v = new Vue({
            el: '#history',
            data:()=>({
                table: null
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
                            this.table = await $('.table').DataTable({
                                processing: true,
                                serverSide: true,
                                destroy: true,
                                responsive: true,
                                order: [[5, 'desc']],
                                ajax: {
                                    url: "<?= base_url() . '/history/ajax_list' ?>",
                                    type: "POST",
                                    data: {},
                                },
                                columns: [
                                    { data: "address", name: "address" },
                                    { data: "user", name: "user" },
                                    { data: "activity", name: "activity" },
                                    { data: "data", name: "data" },
                                    { data: "data2", name: "data2" },
                                    { data: "timestamp", name: "timestamp" },
                                ],
                                columnDefs: [
                                    {
                                        targets: [3],
                                        render: function(data, type, row){
                                            let dt = data ? (data.substr( 0, 75 ) + '…') : ''
                                            return type === 'display' && data && data.length > 75 ?
                                                `<span>${dt}</span>` :
                                                `<span>${data}</span>`
                                        }
                                    }
                                ]

                            });
                            resolve();
                        }catch(er){
                            console.log(er)
                            reject(er);
                        }
                    })
                }
            }
        })
    </script>
<?= $this->endSection() ?>