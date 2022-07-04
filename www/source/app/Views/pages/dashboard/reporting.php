<?= $this->extend('layouts/layout') ?>
<?= $this->section('styles') ?>
    <style>
        table tbody tr td:first-child{
            width: 5% !important;
        }
        table tbody tr td:not(:first-child){
            width:3% !important;
        }
    </style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

    <div id="dashboard" v-cloak>
        <loading :show="control.loading"></loading>
        <div class="row d-flex flex-row justify-content-between my-2">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-body d-flex flex-column">
                        <div class="row">
                            <div class="col-md-6 d-flex flex-column">
                                <div class="btn-group btn-group-toggle my-1" v-if="!master.cems.every(x => x['group'])" data-toggle="buttons">
                                    <label v-for="(item, index) in master.cems" :key="index" :class="model.cemsId == item.cemsId ? 'btn-primary' : ''" class="btn rounded-md mr-1 mb-2 shadow-md">
                                        <input  @change="setModelParameter(); Search(baseURL);" v-model="model.cemsId" :value="item.cemsId" :id="item.cemsId" type="radio" :label="item.name" autocomplete="off" :checked="model.cemsId == item.cemsId"> {{ item.name }} ({{ item.status }})
                                    </label>
                                </div>
                                <!-- <div style="overflow-x: scroll">
                                    <div class="btn-group" role="group" aria-label="CEMS" style="display: grid; grid-template-columns: repeat(100, calc((100% / 5) - 0.25rem));">
                                        <button @click="setParameter(item)" v-for="(item, index) in master.cems" @click="model.cemsId = item.cemsId; setModelParameter(); Search(baseURL);" :key="index" :class="model.cemsId == item.cemsId ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md" style="min-width: 100%;">
                                            {{ item.name }} ({{ item.status }})
                                        </button>
                                    </div>
                                </div> -->
                                <div class="d-flex flex-lg-row flex-md-column mb-2" v-if="master.cems.every(x => x['group'])">
                                    <select class="form-control rounded-md shadow-md mr-2" v-model="model.group">
                                        <option v-for="item in _.keys(_.groupBy(master.cems, 'group'))" v-text="item" :value="item"></option>
                                    </select>
                                    <select class="form-control rounded-md shadow-md" v-model="model.cemsId" @change="setParameter(model.cemsId)">
                                        <option v-for="item in master.cems.filter(x => x.group == model.group)" v-text="`${item.name} - (${item.status})`" :value="item.cemsId"></option>
                                    </select>
                                </div>
                                <div class="btn-group" role="group" aria-label="Parameter" style="display: grid; grid-template-columns: repeat(5, calc((100% / 5) - 0.25rem));">
                                    <button @click="setParameter(item)" v-for="(item, index) in master.masterParameter.filter(x => x.cemsId == model.cemsId)" :key="index" :class="model.parameter.map(x => x.codeVal).includes(item.codeVal) ? 'btn-primary' : ''" type="button" class="btn rounded-md mr-2 shadow-md" style="min-width: 100%;">
                                        {{ item.parameterName }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <?php if(in_array('WEB.FILTER.TIME', session()->get('role'))): ?>
                                    <div class="form-group">
                                        <v-date-picker :popover="{ visibility: 'click' }" :is-dark="dark" @input="Search(baseURL)"  mode="dateTime" is-range is24hr :input-debounce="500" :update-on-input="false" :masks="{ inputDateTime24hr: 'YYYY-MM-DD HH:mm' }" v-model="model.times">
                                            <template v-slot="{ inputValue, inputEvents }">
                                                <input :value="`${inputValue.start} ~ ${inputValue.end}`" :class="`bg-${themes}`" v-on="inputEvents.start" class="form-control rounded-md shadow-md" readonly>
                                            </template>
                                        </v-date-picker>
                                    </div>
                                <?php endif; ?>
                                <?php if(in_array('WEB.FILTER.INTERVAL', session()->get('role'))): ?>
                                    <div class="d-flex flex-row w-100">
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                            <label v-for="(item, index) in master.interval" :key="index" :class="model.interval == item.value ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md">
                                                <input @change="Search(baseURL)" v-model="model.interval" :id="item.value" :value="item.value" type="radio" :label="item.label" autocomplete="off" :checked="model.interval == item.value"> 
                                                <i :class="item.icon"></i>
                                                <span class="d-none d-lg-block">{{ item.label }}</span>
                                            </label>
                                        </div>
                                        <button class="btn rounded-md mr-1 shadow-md ml-1" @click.prevent="model.interval = ''; Search(baseURL);" data-toggle="tooltip" data-placement="top" title="reset interval" v-if="model.interval">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex flex-row row mt-5" v-cloak v-if="master.data.length > 0">
                            <div class="col-md-12 m-auto table-responsive">
                                <?php if(in_array('WEB.REPORT', session()->get('role'))): ?>
                                    <div class="d-flex justify-content-start my-2">
                                        <button class="btn rounded-md mr-1 shadow-md" @click="Report">
                                            <i class="fa fa-file-excel text-primary"></i>
                                            Export
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <table class="table table-sm table-bordered table-striped table-hover" id="table-reporting" style="min-width: 100%;">
                                    <thead>
                                        <tr class="text-uppercase">
                                            <th class="text-center" rowspan="2" style="min-width: 50px; white-space: nowrap;">time</th>
                                            <th class="text-center" style="min-width: 50px; white-space: nowrap;" v-for="item in _.map(_.uniq(parameter.slice(1).map(x => x.split('_')[0])))" :colspan="countValue(_.map(parameter.slice(1).map(x => x.split('_')[0])), item)">
                                                <span v-text="item"></span>
                                            </th>
                                        </tr>
                                        <tr class="text-uppercase">
                                            <th class="text-center" v-for="item in _.map(parameter.slice(1).map(x => x))" style="min-width: 50px; white-space: nowrap;">
                                                <small class="font-weight-bold" v-if="master.masterParameter.filter(x => x.cemsId == model.cemsId && x.send_klhk == 1 && x.parameterName == item.split('_')[0]).length" v-text="item.split('_')[1]"></small>
                                                <small class="font-weight-bold" v-text="master.masterParameter.filter(x => x.cemsId == model.cemsId).filter(y => y.parameterName == item.split('_')[0])[0][`uom_${item.split('_')[1].toLowerCase()}`]"></small>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="master.data.length > 0">
                                        <tr v-for="(item, index) in _.map(master.data, x => _.map(parameter, y => x[y]))" :key="index" >
                                            <td align="center" v-for="(param, paramIndex) in parameter" :key="paramIndex" style="min-width: 50px; white-space: nowrap;">
                                                <span v-text="item[paramIndex]"></span>
                                                <!-- <span v-if="item[paramIndex] != '-'" v-text="master.masterParameter.filter(x => x.cemsId == model.cemsId).filter(x => x.parameterName == param.split('_')[0]).map(x => x.uom)[0]"></span> -->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="d-flex flex-column mt-5 py-5" v-cloak v-if="master.data.length == 0 && control.loading == false">
                            <span class="text-primary display-4 text-center text-capitalize">data empty</span>
                            <img src="<?= base_url('/image/empty.svg') ?>" class="w-50 mx-auto">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        // $(()=>{
        //     $('.timepicker').on('apply.daterangepicker', function(ev, picker) {
        //         $('.timepicker span').html(`${moment(picker.startDate).format('YYYY-MM-DD HH:mm')} ~ ${moment(picker.endDate).format('YYYY-MM-DD HH:mm')}`)
        //         v.model.timeFrom = moment(picker.startDate).format('YYYY-MM-DD HH:mm')
        //         v.model.timeTo = moment(picker.endDate).format('YYYY-MM-DD HH:mm')
        //         v.Search(v.baseURL)
        //     });
        // })
        let v = new Vue({
            el: '#dashboard',
            data:()=>({
                table: null,
                master:{
                    cems: _.uniq(JSON.parse('<?= json_encode($cems) ?>')),
                    masterParameter: _.uniq(JSON.parse('<?= json_encode($parameter) ?>')),
                    // masterParameter: backup,
                    data: [],
                },
                parameter: [],
                model: {
                    group: 'SIG'
                }
            }),
            watch: {
                model: {
                    handler(v){
                        console.log(v)
                    },
                    deep: true
                }
            },
            mounted(){
                // this.model.interval = 'hourly'
                this.model.cemsId = _.first(this.master.cems).cemsId
                this.setModelParameter()
                this.Search(this.baseURL)
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
                countValue(arr, val){
                    return arr.reduce((a,v) => (v === val ? a + 1 : a), 0)
                },
                setModelParameter(){
                    this.model.parameter = _.take(this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).map(x => x), 5)
                },
                setParameter(str){
                    if(this.model.parameter.indexOf(str) != -1){
                        if(this.model.parameter.length >= 2){
                            this.model.parameter.splice(this.model.parameter.indexOf(str), 1)
                            if ($.fn.DataTable.isDataTable('#table-reporting')) {
                                let t = $('#table-reporting').DataTable();
                                t.destroy()
                            }
                            this.Search(this.baseURL);
                        }else{
                            swal.fire({
                                icon: 'warning',
                                title: 'Min. parameter selected is 1',
                                allowOutsideClick: false
                            })
                            return false
                        }
                    }else{
                        this.model.parameter.push(str)
                        if ($.fn.DataTable.isDataTable('#table-reporting')) {
                            let t = $('#table-reporting').DataTable();
                            t.destroy()
                        }
                        this.Search(this.baseURL);
                    }
                },
                async Search(URL){
                    if(this.model.cemsId != null && this.model.parameter.length > 0){
                        this.control.loading = true
                        this.waiting()
                        let cemsId = this.model.cemsId ? `cemsid = ${this.model.cemsId} AND` : ''
                        let parameter = this.model.parameter.map(x => `mean("u${x.codeVal}") AS "u${x.codeVal}",mean("k${x.codeVal}") AS "k${x.codeVal}"`).join()
                        await fetch(`${URL}/queryreport?cemsId=${cemsId}&parameter=${parameter}&fromDate=${moment(this.model.times.start).format('YYYY-MM-DD')}&fromTime=${moment(this.model.times.start).format('HH:mm')}&toDate=${moment(this.model.times.end).format('YYYY-MM-DD')}&toTime=${moment(this.model.times.end).format('HH:mm')}&interval=${this.model.interval ?? ''}`)
                        .then((res) => res.json())
                        .then((res) => {
                            if(res.length > 0){
                                if ($.fn.DataTable.isDataTable('#table-reporting')) {
                                    let t = $('#table-reporting').DataTable({
                                        order: [[ 0, "asc" ]],
                                        destroy: true
                                    });
                                    t.destroy()
                                    this.master.data = []
                                }
                                let key = res.map(r => {
                                    return Object.keys(r)
                                })[0]
                                
                                let result = res.map(x => {
                                    return Object.keys(x).filter(k => key.includes(k)).reduce((obj, k) => {
                                        let flag = k[0] == 'u' ? 'Terukur' : 'Terkoreksi';
                                        let name = this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId && x.codeVal == k.substring(1)).map(x => x.parameterName)[0];
                                        let value = name != undefined ? `${name}_${flag}` : k;
                                        let values =  x[k] ? (_.isNumber(x[k]) ? (name.toLowerCase() == 'mercury' ? parseFloat(x[k]).toFixed(10) : parseFloat(x[k]).toFixed(2)) : 
                                                            (   this.model.interval == 'hourly' ? this.$moment(x[k]).format('DD-MM-YYYY HH:mm:ss') : 
                                                                this.model.interval == 'hourly' ? this.$moment(x[k]).format('DD-MM-YYYY HH:mm:ss') : 
                                                                this.model.interval == 'hourly' ? this.$moment(x[k]).format('DD-MM-YYYY HH:mm:ss') : 
                                                                this.model.interval == 'daily' ? this.$moment(x[k]).format('DD-MMMM-YYYY') : 
                                                                this.model.interval == 'monthly' ? this.$moment(x[k]).format('MMMM-YYYY') : 
                                                                this.$moment(x[k]).format('DD-MM-YYYY HH:mm:ss')
                                                            )) : 0; 
                                        obj[value] = values;
                                        return obj;
                                    }, {})
                                });
                                this.master.data = _.orderBy(result, 'time', 'asc')
                                this.master.data = this.master.data.filter(x => {
                                    return !_.values(_.omit(x, 'time')).every(y => y == 0)
                                })
                                // this.master.data.pop()
                                
                                let data = []
                                this.model.parameter.map(x => {
                                    if(x.send_klhk == 1){
                                        data.push(`${x.parameterName}_Terukur`)
                                        data.push(`${x.parameterName}_Terkoreksi`)
                                    }else {
                                        data.push(`${x.parameterName}_Terukur`)
                                    }
                                })
                                data.unshift('time')
                                this.parameter = data

                                swal.close()
                            }else{
                                this.notfound()
                                if ($.fn.DataTable.isDataTable('#table-reporting')) {
                                    let t = $('#table-reporting').DataTable({
                                        order: [[ 0, "asc" ]],
                                        destroy: true
                                    });
                                    t.destroy()
                                    this.master.data = []
                                }
                            }

                        })
                        .then(() => {
                            if ($.fn.DataTable.isDataTable('#table-reporting')) {
                                let t = $('#table-reporting').DataTable();
                                t.destroy()
                            }
                            if(this.master.data.length > 0){
                                $('#table-reporting').DataTable({
                                    order: [[ 0, "asc" ]],
                                    destroy: true
                                })
                            }
                            
                            this.control.loading = false
                        })
                        .catch((er) => {
                            console.log(er)
                            this.error()
                            this.control.loading = false
                            // this.Search(this.bc_baseURL)
                        })
                    }else{
                        swal.fire({
                            icon: 'warning',
                            title: 'Please select Data Logger or Parameter',
                            showConfirmButton: true,
                            allowOutsideClick: false
                        })
                    }
                },
                async Report(){
                    let params = this.master.masterParameter.filter(p => p.cemsId == this.model.cemsId && p.send_klhk == 1).map(y => y.parameterName)
                    let keys = _.uniq(Object.keys(this.master.data[0]).map(obj => {
                        let result = []
                        if(obj != "time"){
                            let name = obj.split("_")[0]
                            if(params.includes(name)){
                                result.push(obj)
                            }else {
                                result.push(`${name}_Terukur`)
                            }
                        }
                        return result[0]
                    }))
                    keys.shift()
                    keys.unshift("time")
                    let data = this.master.data.map(obj => {
                        let result = {}
                        Object.keys(obj).map(k => {
                        if(keys.includes(k)){
                            result[k] = obj[k]
                        }
                        })
                        return result
                    })
                    this.control.loading = true
                    this.waiting()
                    let form = new FormData();
                    form['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>'
                    form.append('cemsId', this.model.cemsId);
                    form.append('interval', this.model.interval);
                    form.append('data', JSON.stringify(data));
                    form.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                    await fetch('<?= '/' ?>dashboard/export', {
                        method: 'POST',
                        body: form
                    })
                    // .then(res => res.json())
                    // .then(res => {
                    //     swal.close()
                    //     this.control.loading = false
                    //     console.log(res)
                    // })
                    .then(res => res.blob())
                    .then(blob => {
                        let url = window.URL.createObjectURL(blob);
                        let a = document.createElement('a');
                        a.href = url;
                        a.download = `Report CEMS ${this.model.interval} - ${this.master.cems.filter(x => x.cemsId == this.model.cemsId)[0].name} .xlsx`;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        swal.close();
                        this.control.loading = false
                    })

                }
            }
        })//.mount('#dashboard')
    </script>
<?= $this->endSection() ?>