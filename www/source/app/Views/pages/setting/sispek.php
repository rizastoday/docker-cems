<?= $this->extend('layouts/layout') ?>
<?= $this->section('content') ?>
    <div id="app" class="card" v-cloak>
        <div class="card-body p-5 d-flex flex-row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Base URL</label>
                    <div class="col-md-8">
                        <input class="form-control" name="baseURL" type="text" v-model="form.baseURL">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">App Id</label>
                    <div class="col-md-8">
                        <input class="form-control" name="appId" type="text" v-model="form.appId">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">App Secret</label>
                    <div class="col-md-8">
                        <input class="form-control" name="appSecret" type="text" v-model="form.appSecret">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">KLHK Address</label>
                    <div class="col-md-8">
                        <input class="form-control" type="text" v-model="form.klhkAddress">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Backend IP</label>
                    <div class="col-md-8">
                        <input class="form-control" type="text" v-model="form.backendIP">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">App IP</label>
                    <div class="col-md-8">
                        <input class="form-control" type="text" v-model="form.appIP">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <button class="btn btn-primary mt-3" @click.prevent="Save"><i class="fa fa-paper-plane"></i> Save</button>
                    </div>
                </div>
                <hr>
                <h3>Syncronize Log Testing</h3>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label">Interval (minutes)</label>
                    <div class="col-md-8">
                        <input class="form-control"  type="number" v-model="formSynclog.interval">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4">Chimney Code</label>
                    <div class="col-md-8">
                        <template v-for="(item,index) in master.cems">
                            <div class="form-group">
                                <label>{{ item.name }}</label>
                                <div class="d-flex flex-row">
                                    <input class="form-control col-md-10" disabled type="text" v-model="item.chimneyCode" :ref="`inputRefs${index}`" placeholder="Chimney Code">
                                    <button class="btn btn-primary btn-sm col-md-2 ml-1" @click.prevent="$refs[`inputRefs${index}`][0].disabled = !$refs[`inputRefs${index}`][0].disabled">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Parameter :</label>
                                <span title="Included parameters can be enable in Cems Detail Page. (send klhk status)">
                                    {{ master.cemsParameter.filter(x => x.cemsId == item.cemsId && x.send_klhk == 1).map(y => y.parameterName).join(',') }}
                                </span>
                            </div>
                            <div class="form-group d-flex flex-row">
                                <label>Auto Synclog :</label>
                                <div class="ckbx-style-13">
                                    <input type="checkbox" :id="item.name" :checked="item.syncLog == 1" v-model="item.syncLog" true-value="1" false-value="0" @change="UpdateSynlog(item.cemsId)" :ref="`cems${item.cemsId}`">
                                    <label :for="item.name"></label>
                                </div>
                            </div>
                            <hr>
                        </template>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-8 offset-4">
                        <button class="btn btn-primary" @click.prevent="Update"><i class="fa fa-paper-plane"></i> Update</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-4">Time Ranges</label>
                    <div class="col-md-8">
                        <v-date-picker :popover="{ visibility: 'click' }" :is-dark="dark"  mode="dateTime" is-range is24hr :input-debounce="500" :update-on-input="false" :masks="{ inputDateTime24hr: 'YYYY-MM-DD HH:mm:ss' }" v-model="model.times">
                            <template v-slot="{ inputValue, inputEvents }">
                                <input :value="`${inputValue.start} ~ ${inputValue.end}`" :class="`bg-${themes}`" v-on="inputEvents.start" class="form-control border-primary" readonly>
                            </template>
                        </v-date-picker>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-8 offset-4">
                        <button class="btn btn-primary" @click.prevent="Generate">Generate</button>
                    </div>
                </div>
                <div class="form-group row mt-5">
                    <div class="col-md-12" v-if="resultJSON">
                        <!-- <textarea class="form-control" rows="20" style="resize: none; user-select: none;" v-model="resultJSON"></textarea> -->
                        <template v-for="(item, index) in resultJSON">
                            <div class="d-flex flex-column">
                                <label class="mt-2 d-flex flex-row justify-content-between">
                                    <span>Cems ID : {{ item.cemsid }}</span>
                                    <span style="cursor: pointer;" title="Open" @click="openModalJSON(index)">
                                        <i class="fa fa-expand fa-2x" ></i>
                                    </span>
                                </label>
                                <textarea class="form-control" disabled readonly rows="10" style="user-select: none; resize: none;" :value="stringify(item.data)" ref="modelJSON"></textarea>
                                <button class="btn btn-primary mt-2 float-right" @click="sendModelJSON(index)">Test KLHK</button>
                                <hr/>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modalJSON" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Live Edit
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body modal-sync">
                        <div class="p-3">
                            <div class="card">
                                <div class="card-body">
                                    <textarea class="w-100" style="height: 400px; resize: none; user-select: none;" id="detailJSON"></textarea>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-success" @click="closeModalJSON" data-dismiss="modal">Update</button>
                                </div>
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
        let app = new Vue({
            el: '#app',
            data:()=>({
                form: {
                    baseURL: "<?= isset($sispek->baseURL) ? $sispek->baseURL : "" ?>",
                    appId: "<?= isset($sispek->appId) ? $sispek->appId : "" ?>",
                    appSecret: "<?= isset($sispek->appSecret) ? $sispek->appSecret : "" ?>",
                    klhkAddress: "<?= isset($sispek->klhkAddress) ? $sispek->klhkAddress : "" ?>",
                    backendIP: "<?= isset($sispek->backendIP) ? $sispek->backendIP : "" ?>",
                    appIP: "<?= isset($sispek->appIP) ? $sispek->appIP : "" ?>"

                },
                formSynclog: {
                    interval: 5
                },
                master: {
                    cems: _.uniq(JSON.parse('<?= json_encode($cems) ?>')),
                    cemsParameter: _.uniq(JSON.parse('<?= json_encode($cemsParameter) ?>')),
                },
                resultJSON: null,
                indexJSON: null
            }),
            computed:{
                formSynclogResult(){
                    let data = this.master.cems.map(cems => {
                        let parameters = this.master.cemsParameter.filter(x => x.cemsId == cems.cemsId && x.send_klhk == 1).map(res => `k${res.codeVal}`)
                        return {
                            cemsId: cems.cemsId,
                            chimneyCode: cems.chimneyCode,
                            autoSynclog: 1,
                            parameters: parameters 
                        }
                    })
                    return {
                        interval: this.formSynclog.interval,
                        timestampFrom: moment(this.model.times.start).format('YYYY-MM-DD HH:mm'),
                        timestampTo: moment(this.model.times.end).format('YYYY-MM-DD HH:mm'),
                        data: data
                    }
                }
            },
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

                            await fetch("<?= '/'; ?>setting/saveSispek/<?= isset($sispek->sispekId) ? $sispek->sispekId : "0" ?>", {
                                method: 'POST',
                                body: data
                            }).then(res => res.json()).then(res => {
                                if (res.status == 200) {
                                    fetch('<?= env('KLHK_URL') ?>/triggersql',{
                                        method: 'POST'
                                    })
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

                            
                            let trigger = {
                                data: this.master.cems.map(x => {
                                    return {
                                        chimneyCode: x.chimneyCode,
                                        cemsId: x.cemsId
                                    }
                                }),
                                appId: this.form.appId,
                                appSecret: this.form.appSecret
                            }
                            let headers = new Headers();
                            headers.append('Content-type', "application/javascript");

                            await fetch('<?= env('KLHK_URL') ?>/sispek_m', {
                                method: 'POST',
                                headers: headers,
                                body: JSON.stringify(trigger),
                                redirect: 'follow'
                            }).then(res => res.json()).then(res => {
                                fetch('<?= env('KLHK_URL') ?>/triggersql',{
                                    method: 'POST'
                                })
                                swal.close()
                            })

                            
                        }
                    })
                },
                Update(){
                    swal.fire({
                        title: 'Update the data ?',
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

                            await this.ExecuteUpdate()
                        }
                    })
                },
                UpdateSynlog(id){
                    swal.fire({
                        title: 'Update the data ?',
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

                            await this.ExecuteUpdate()
                            let data = {
                                cemsId: id,
                                data: this.master.cems.filter(x => x.cemsId == id)[0]['syncLog'],
                                appId: this.form.appId,
                                appSecret: this.form.appSecret
                            }
                            let headers = new Headers();
                            headers.append('Content-type', "application/javascript");

                            await fetch('<?= env('KLHK_URL') ?>/sispek_m', {
                                method: 'POST',
                                headers: headers,
                                body: JSON.stringify(data),
                                redirect: 'follow'
                            }).then(res => res.json()).then(res => {
                                fetch('<?= env('KLHK_URL') ?>/triggersql',{
                                    method: 'POST'
                                })
                                swal.close()
                            }).catch(() => {
                                this.error()
                            })

                        }
                    })
                },
                async ExecuteUpdate(){

                    let data = new FormData();
                    this.master.cems.map((dt, index) => {
                        data.append(`data[${index}][cemsId]`, dt.cemsId)
                        data.append(`data[${index}][chimneyCode]`, dt.chimneyCode ?? '')
                        data.append(`data[${index}][syncLog]`, dt.syncLog ?? '')
                    })
                    data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                    await fetch("<?= '/'; ?>setting/updateChimneyCode", {
                        method: 'POST',
                        body: data
                    }).then(res => res.json()).then(res => {
                        if (res.status == 200) {
                            fetch('<?= env('KLHK_URL') ?>/triggersql',{
                                method: 'POST'
                            })
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
                },
                async Generate(){
                    swal.fire({
                        title: 'Please Wait',
                        text: 'Data is processing',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    })

                    let headers = new Headers();
                    headers.append('Content-type', "application/javascript");

                    await fetch('<?= env('KLHK_URL') ?>/sispek', {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify(this.formSynclogResult),
                        redirect: 'follow'
                    }).then(res => res.json()).then(res => {
                        this.resultJSON = res
                        fetch('<?= env('KLHK_URL') ?>/triggersql',{
                            method: 'POST'
                        })
                        // document.querySelector('#resultJSON').value = JSON.stringify(res.map(x => x.data), null, 7)
                        swal.close()
                    }).catch(() => {
                        this.error()
                    })
                },
                async sendModelJSON(index){
                    swal.fire({
                        title: 'Please Wait',
                        text: 'Data is processing',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    })

                    let headers = new Headers();
                    headers.append('Content-type', "application/javascript");

                    await fetch('<?= env('KLHK_URL') ?>/sendklhkmanual', {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify(JSON.parse(this.$refs.modelJSON[index].value)),
                        redirect: 'follow'
                    }).then(res => res.json()).then(res => {
                        swal.fire({
                            title: 'Success',
                            icon: 'success',
                            text: 'Success Test KLHK with status: ' + res.data.status
                        })
                        fetch('<?= env('KLHK_URL') ?>/triggersql',{
                            method: 'POST'
                        })
                    }).catch(() => {
                        this.error()
                    })
                },
                stringify(data){
                    return JSON.stringify(data, null, 7)
                },
                openModalJSON(index){
                    this.indexJSON = index
                    new coreui.Modal(document.getElementById('modalJSON')).show()
                    document.querySelector('#detailJSON').value = JSON.stringify(JSON.parse(this.$refs.modelJSON[index].value), null, 7)
                },
                closeModalJSON(){
                    this.$refs.modelJSON[this.indexJSON].value = JSON.stringify(JSON.parse(document.querySelector('#detailJSON').value), null, 7)
                }
            }
        })
    </script>
<?= $this->endSection() ?>