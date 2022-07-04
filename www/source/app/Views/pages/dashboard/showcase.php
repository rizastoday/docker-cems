<?= $this->extend('layouts/layout') ?>

<?= $this->section('styles') ?>
    <style>
        .table thead th{
            border-bottom: none !important
        }
        .simple{
            box-shadow: 0px 1rem 3rem rgb(0 0 0 / 10%) !important;
        }
        .simple:hover{
            transform: scale(1.02);
            transition: .25s ease-in-out all;
        }
    </style>
<?= $this->endsection(); ?>

<?= $this->section('content') ?>

    <div id="showcase" v-cloak>
        <loading :show="control.loading"></loading>
        <div class="card">
            <div class="card-body p-5">
                <div class="row px-4 d-flex flex-row justify-content-between">
                    <div class="col-md-6 justify-content-lg-start d-flex justify-content-center mb-3 mb-lg-0">
                        <button data-toggle="title" data-placement="top" title="Back Dashboard" class="btn rounded-md mr-1 shadow-md" onclick="window.location.href = '<?= 'dashboard' ?>'">
                            <i class="fa fa-tachometer-alt"></i>
                            Dashboard
                        </button>
                    </div>
                    <div class="col-md-6 justify-content-lg-end d-flex justify-content-center">
                        <div class="btn-group btn-group-toggle mx-3 text-uppercase" data-toggle="buttons">
                            <label v-for="(item, index) in master.view" :key="index" :class="model.view == item.value ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md">
                                <input @change="setViewPersistance" v-model="model.view" :value="item.value" :id="item.value" type="radio" :label="item.value" autocomplete="off" :checked="model.view == item.value"> 
                                <i :class="item.icon"></i>
                                {{ item.value }}
                            </label>
                        </div>
                        <div class="btn-group btn-group-toggle mx-3 text-uppercase" data-toggle="buttons">
                            <label v-for="(item, index) in master.type" :key="index" :class="model.flag == item.value ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md">
                                <input @change="setView" v-model="model.flag" :value="item.value" :id="item.label" type="radio" :label="item.label" autocomplete="off" :checked="model.flag == item.value"> {{ item.label }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row p-4" v-if="model.view == 'table'">
                    <div class="col-md-6 d-flex flex-column p-3 mx-auto fresh-table" v-if="master.data.filter(x => x[0].cemsId == cem.cemsId)[0]" v-for="cem in master.cems" :key="cem.cemsId">
                        <h3 class="text-center my-2" v-text="`${cem.name} (${ cem.status })`"></h3>
                        <hr/>
                        <table id="fresh-table" class="table table-sm table-bordered table-striped table-hover text-uppercase" v-if="master.data.length">
                            <thead>
                                <tr>
                                    <th style="width: 50% !important">Parameter</th>
                                    <th style="width: 50% !important">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in master.data.filter(x => x[0].cemsId == cem.cemsId)[0]">
                                    <td v-text="item.name"></td>
                                    <td v-text="`${item.value} ${item.uom}`"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row p-4" v-if="model.view == 'chart'">
                    <div class="col-md-6 d-flex flex-column p-3 mx-auto" v-if="master.data.filter(x => x[0].cemsId == cem.cemsId)[0]" v-for="cem in master.cems" :key="cem.cemsId">
                        <h3 class="text-center my-2" v-text="`${cem.name} (${ cem.status })`"></h3>
                        <hr/>
                        <div class="btn-group d-inline" role="group" aria-label="Parameter" v-if="model.parameter.length > 0 && master.data.length && master.data.filter(x => x[0].cemsId == cem.cemsId)[0]">
                            <button @click="setParameter(item, cem.cemsId)" v-for="(item, index) in master.masterParameter.filter(x => x.cemsId == cem.cemsId)" :key="index" :class="model.parameter.filter(m => m.name == cem.cemsId)[0].data.map(x => x.codeVal).includes(item.codeVal) ? 'btn-primary' : ''" type="button" class="btn btn-sm btn-outline-primary" style="min-width: 20%">
                                {{ item.parameterName }}
                            </button>
                        </div>
                        <div :id="`u${cem.cemsId}`" class="w-100" style="height: 50vh;" v-if="master.data.length" ></div>
                    </div>
                </div>
                <div class="row p-4" v-if="model.view == 'simple'" >
                    <div class="col-md-6 d-flex flex-column p-3 mx-auto" v-if="master.data && master.data.filter(x => x[0].cemsId == cem.cemsId)[0]" v-for="cem in master.cems" :key="cem.cemsId">
                        <h3 class="text-center my-2" v-text="`${cem.name} (${ cem.status })`"></h3>
                        <hr/>
                        <div class="row d-flex justify-content-center align-items-center" v-if="master.data.length > 0">
                            <div style="width: 23%;" class="text-uppercase mx-1 my-2" v-for="(item, index) in master.data.filter(x => x[0].cemsId == cem.cemsId)[0]">
                                <div class="card simple bg-light">
                                    <div class="card-body py-2">
                                        <div class="d-flex flex-row justify-content-center ">
                                            <h5 v-text="item.name"></h5>
                                        </div>
                                        <hr/>
                                        <div class="row d-flex flex-row justify-content-around" :class="item.value < item.high ? 'text-success' : item.value > item.high ? 'text-warning' : item.value > item.highHigh ? 'text-danger' : ''">
                                            <h6 v-text="`${item.value} ${item.uom}`"></h6>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row p-4"  v-if="control.onRequest">
                    <div class="col-md-6 d-flex flex-column p-3 mx-auto" v-for="cem in master.cems" :key="cem.cemsId">
                        <h3 class="text-center my-2"> Waiting Server </h3>
                        <div class="row d-flex flex-row justify-content-center align-items-center">
                            <div v-for="x in 5" class="skeleton my-2" style="width: 90%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        window.onresize = () => {
            v.logic.filter(x => x.chart.id).map(x => x['chart'] ? x['chart'].resize() : false)
        }
        const v = new Vue({
            el: '#showcase',
            data:()=>({
                table: null,
                master:{
                    cems: _.uniq(JSON.parse('<?= json_encode($cems) ?>')),
                    masterParameter: _.uniq(JSON.parse('<?= json_encode($parameter) ?>')),
                    data: [],
                },
                logic: [],
                control: {
                    onRequest: false
                }
            }),
            async mounted(){
                this.model.cemsId = _.first(this.master.cems).cemsId
                this.setView();
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
                setViewPersistance(){
                    localStorage.setItem('view', this.model.view)
                    this.setView();
                },
                setView(){
                    this.master.data = []
                    if(this.model.view === 'chart'){
                        this.logic = [];
                        this.model.parameter = [];
                        this.master.cems.map(x => {
                            this.logic.push({
                                name: x.cemsId,
                                series: [],
                                axis: [],
                                chart: {},
                                option:{}
                            })
                            this.model.parameter.push({
                                name: x.cemsId,
                                data: _.take(this.master.masterParameter.filter(m => m.cemsId == x.cemsId).map(y => y), 5)
                            })
                        })
                    }
                    this.getData(this.master.wsURL.URL);
                },
                setParameter(str, id){
                    if(this.model.parameter.filter(x => x.name == id)[0].data.indexOf(str) != -1){
                        if(this.model.parameter.filter(x => x.name == id)[0].data.length >= 4){
                            this.model.parameter.filter(x => x.name == id)[0].data.splice(this.model.parameter.filter(x => x.name == id)[0].data.indexOf(str), 1)
                            // this.logic.filter(x => x.name == id)[0].chart.setOption(this.logic.filter(x => x.name == id)[0].option)
                            this.getData(this.master.wsURL.URL, true)
                        } else{
                            swal.fire({
                                icon: 'warning',
                                title: 'Min. parameter selected is 3',
                                allowOutsideClick: false
                            })
                            return false
                        }
                    }else{
                        if(this.model.parameter.filter(x => x.name == id)[0].data.length <= 4){
                            this.model.parameter.filter(x => x.name == id)[0].data.push(str)
                        }else{
                            swal.fire({
                                icon: 'warning',
                                title: 'Max. parameter selected is 5',
                                allowOutsideClick: false
                            })
                            return false
                        }
                    }
                },
                async getData(url, update){
                    this.control.onRequest = true
                    this.control.loading = true
                    return await new Promise(async (resolve) => {
                        await this.connectWebSocket(url).then(() => {
                            
                            this.connection.onmessage = async (res) => {
                                this.control.error = false
                                let data = JSON.parse(res.data).filter(x => x)
                                let keys = this.master.masterParameter.filter(param => param.cemsId == this.model.cemsId).map(x => `${this.model.flag}${x.codeVal}`);
                                data = data.map(x => {
                                    return Object.keys(x).filter(k => keys.includes(k)).reduce((obj, key) => {
                                        obj[key] = x[key];
                                        obj['cemsId'] = x['cemsid'];
                                        return obj;
                                    }, {})
                                })
                                data = data.filter(x => this.master.cems.map(y => parseInt(y.cemsId)).includes(x.cemsId))

                                this.master.data = data.map(dt => {
                                    let cemsId = dt['cemsId'];
                                    let res = v.master.masterParameter.filter(param => param.cemsId == cemsId).map(p => {
                                        let val = 0;
                                        if(p.parameterName.toLowerCase() == 'mercury'){
                                            val =  _.isNaN(dt[`${v.model.flag}${p.codeVal}`]) ? parseFloat(dt[`${v.model.flag}${p.codeVal}`]).toFixed(10) : dt[`${v.model.flag}${p.codeVal}`];
                                        }else {
                                            val =  parseFloat(dt[`${v.model.flag}${p.codeVal}`]).toFixed(2);
                                        }
                                        return {
                                            cemsId: cemsId,
                                            name: p.parameterName,
                                            value: val,
                                            high: p[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`],
                                            highHigh: p[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`],
                                            status: val <= p[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`] ? 'Normal' : 
                                                    val >= p[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`] && val <= p[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`] ? 'High' : 
                                                    val >= p[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`] && val >= p[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`] ? 'High High' : '',
                                            uom: p[`uom_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`] ?? ''
                                        }
                                    })
                                    return res
                                })

                                if(this.model.view === 'chart'){
                                    this.master.data.forEach((data, index) => {
                                        if(_.size(this.logic.filter(x => x.name == data[0].cemsId)) == 1){
                                            data.filter(d => d != undefined).forEach(dt => {
                                                if(this.logic.filter(x => x.name == dt.cemsId)[0].series.filter(y => y.name == dt.name).length == 0){
                                                    this.logic.filter(x => x.name == dt.cemsId)[0].series.push({
                                                        name: dt.name,
                                                        type: 'line',
                                                        smooth: false,
                                                        showSymbol: false,
                                                        lineStyle: {
                                                            width: 3
                                                        },
                                                        boundaryGap: true,
                                                        data: [dt.value],
                                                        markLine: {
                                                            data: [
                                                                {
                                                                    xAxis: 0,
                                                                    yAxis: parseFloat(this.master.masterParameter.filter(m => m.parameterName == dt.name)[0].high),
                                                                    lineStyle:{
                                                                        color: 'rgb(249 177 21)',
                                                                    },
                                                                    label: {
                                                                        formatter: `{c} : ${dt.name} `
                                                                    }
                                                                },
                                                                {
                                                                    xAxis: 0,
                                                                    yAxis: parseFloat(this.master.masterParameter.filter(m => m.parameterName == dt.name)[0].highHigh),
                                                                    lineStyle:{
                                                                        color: 'rgb(229 83 83)',
                                                                    },
                                                                    label: {
                                                                        formatter: `{c} : ${dt.name}`
                                                                    }
                                                                }
                                                            ]
                                                        },
                                                    })
                                                }else {
                                                    this.logic.filter(x => x.name == dt.cemsId)[0].series.filter(y => y.name == dt.name)[0].data.push(dt.value)
                                                    if(this.logic.filter(x => x.name == dt.cemsId)[0].series.filter(y => y.name == dt.name)[0].data.length > 30){
                                                        this.logic.filter(x => x.name == dt.cemsId)[0].series.filter(y => y.name == dt.name)[0].data.shift()
                                                    }
                                                }
                                            })
                                            this.logic.filter(x => x.name == data[0].cemsId)[0].axis.push(moment().format('DD-MM-YYYY HH:mm:ss'))
                                            if(this.logic.filter(x => x.name == data[0].cemsId)[0].axis.length > 30){
                                                this.logic.filter(x => x.name == data[0].cemsId)[0].axis.shift();
                                            }
                                            if(_.size(this.logic.filter(x => x.name == data[0].cemsId)[0].chart) == 0){
                                                this.setChart(
                                                    data[0].cemsId,
                                                    this.logic.filter(x => x.name == data[0].cemsId)[0].series,
                                                    this.logic.filter(x => x.name == data[0].cemsId)[0].axis,
                                                    document.querySelector(`#u${data[0].cemsId}`)
                                                    )
                                            }else {
                                                if(update == true){
                                                    this.setChart(
                                                        data[0].cemsId,
                                                        this.logic.filter(x => x.name == data[0].cemsId)[0].series,
                                                        this.logic.filter(x => x.name == data[0].cemsId)[0].axis,
                                                        document.querySelector(`#u${data[0].cemsId}`)
                                                    );
                                                    update = false;    
                                                }else{
                                                    v.logic.filter(x => x.name == data[0].cemsId)[0].chart.setOption({
                                                        series: this.logic.filter(x => x.name == data[0].cemsId)[0].series.filter(s => {
                                                            return this.model.parameter.filter(param => param.name == data[0].cemsId)[0].data.map(dt => dt.parameterName).includes(s.name)
                                                        }),
                                                        xAxis: [{ data: this.logic.filter(x => x.name == data[0].cemsId)[0].axis }]
                                                    })
                                                }
                                            }
                                        }
                                    })
                                }

                                this.control.onRequest = false

                            }

                            this.connection.onclose = () => {
                                swal.fire({
                                    icon: 'warning',
                                    title: 'Connection Lost, reconnect in 3 seconds',
                                    toast: true,
                                    showConfirmButton: false,
                                    position: 'top-end',
                                })
                                setTimeout(() => {
                                    this.getData(this.master.wsURL.URL)
                                }, 3000)
                            }
                        }).catch(er => {
                            this.control.error = true
                            this.control.loading = false
                            swal.fire({
                                icon: 'error',
                                title: 'Error connecting to Server',
                                text: 'Reconnecting ?',
                                toast: true,
                                position: 'top-end',
                            }).then(() => {
                                this.getData(this.master.wsURL.bc_URL)
                                // location.reload()
                            })
                        })
                        this.control.loading = false
                        resolve()
                    })
                },
                setChart(id, series, axis, DOM){
                    if (DOM == null) { return; }
                    echarts.dispose(DOM);
                    this.logic.filter(x => x.name == id)[0].option = {
                        tooltip: {
                            trigger: 'axis',
                            axisPointer:{
                                type: 'cross',
                            },
                            formatter: (x) => {
                                let data = 'Parameter <br/>'
                                x.forEach(i => {
                                    let uom = `${this.master.masterParameter.filter(m => m.parameterName == i.seriesName)[0][`uom_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase() ?? ''}`]}`
                                    data += `${i.marker} ${i.seriesName} : ${i.value} ${uom} <br/>`
                                })
                                return data
                            }
                        },
                        toolbox: {
                            feature: {
                                dataZoom: {
                                    yAxisIndex: 'none'
                                },
                                restore: {},
                                saveAsImage: {}
                            }
                        },
                        dataZoom: [
                            {
                                type: 'slider',
                                yAxisIndex: 0,
                                left: 0,
                                width: 20
                            },
                        ],
                        legend: {
                            bottom: 0,
                            type: 'scroll'
                        },
                        xAxis: [{
                            data: axis
                        }],
                        yAxis: [{
                            type: 'value',
                        }],
                        grid: [{
                            bottom: 50,
                            top: 40
                        }],
                        series: series.filter(s => {
                            return this.model.parameter.filter(param => param.name == id)[0].data.map(dt => dt.parameterName).includes(s.name)
                        })
                    };
                    this.logic.filter(x => x.name == id)[0].chart = echarts.init(DOM)
                    this.logic.filter(x => x.name == id)[0].chart.setOption(this.logic.filter(x => x.name == id)[0].option)
                }
            }
        })

        
        new Vue({
            el: '#sidebar',
            mounted(){
                this.observer = new MutationObserver(mutation => {
                    for (const m of mutation){
                        const newValue = m.target.getAttribute(m.attributeName);
                        this.$nextTick(() => {
                            this.classStateChange(newValue, m.oldValue);
                        })
                    }
                });

                this.observer.observe(this.$refs.sidebar, {
                    attribute: true,
                    attributeOldValue: true,
                    attributeFilter: ['class']
                });
            },
            methods:{
                classStateChange(className){
                    const list = className.split(' ');
                    if(list.includes('c-sidebar-lg-show')){
                        v.logic.map(x => x.chart.resize())
                    }
                    if(list.includes('c-sidebar-show')){
                        v.logic.map(x => x.chart.resize())
                    }
                },
            }
        })
    </script>
<?= $this->endSection() ?>