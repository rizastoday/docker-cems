<?= $this->extend('layouts/layout') ?>
<?= $this->section('styles') ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vue-slider-component@latest/theme/default.css">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
    <div id="schedule" v-cloak>
        <loading :show="control.loading"></loading>
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-3 p-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-primary text-capitalize my-3" data-toggle="modal" data-target="#modalParameter">
                                    <i class="fa fa-plus"></i>
                                    <span>add cems parameter</span>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>UNIT</label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <label v-for="(item, index) in master.cems" :key="index" :class="model.cemsId == item.cemsId ? 'btn-primary' : ''" class="btn btn-outline-primary">
                                        <input  @change="setModelParameter" v-model="model.cemsId" :value="item.cemsId" :id="item.cemsId" type="radio" :label="item.name" autocomplete="off" :checked="model.cemsId == item.cemsId"> {{ item.name }} ({{ item.status }})
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <!-- <div class="form-group">
                                    <label>From - To</label>
                                    <div class="form-control timepicker border-primary">
                                        <i class="fa fa-calendar text-primary mr-3"></i>
                                        <span></span>
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <v-date-picker :popover="{ visibility: 'click' }" @input="setModelParameter"  mode="dateTime" :attributes="attributes" is-range is24hr :input-debounce="500" :update-on-input="false" :masks="{ inputDateTime24hr: 'YYYY-MM-DD HH:mm' }" v-model="model.times">
                                        <template v-slot="{ inputValue, inputEvents }">
                                            <label>From - To</label>
                                            <input :value="`${inputValue.start} ~ ${inputValue.end}`" v-on="inputEvents.start" class="form-control bg-white border-primary" readonly>
                                        </template>
                                    </v-date-picker>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="btn-group d-inline" role="group" aria-label="Parameter">
                                    <button @click="setParameter(item)" v-for="(item, index) in master.masterParameter.filter(x => x.cemsId == model.cemsId)" :key="index" :class="model.parameter.map(x => x.codeVal).includes(item.codeVal) ? 'btn-primary' : ''" type="button" class="btn btn-outline-primary" style="min-width: 20%;">
                                        {{ item.parameterName }}
                                    </button>
                                </div>
                            </div> -->
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped table-hover" style="width: 100%;">
                                <thead>
                                    <tr class="text-uppercase"> 
                                        <th>timestamp</th>
                                        <th>unit</th>
                                        <th>parameter</th>
                                        <th>date</th>
                                        <th>times executed</th>
                                        <th>maintenance end</th>
                                        <th>create by</th>
                                        <th>option</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="modal fade" id="modalParameter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Schedule Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click.prevent="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form ref="form">
                            <?= csrf_field() ?>
                            <input type="hidden" v-model="form.scheduleId" name="scheduleId">
                            <div class="form-group">
                                <label>UNIT</label>
                                <select class="form-control" v-model="form.cemsId" name="cemsId">
                                    <option v-for="item in _.keys(_.groupBy(master.cems, 'cemsId'))" :value="item">
                                        UNIT {{ item }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group" v-if="!_.keys(_.groupBy(master.parameter.filter(x => x.cemsId == form.cemsId), 'source')).includes('null') && form.cemsId">
                                <label>Source Analyzer</label>
                                <select class="form-control" v-model="form.source" name="source">
                                    <option v-for="item in _.keys(_.groupBy(master.parameter.filter(x => x.cemsId == form.cemsId), 'source'))" :value="item">
                                        {{ item }}
                                    </option>
                                </select>
                            </div>
                            <!-- <div class="form-group">
                                <label>Parameter Name</label>
                                <input type="hidden" v-model="form.scheduleId" name="scheduleId">
                                <select class="form-control" v-model="form.cemsParameterId" name="cemsParameterId">
                                    <option v-for="item in master.parameter" :value="item.cemsParameterId">
                                        {{ item.parameterName }} - UNIT {{ item.cemsId }}
                                    </option>
                                </select>
                            </div> -->
                            <div class="form-group" v-if="parameterGroup.length  && form.cemsId">
                                <label v-if="parameterGroup.length">Parameter</label>
                                <div class="d-flex flex-column">
                                    <label class="container-checkbox" v-for="(item, index) in parameterGroup" >
                                        {{ item.parameterName }} - Unit {{ item.cemsId }}
                                        <input v-model="form.cemsParameterId" type="checkbox" name="cemsParameterId" :id="item.cemsParameterId" :value="item.cemsParameterId">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <v-date-picker :popover="{ visibility: 'click' }" :masks="{ inputDateTime24hr: 'YYYY-MM-DD HH:mm A' }" v-model="form.hour" mode="dateTime" is24hr :min-date="new Date()" :input-debounce="500" :update-on-input="false"> 
                                    <template v-slot="{ inputValue, inputEvents }">
                                        <input class="form-control bg-white" :value="inputValue" v-on="inputEvents" name="hour" :value="form.hour" readonly/>
                                    </template>
                                </v-date-picker>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click.prevent="Submit">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script>
        $(()=>{
            $('.timepicker').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                startDate: moment().add('-60', 'minutes'),
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });
            $('.timepicker span').html(`${moment().add('-60', 'minutes').format('YYYY-MM-DD HH:mm')} ~ ${moment().format('YYYY-MM-DD HH:mm')}`)
            $('.timepicker').on('apply.daterangepicker', function(ev, picker) {
                $('.timepicker span').html(`${moment(picker.startDate).format('YYYY-MM-DD HH:mm')} ~ ${moment(picker.endDate).format('YYYY-MM-DD HH:mm')}`)
                v.model.timeFrom = moment(picker.startDate).format('YYYY-MM-DD HH:mm')
                v.model.timeTo = moment(picker.endDate).format('YYYY-MM-DD HH:mm')
                v.GetData()
                .then(()=>v.control.loading = false)
                .catch((er) => {
                    swal.fire({
                        icon: 'error',
                        title: 'Failed getting data',
                        allowOutsideClick: false,
                    })
                })
            });
        })
        let v = new Vue({
            el: '#schedule',
            data:()=>({
                table: null,
                modal: null,
                form:{
                    cemsParameterId: [],
                    scheduleId: null,
                    hour: null,
                    source: null,
                    cemsId: null
                },
                master: {
                    parameter: JSON.parse('<?= json_encode($parameter) ?>'),
                    cems: _.uniq(JSON.parse('<?= json_encode($cems) ?>')),
                    masterParameter: _.uniq(JSON.parse('<?= json_encode($cemsParameter) ?>')),
                },
                control: {
                    max: 24,
                    dot: 16,
                    label: [{
                        value: 0,
                        label: '00:00'
                    },{
                        value: 4,
                        label: '04:00'
                    },{
                        value: 8,
                        label: '08:00'
                    },{
                        value: 12,
                        label: '12:00'
                    },{
                        value: 16,
                        label: '16:00'
                    },{
                        value: 20,
                        label: '20:00'
                    },{
                        value: 24,
                        label: '24:00'
                    }]
                }
            }),
            mounted(){
                this.model.cemsId = _.first(this.master.cems).cemsId
                this.setModelParameter()
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
            computed: {
                parameterGroup(){
                    return this.form.source ? 
                        this.master.parameter.filter(x => x.source == this.form.source) : 
                        this.master.parameter.filter(x => x.cemsId == this.form.cemsId)
                },
                attributes(){
                    let data = JSON.parse('<?= json_encode($allSchedule) ?>')
                    // console.log(data)
                    return data.map(res => {
                        return {
                            dates: new Date(res.dates),
                            dot: {
                                style: {
                                    backgroundColor: parseInt(res.executed) ? 'green' : 'red',
                                },
                            },
                            popover: {
                                label: parseInt(res.executed) == 1 ? (res.passed == true ? 'Executed' : 'Upcoming') : (res.passed == true ? 'Passed Not Executed': 'Upcoming')
                            }
                        }
                    })
                }
            },
            methods:{
                setModelParameter(){
                    this.model.parameter = _.take(this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).map(x => x), 5)
                    
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
                setParameter(str){
                    if(this.model.parameter.indexOf(str) != -1){
                        if(this.model.parameter.length >= 2){
                            this.model.parameter.splice(this.model.parameter.indexOf(str), 1)
                            if ($.fn.DataTable.isDataTable('.table')) {
                                let t = $('.table').DataTable();
                                t.destroy()
                            }
                            this.GetData()
                            .then(()=>this.control.loading = false)
                            .catch((er) => {
                                swal.fire({
                                    icon: 'error',
                                    title: 'Failed getting data',
                                    allowOutsideClick: false,
                                })
                            })
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
                        if ($.fn.DataTable.isDataTable('.table')) {
                            let t = $('.table').DataTable();
                            t.destroy()
                        }
                        this.GetData()
                        .then(()=>this.control.loading = false)
                        .catch((er) => {
                            swal.fire({
                                icon: 'error',
                                title: 'Failed getting data',
                                allowOutsideClick: false,
                            })
                        })
                    }
                },
                GetData(){
                    return new Promise(async (resolve, reject) => {
                        try{
                            this.table = await $('.table').DataTable({
                                processing: true,
                                serverSide: true,
                                destroy: true,
                                responsive: true,
                                order: [[0, 'desc']],
                                columns: [{
                                    data: 'created_at'
                                },{
                                    data: 'cemsId'
                                },{
                                    data: 'parameterName'
                                },{
                                    data: 'hour'
                                },{
                                    data: 'timestamp_executed'
                                },{
                                    data: 'timestamp_end'
                                },{
                                    data: 'created_by'
                                },{
                                    data: 'scheduleId'
                                }],
                                columnDefs: [
                                    {
                                        targets: [-1],
                                        render: function(data, type, row){
                                            return row.timestamp_executed ? '' : `
                                                <div class="d-flex flex-row justify-content-around">
                                                    <button class="btn btn-danger btn-sm" onclick="v.Remove(${data})">
                                                        <i class="fa fa-times"></i>
                                                        <span>Cancel</span>
                                                    </button>
                                                    <button class="btn btn-success btn-sm" onclick="v.Done(${data})">
                                                        <i class="fa fa-check"></i>
                                                        <span>Done</span>
                                                    </button>
                                                </div>
                                            `
                                        }
                                    },
                                    {
                                        targets: [-2],
                                        render: function(data){
                                            return data ??  '-'
                                        }
                                    },
                                    {
                                        targets: [0, 4, 5],
                                        render: function(data){
                                            return moment(data).isValid() ? data : '-'
                                        }
                                    }
                                ],
                                ajax: {
                                    url: "<?= base_url() . '/setting/ajax_list_schedule' ?>",
                                    type: "POST",
                                    data: {
                                        cemsId: this.model.cemsId,
                                        cemsParameter: this.model.parameter.map(x => x.cemsParameterId).join(','),
                                        from: moment(this.model.times.start).format('YYYY-MM-DD HH:mm:00'),
                                        to: moment(this.model.times.end).format('YYYY-MM-DD HH:mm:00')
                                    },
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
                    let { cemsParameterId, hour } = this.form
                    if(_.map({ cemsParameterId, hour }, (x,y) => x).every(z => z != null && z != '')){
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

                                let formData = new FormData();
                                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                                if(this.form.scheduleId){
                                    formData.append('scheduleId', this.form.scheduleId)
                                }
                                formData.append('cemsParameterId', this.form.cemsParameterId.join(','))
                                formData.append('hour', moment(this.form.hour).format('Y-MM-DD H:mm:00'))
                                await fetch(`<?= 'setting/saveSchedule' ?>`, {
                                    method: 'POST',
                                    // body: new FormData(this.$refs.form)
                                    body: formData
                                }).then((res) => res.json()).then(res => {
                                    if(res.status){
                                        fetch('<?= env('NODE_URL') ?>/triggerschedule',{
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
                            title: 'Field all form',
                            allowOutsideClick: false
                        })
                    }
                },
                Close(){
                    this.form = {
                        cemsParameterId: null,
                        scheduleId: null,
                        hour: 0
                    }
                },
                Remove(id){
                    swal.fire({
                        icon: 'question',
                        title: 'Remove schedule ?',
                        showCancelButton: true,
                        allowOutsideClick: false,
                        confirmButtonColor: '#d33'
                    }).then(res => {
                        if(res.value){
                            swal.fire({
                                icon: 'info',
                                title: 'Waiting',
                                showConfirmButton: false,
                                allowOutsideClick: false
                            })

                            let form = new FormData();
                            form.append('id', id);
                            form.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                            fetch('<?= 'setting/removeSchedule' ?>', {
                                method: 'POST',
                                body: form
                            }).then((res) => res.json()).then(res => {
                                if(res.status){
                                    fetch('<?= env('NODE_URL') ?>/triggerschedule',{
                                        method: 'POST'
                                    })
                                    swal.fire({
                                        icon: 'success',
                                        title: 'Success Remove Data',
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
                                    title: 'Error Remove Data',
                                    allowOutsideClick: false
                                })
                            })
                        }
                    })
                },
                Done(id){
                    swal.fire({
                        icon: 'question',
                        title: 'Close schedule ?',
                        text: 'Related Parameter will set to running',
                        showCancelButton: true,
                        allowOutsideClick: false
                    }).then(res => {
                        if(res.value){
                            swal.fire({
                                icon: 'info',
                                title: 'Waiting',
                                showConfirmButton: false,
                                allowOutsideClick: false
                            })

                            let form = new FormData();
                            form.append('id', id);
                            form.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                            fetch('<?= 'setting/doneSchedule' ?>', {
                                method: 'POST',
                                body: form
                            }).then((res) => res.json()).then(res => {
                                if(res.status){
                                    fetch('<?= env('NODE_URL') ?>/triggerschedule',{
                                        method: 'POST'
                                    })
                                    swal.fire({
                                        icon: 'success',
                                        title: 'Success Close Schedule',
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
                                    title: 'Error Remove Data',
                                    allowOutsideClick: false
                                })
                            })
                        }
                    })
                },
            }
        })
    </script>
<?= $this->endSection() ?>