<?= $this->extend('layouts/layout') ?>
<?= $this->section('content') ?>

    <div id="sync">
        <loading :show="control.loading"></loading>
        <div class="row d-flex flex-row justify-content-between my-2">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex flex-row row">
                            <div class="col-md-6">
                                <div class="form-group d-flex flex-column">
                                    <label>Unit</label>
                                    <div class="btn-group btn-group-toggle" v-if="!master.cems.every(x => x['group'])" data-toggle="buttons">
                                        <label v-cloak v-for="(item, index) in master.cems" :key="index" :class="model.cemsId == item.cemsId ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md">
                                            <input @change="setData" v-model="model.cemsId" :value="item.cemsId" :id="item.cemsId" type="radio" :label="item.name" autocomplete="off" :checked="model.cemsId == item.cemsId"> {{ item.name }}
                                        </label>
                                    </div>
                                    <div class="d-flex flex-lg-row flex-md-column mb-2" v-if="master.cems.every(x => x['group'])">
                                        <select class="form-control rounded-md shadow-md mr-2" v-model="model.group">
                                            <option v-for="item in _.keys(_.groupBy(master.cems, 'group'))" v-text="item" :value="item"></option>
                                        </select>
                                        <select class="form-control rounded-md shadow-md" v-model="model.cemsId" @change="setData()">
                                            <option v-for="item in master.cems.filter(x => x.group == model.group)" v-text="`${item.name} - (${item.status})`" :value="item.cemsId"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start mt-2 mb-4">
                                    <button class="btn rounded-md mr-1 shadow-md" @click="Report">
                                        <i class="fa fa-file-excel text-primary"></i>
                                        Export
                                    </button>
                                </div>
                            </div>
                            <?php if(in_array('WEB.FILTER.TIME', session()->get('role'))): ?>
                                <div class="col-md-6">
                                    <div class="form-group d-flex flex-column">
                                        <label>From - To</label>
                                        <v-date-picker :popover="{ visibility: 'click' }" @input="setData" :is-dark="dark" mode="dateTime" is24hr is-range :input-debounce="500" :update-on-input="false" :max-date="new Date()" :masks="{ inputDateTime24hr: 'YYYY-MM-DD HH:mm' }" v-model="model.times">
                                            <template v-slot="{ inputValue, inputEvents }">
                                                <input :value="`${inputValue.start} ~ ${inputValue.end}`" v-on="inputEvents.start" :class="`bg-${themes}`" class="form-control rounded-md mr-1 shadow-md" readonly>
                                            </template>
                                        </v-date-picker>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex flex-row row" >
                            <div class="col-md-12 m-auto table-responsive">
                                <table class="table table-sync table-sm table-bordered table-striped table-hover" style="min-width: 100%;">
                                    <thead>
                                        <tr class="text-uppercase">
                                            <th class="text-center">time id</th>
                                            <th class="text-center">event</th>
                                            <th class="text-center">status</th>
                                            <th class="text-center">decription</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        const v = new Vue({
            el: '#sync',
            data:()=>({
                table: null,
                master:{
                    cems: _.uniq(JSON.parse('<?= json_encode($cems) ?>')),
                    masterParameter: _.uniq(JSON.parse('<?= json_encode($parameter) ?>')),
                    // masterParameter: backup,
                    data: [],
                    detail: {},
                    status: null,
                    desc: null
                },
                model: {
                    group: 'SIG'
                }
            }),
            mounted(){
                this.model.cemsId = _.first(this.master.cems).cemsId
                this.setData()
                const cemsParamNotification = this.master.masterParameter.filter(x => x.maintenance == 1 && x.cemsId == this.model.cemsId)
                const cemsNotification = this.master.cems.filter(x => x.status != 'Running')
                if(cemsParamNotification.length || cemsNotification.length){
                    $('#list-notification').html('')
                    if(cemsNotification.length){
                        document.querySelector('.list-notification-icon').classList.add('text-danger')
                        $('#list-notification').append(`<div class="dropdown-header bg-light py-2"><strong>CEMS Alarm</strong></div>`)
                        cemsNotification.forEach(x => {
                            $('#list-notification').append(`
                                <span class="dropdown-item font-weight-bold">
                                    ${x.name} is on ${x.status}
                                </span>
                            `)
                        })
                    }
                    if(cemsParamNotification.length){
                        document.querySelector('.list-notification-icon').classList.add('text-danger')
                        $('#list-notification').append(`<div class="dropdown-header bg-light py-2"><strong>Parameter Alarm</strong></div>`)
                        cemsParamNotification.forEach(x => {
                            $('#list-notification').append(`
                                <span class="dropdown-item font-weight-bold">
                                    ${x.parameterName} is on Maintenance
                                </span>
                            `)
                        })
                    }
                }
            },
            methods:{
                setData(){
                    this.control.loading = true
                    this.getData()
                    .then(() => this.control.loading = false)
                    .catch((er) => {
                        this.error()
                    })
                },
                getData(){
                    return new Promise(async (resolve, reject) => {
                        try{
                            this.table = await $('.table-sync').DataTable({
                                processing: true,
                                serverSide: true,
                                destroy: true,
                                responsive: true,
                                // dom: 'Bfrtip',
                                // buttons: [
                                //     {
                                //         extend: 'excel',
                                //         text: 'Export to Excel',
                                //         exportOptions: {
                                //             columns: [ 0, 1, 2 ]
                                //         }
                                //     }
                                // ],
                                columns: [
                                    {data: 'timestamp_id'},
                                    {data: 'event'},
                                    {data: 'status'},
                                    {data: 'desc'}
                                ],
                                ajax: {
                                    url: "<?= base_url('dashboard/ajax_list_sync_error') ?>",
                                    type: "POST",
                                    data: {
                                        cemsId: this.model.cemsId,
                                        from: moment(this.model.times.start).valueOf(),
                                        to: moment(this.model.times.end).valueOf(),
                                    },
                                },
                                columnDefs: [
                                    {
                                        targets: [0],
                                        render: function (data, type, row){
                                            /*template*/
                                            return `
                                                ${moment(parseInt(data)).format('lll')}
                                            `
                                        }
                                    },
                                ]

                            });
                            // document.querySelector('.buttons-excel').classList.add('btn')
                            // document.querySelector('.buttons-excel').classList.add('rounded-md')
                            resolve();
                        }catch(er){
                            console.log(er)
                            reject(er);
                        }
                    })
                },
                async Report(){
                    this.control.loading = true
                    this.waiting()
                    let form = new FormData();
                    form['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>'
                    form.append('cemsId', this.model.cemsId);
                    form.append('from', moment(this.model.times.start).valueOf());
                    form.append('to', moment(this.model.times.end).valueOf());
                    form.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                    await fetch('<?= '/' ?>dashboard/export_sync_error', {
                        method: 'POST',
                        body: form
                    })
                    // .then(res => console.log(res))
                    .then(res => res.blob())
                    .then(blob => {
                        let url = window.URL.createObjectURL(blob);
                        let a = document.createElement('a');
                        a.href = url;
                        a.download = `Report Syncronize CEMS ${this.model.interval} - ${this.master.cems.filter(x => x.cemsId == this.model.cemsId)[0].name} .xlsx`;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        swal.close();
                        this.control.loading = false
                    })

                }
            }
        })
    </script>
<?= $this->endSection() ?>