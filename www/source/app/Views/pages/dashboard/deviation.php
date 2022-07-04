<?= $this->extend('layouts/layout') ?>
<?= $this->section('content') ?>
    <div id="dashboard" v-cloak>
        <loading :show="control.loading"></loading>
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-3 p-3">
                    <div class="card-body d-flex flex-column">
                        <div class="row d-flex flex-row justify-content-between my-2">
                            <div class="col-md-6 d-flex flex-column">
                                <div class="btn-group btn-group-toggle mb-2" v-if="!master.cems.every(x => x['group'])" data-toggle="buttons">
                                    <label v-for="(item, index) in master.cems" :key="index" :class="model.cemsId == item.cemsId ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md">
                                        <input @change="control.loading = true;GetData().then(() => control.loading = false)" v-model="model.cemsId" :value="item.cemsId" :id="item.cemsId" type="radio" :label="item.name" autocomplete="off" :checked="model.cemsId == item.cemsId"> {{ item.name }}
                                    </label>
                                </div>
                                <div class="d-flex flex-lg-row flex-md-column mb-2" v-if="master.cems.every(x => x['group'])">
                                    <select class="form-control rounded-md shadow-md mr-2" v-model="model.group">
                                        <option v-for="item in _.keys(_.groupBy(master.cems, 'group'))" v-text="item" :value="item"></option>
                                    </select>
                                    <select class="form-control rounded-md shadow-md" v-model="model.cemsId" @change="control.loading = true; GetData().then(() => control.loading = false)">
                                        <option v-for="item in master.cems.filter(x => x.group == model.group)" v-text="`${item.name} - (${item.status})`" :value="item.cemsId"></option>
                                    </select>
                                </div>
                                <!-- <div style="overflow-x: scroll">
                                    <div class="btn-group" role="group" aria-label="CEMS" style="display: grid; grid-template-columns: repeat(100, calc((100% / 5) - 0.25rem));">
                                        <button v-for="(item, index) in master.cems" @click="model.cemsId = item.cemsId; control.loading = true;GetData().then(() => control.loading = false)" :key="index" :class="model.cemsId == item.cemsId ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md" style="min-width: 100%;">
                                            {{ item.name }} ({{ item.status }})
                                        </button>
                                    </div>
                                </div> -->
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <?php if(in_array('WEB.FILTER.TIME', session()->get('role'))): ?>
                                    <v-date-picker :popover="{ visibility: 'click' }" :is-dark="dark" @input="GetData"  mode="datetime" is24hr is-range :input-debounce="500" :update-on-input="false" :max-date="new Date()" :masks="{ inputDateTime24hr: 'YYYY-MM-DD HH:mm:ss' }" v-model="model.times">
                                        <template v-slot="{ inputValue, inputEvents }">
                                            <input :value="`${inputValue.start} ~ ${inputValue.end}`" :class="`bg-${themes}`"  v-on="inputEvents.start" class="form-control rounded-md mr-1 shadow-md" readonly>
                                        </template>
                                    </v-date-picker>
                                <?php endif;?>
                            </div>
                            <?php if(in_array('WEB.REPORT', session()->get('role'))): ?>
                                <!-- <div class="col-md-6 d-table">
                                    <button class="btn btn-primary float-right mt-5" @click="Report">
                                        <i class="fa fa-file-excel"></i>
                                        Export
                                    </button>
                                </div> -->
                            <?php endif;?>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-alarm table-sm table-bordered table-striped table-hover" style="width: 100%;">
                                <thead>
                                    <tr class="text-uppercase">
                                        <th class="text-center">Timestamp</th>
                                        <th class="text-center">Parameter</th>
                                        <th class="text-center">Value Terukur</th>
                                        <th class="text-center">Value Terkoreksi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Status Before</th>
                                        <th class="text-center">Duration (Minutes)</th>
                                        <!-- <th class="text-center">history</th> -->
                                    </tr>
                                </thead>
                                <tbody v-if="dataTable.length > 0">
                                    <tr v-for="(item, index) in dataTable" :key="index">
                                        <td>{{ formatter(item.time) }}</td>
                                        <td>{{ item.parameterName }}</td>
                                        <td :title="`High: ${JSON.parse(item.historical)['high']}${item.uom_terkoreksi} | High-High: ${JSON.parse(item.historical)['highHigh']}${item.uom_terkoreksi}`">{{ item.value_ukur }} ({{ item.uom_terukur }})</td>
                                        <td :title="`High: ${JSON.parse(item.historical)['high']}${item.uom_terkoreksi} | High-High: ${JSON.parse(item.historical)['highHigh']}${item.uom_terkoreksi}`">{{ item.value }} ({{ item.uom_terkoreksi }}) </td>
                                        <td>{{ item.status }}</td>
                                        <td>{{ item.status_before }}</td>
                                        <td>{{ item.duration_minutes }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="7" class="text-center">No Data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script>
        let v = new Vue({
            el: '#dashboard',
            data:()=>({
                table: null,
                dataTable: [],
                master:{
                    cems: JSON.parse('<?= json_encode($cems) ?>'),
                    masterParameter: _.uniq(JSON.parse('<?= json_encode($parameter) ?>')),
                },
                model: {
                    group: 'SIG'
                }
            }),
            mounted(){
                this.model.cemsId = _.first(this.master.cems).cemsId
                this.GetData()
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
                async GetData(){
                    this.control.loading = true
                    let url = new URL(`<?= env('NODE_URL') ?>/queryalarm`)
                    url.search = new URLSearchParams({
                        fromDate: moment(this.model.times.start).format('YYYY-MM-DD'),
                        toDate: moment(this.model.times.end).format('YYYY-MM-DD'),
                        fromTime: moment(this.model.times.start).format('HH:mm'),
                        toTime: moment(this.model.times.end).format('HH:mm'),
                        cemsId: this.model.cemsId
                    }).toString()
                    await fetch(url).then(res => res.json()).then(res => {
                        if(res.length){
                            if ($.fn.DataTable.isDataTable('.table-alarm')) {
                                let t = $('.table-alarm').DataTable({
                                    dom: 'Bfrtip',
                                    buttons: [
                                        {
                                            extend: 'excel',
                                            text: 'Export to Excel'
                                        }
                                    ],
                                    destroy: true,
                                    processing: false
                                });
                                document.querySelector('.buttons-excel').classList.add('btn')
                                document.querySelector('.buttons-excel').classList.add('rounded-md')
                                t.destroy()
                                this.dataTable = []
                            }
                            let filtered = this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).map(x => parseFloat(x.cemsParameterId))
                            this.dataTable = _.filter(res, d => filtered.includes(d.cemsParameterId)).map(result => {
                                let param = this.master.masterParameter.filter(p => p.cemsParameterId == result.cemsParameterId)[0]
                                return {
                                    ...result,
                                    ...param
                                }
                            })
                        }else {
                            this.notfound()
                            if ($.fn.DataTable.isDataTable('.table-alarm')) {
                                let t = $('.table-alarm').DataTable({
                                    dom: 'Bfrtip',
                                    buttons: [
                                        {
                                            extend: 'excel',
                                            text: 'Export to Excel'
                                        }
                                    ],
                                    destroy: true,
                                    processing: false
                                });
                                document.querySelector('.buttons-excel').classList.add('btn')
                                document.querySelector('.buttons-excel').classList.add('rounded-md')
                                t.destroy()
                                this.dataTable = []
                            }
                        }
                    }).then(() => {
                        if ($.fn.DataTable.isDataTable('.table-alarm')) {
                            let t = $('.table-alarm').DataTable({
                                dom: 'Bfrtip',
                                buttons: [
                                    {
                                        extend: 'excel',
                                        text: 'Export to Excel'
                                    }
                                ],
                                destroy: true,
                                processing: false
                            });
                            document.querySelector('.buttons-excel').classList.add('btn')
                            document.querySelector('.buttons-excel').classList.add('rounded-md')
                            t.destroy()
                        }
                        this.control.loading = false
                        
                        if(this.dataTable.length){
                            this.table = $('.table-alarm').DataTable({
                                dom: 'Bfrtip',
                                buttons: [
                                    {
                                        extend: 'excel',
                                        text: 'Export to Excel'
                                    }
                                ],
                                destroy: true,
                                processing: false
                            })
                            document.querySelector('.buttons-excel').classList.add('btn')
                            document.querySelector('.buttons-excel').classList.add('rounded-md')
                        }
                    }).catch(() => {
                        this.error();
                        this.control.loading = false
                    })
                },
                async Report(){
                    this.control.loading = true
                    let form = new FormData();
                    form['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>'
                    form.append('cemsId', this.model.cemsId);
                    form.append('from', moment(this.model.times.start).format('YYYY-MM-DD HH:mm:ss'))
                    form.append('to', moment(this.model.times.end).format('YYYY-MM-DD HH:mm:ss'))
                    form.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                    await fetch('<?= 'dashboard/export_alarm' ?>', {
                        method: 'POST',
                        body: form
                    })
                    // .then((res) => res.json())
                    .then(res => res.blob())
                    .then(blob => {
                        let url = window.URL.createObjectURL(blob);
                        let from = moment(this.model.times.start).format('YYYY-MM-DD')
                        let to = moment(this.model.times.end).format('YYYY-MM-DD')
                        let a = document.createElement('a');
                        a.href = url;
                        a.download = `Report Alarm CEMS Unit ${this.model.cemsId} (${from} - ${to}) .xlsx`;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        swal.close();
                        this.control.loading = false
                    })
                },
                formatter(str){
                    return moment(str).format('YYYY-MM-DD HH:mm:ss')
                }
            }
        })
    </script>
<?= $this->endSection() ?>