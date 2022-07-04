<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>
    <div id="dashboard" v-cloak>
        <loading :show="control.loading"></loading>
        <div class="d-flex flex-column flex-lg-row mb-2">
            <div class="col-md-6 d-flex flex-lg-row flex-md-column">
                <select class="form-control rounded-md shadow-md mr-2" v-if="master.cems.every(x => x['group'])" v-model="model.group">
                    <option v-for="item in _.keys(_.groupBy(master.cems, 'group'))" v-text="item" :value="item"></option>
                </select>
                <select class="form-control rounded-md shadow-md" v-if="master.cems.every(x => x['group'])" v-model="model.cemsId" @change="getData(master.wsURL.URL, true)">
                    <option v-for="item in master.cems.filter(x => x.group == model.group)" v-text="`${item.name} - (${item.status})`" :value="item.cemsId"></option>
                </select>
                <div class="d-flex justify-content-center justify-content-lg-start" v-if="!master.cems.every(x => x['group'])">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label v-for="(item, index) in master.cems" :key="index" :class="model.cemsId == item.cemsId ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md" style="white-space:nowrap">
                            <input @change="getData(master.wsURL.URL, true)" v-model="model.cemsId" :value="item.cemsId" type="radio" :label="item.name" autocomplete="off" :checked="model.cemsId == item.cemsId"> <span>{{ item.name }}</span> ({{ item.status }})
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-center justify-content-lg-end">
                <div class="d-flex flex-row">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label v-for="(item, index) in master.type" :key="index" :class="model.flag == item.value ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md" style="width: 200px;">
                            <input @change="getData(master.wsURL.URL)" v-model="model.flag" :value="item.value" :id="item.label" type="radio" :label="item.label" autocomplete="off" :checked="model.flag == item.value"> {{ item.label.toUpperCase() }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row d-flex flex-row justify-content-between">
            <div class="col-md-6 d-flex justify-content-center justify-content-lg-start">
                <div class="d-flex flex-row">
                    <div class="btn-group btn-group-toggle mx-3" data-toggle="buttons">
                        <label class="btn rounded-md mr-1 shadow-md legend d-flex ">
                            <li class="normal mr-1">Normal</li>
                        </label>
                        <label class="btn rounded-md mr-1 shadow-md legend d-flex ">
                            <li class="warning mr-1">High</li>
                        </label>
                        <label class="btn rounded-md mr-1 shadow-md legend d-flex ">
                            <li class="danger mr-1">High-high</li>
                        </label>
                    </div>
                </div>
                <!-- <div>
                    <ul class="text-uppercase legend d-flex flex-row">
                        <li class="normal">normal</li>
                        <li class="warning">high</li>
                        <li class="danger">high high</li>
                    </ul>
                </div> -->
            </div>
            <div class="col-md-6 d-flex justify-content-center justify-content-lg-end">
                <div>
                    <button data-toggle="title" data-placement="top" title="Show Showcase" class="btn rounded-md mr-1 shadow-md" onclick="window.location.href = '<?= 'dashboard/showcase' ?>'">
                        <i class="fa fa-snowflake"></i>
                        Showcase
                    </button>
                    <div class="btn-group btn-group-toggle mx-3 float-right" data-toggle="buttons">
                        <label v-for="(item, index) in master.grid" :key="index" :class="model.grid == item ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md">
                            <input @change="resize, getData(master.wsURL.URL, true)" data-toggle="tooltip" data-placement="top" :title=" item + ' Column Grid'"   v-model="model.grid" :value="item" :id="item" type="radio" :label="item" autocomplete="off" :checked="model.grid == item"> 
                            <img :src="`<?= '/' ?>image/${item}.svg`" height="15">
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="control.error == false" class="d-flex flex-row justify-content-around row mt-3" id="dashboard-content">
            <div v-for="(item, index) in master.activeParameter" :key="item.name" :class="`${model.grid == 3 ? 'col-md-4' : 'col-md-3'}`" v-cloak>
                <div class="card card-alarm" :class="item.status == 'Normal' ? 'card-normal' : item.status == 'High' ? 'card-warning' : item.status == 'High High' ? 'card-danger' : ''" style="cursor: pointer;">
                    <div class="card-body d-flex flex-column p-3" v-if="item.show" onclick="window.open('<?= '/' ?>dashboard/trending')">
                        <div class="d-flex flex-row justify-content-center align-items-center">
                            <h1 class="font-weight-light font-3xl my-auto mr-2">
                                <span v-text="item.name" class="text-uppercase font-weight-bold"></span>
                            </h1>
                            <i v-if="item.maintenance == 1" class="cil-settings fa-2x my-auto" data-toggle="tooltip" data-placement="top" :title="item.maintenance_description ?? 'On Maintenance'"></i>
                        </div>
                        <div class="row">
                            <div class="col-md-6 d-flex flex-row p-2">
                                <div :id="item.name" class="m-auto" style="width: 100%; height: 100px;"></div>
                            </div>
                            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center m-auto"  :class="item.status == 'Normal' ? 'text-success' : item.status == 'High' ? 'text-warning' : item.status == 'High High' ? 'text-danger' : ''">
                                
                                <div class="d-flex flex-row w-100 justify-content-center my-0 font-weight-bold">
                                    <span data-toggle="tooltip" data-placement="top" title="High Limit"  :class="model.grid == 3 ? 'font-2xl' : 'font-1xl'" class="text-warning mx-2" v-text="item.high"></span>
                                    <span data-toggle="tooltip" data-placement="top" title="High-High Limit"  :class="model.grid == 3 ? 'font-2xl' : 'font-1xl'" class="text-danger mx-2" v-text="item.highHigh"></span>
                                </div>
                                <h1 class="font-weight-bold my-0" :class="model.grid == 3 ? 'font-xl' : 'font-xl'" v-text="item.value"></h1>
                                <sub class="font-lg my-0" v-text="item.uom"></sub>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 d-flex flex-column p-2">
                                <div :id="`${item.name}_h`" class="m-auto" style="width: 100%; height: 75px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body d-flex p-3" v-else>
                        <div class="m-auto w-100">
                            <h1 class="font-weight-light font-3xl text-center">
                                Waiting Server
                            </h1>
                            <div v-for="x in 3" class="skeleton my-2" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="control.error == true" class="card col-md-12">
            <div class="card-body">
                <div class="d-flex flex-column mt-5 py-5">
                    <span class="text-primary display-4 text-center text-capitalize">data empty</span>
                    <img src="<?= 'image/empty.svg' ?>" class="w-50 mx-auto">
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        const v = new Vue({
            el: '#dashboard',
            data:()=>({
                logic:{
                    chart: {},
                    option: {},
                    chart_h: {},
                    series_h: {},
                    axis_h:{}
                },
                master:{
                    cems: _.uniq(JSON.parse('<?= json_encode($cems) ?>')),
                    masterParameter: _.uniq(JSON.parse('<?= json_encode($parameter) ?>')),
                    activeParameter: []
                },
                model:{
                    unit: 1,
                    parameter: [],
                    show:{},
                    group: 'SIG'
                },
                control: {
                    error: false
                }
            }),
            async mounted(){
                this.model.cemsId = _.first(this.master.cems).cemsId
                this.master.activeParameter = this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).map(param => {
                    return {
                        show: false
                    }
                })
                this.getData(this.master.wsURL.URL)
                window.onresize = () => {
                    this.resize()
                }
                let resize = false
                let ob = new MutationObserver((mutations, observer) => {
                    mutations.forEach(mutation => {
                        if(mutation.type == 'attributes' && mutation.attributeName == 'class'){
                            resize = !resize
                            if(resize){
                                this.resize()
                            }
                        }
                    })
                })
                ob.observe(document.querySelector('#sidebar'), { attributes: true })
            },
            watch:{
                'master.activeParameter': {
                    handler(el, old){
                        if(old.length > 0){
                            el.map(x => {
                                if(x.show == true){
                                    if(this.logic.series_h[`${x.name}`].series[0].data.length > 30){
                                        this.logic.series_h[`${x.name}`].series[0].data.shift()
                                        this.logic.axis_h[x.name].shift()
                                    }
                                    let color = x.status == 'Normal' ? '#2eb85c' : 
                                                x.status == 'High' ? 'rgb(249 177 21)' : 
                                                x.status == 'High High' ? 'rgb(229 83 83)' : 'white';

                                    let colorS = x.status == 'Normal' ? '#BEE3F8' : 
                                                 x.status == 'High' ? '#FEFCBF' : 
                                                 x.status == 'High High' ? '#FED7D7' : 'white';
                                    if(x.name == 'MERCURY'){
                                        this.logic.series_h[`${x.name}`].series[0].data.push(parseFloat(x.value).toFixed(10))
                                    }else {
                                        this.logic.series_h[`${x.name}`].series[0].data.push(parseFloat(x.value).toFixed(2))
                                    }
                                    this.logic.series_h[`${x.name}`].series[0].itemStyle.color = color
                                    this.logic.series_h[`${x.name}`].series[0].areaStyle.color = new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                                                                                    offset: 0.5,
                                                                                                    color:  color
                                                                                                }, {
                                                                                                    offset: 1,
                                                                                                    color:  colorS
                                                                                                }])
                                    this.logic.axis_h[x.name].push(x.timestamp)
                                    this.logic.chart_h[`${x.name}`].setOption({
                                        series: this.logic.series_h[`${x.name}`].series,
                                        xAxis: [{
                                            data: this.logic.axis_h[x.name]
                                        }],
                                    })


                                    //
                                    this.logic.option[x.name].series[0].pointer.itemStyle.color = color;
                                    this.logic.option[x.name].series[0].itemStyle.color = color;
                                    this.logic.option[x.name].series[0].axisLine.lineStyle.color = [[1, colorS]]
                                    this.logic.chart[x.name].setOption({
                                        series: this.logic.option[x.name].series
                                    })
                                }
                            })
                        }
                    },
                    deep: true
                }
            },
            methods:{
                async getData(url, updated){
                    this.master.activeParameter = this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).map(param => {
                        return {
                            show: false
                        }
                    })
                    if(this.master.activeParameter.length != 0){
                        this.logic = {
                            chart: {},
                            option: {},
                            chart_h: {},
                            series_h: {},
                            axis_h:{}
                        }
                        let isUpdated = updated
                        this.control.loading = true
                        return await new Promise(async (resolve) => {
                            await this.connectWebSocket(url).then(() => {
                                this.connection.onmessage = async (res) => {
                                    this.control.error = false
                                    let data = JSON.parse(res.data).filter(x => x && x.cemsid == this.model.cemsId)[0]
                                    let allowed = this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).map(x => `${this.model.flag}${x.codeVal}`);
                                    // console.log(allowed)
                                    data = Object.keys(data).filter(key => allowed.includes(key)).reduce((obj, key) => {
                                        obj[key] = data[key];
                                        return obj
                                    }, {})
                                    let datanow = this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).filter((lim) => {
                                        return Object.keys(data).includes(`${this.model.flag}${lim.codeVal}`)
                                    }).map((limVal) => {
                                        let val = null;
                                        if(limVal.parameterName.toLowerCase() == 'mercury'){
                                            val = parseFloat(data[`${this.model.flag}${limVal.codeVal}`]).toFixed(10)
                                        }else {
                                            val = parseFloat(data[`${this.model.flag}${limVal.codeVal}`]).toFixed(2)
                                        }
                                        let status = val <= parseFloat(limVal[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`]) ? 'Normal' 
                                                    : (val >= parseFloat(limVal[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`]) && val <= parseFloat(limVal[`highHigh_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`])) ? 'High' 
                                                    : (val >= parseFloat(limVal[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`]) && val >= parseFloat(limVal[`highHigh_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`])) ? 'High High' : ''
                                        
                                        return {
                                            name: limVal.parameterName,
                                            value: val,
                                            high: limVal[`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`],
                                            highHigh: limVal[`highHigh_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`],
                                            status: status,
                                            timestamp: moment().format('DD-MM-YYYY H:mm:s'),
                                            uom: limVal[`uom_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`] ?? '',
                                            show: true,
                                            maintenance: limVal.maintenance,
                                            maintenance_description: limVal.maintenance_description,
                                        }
                                    })
                                    // console.log(datanow)
                                    this.master.activeParameter = datanow
                                    this.master.activeParameter.forEach(x => {
                                        if(isUpdated == true){
                                            this.logic.chart[`${x.name}`] = null;
                                        }
                                        if(this.logic.chart[`${x.name}`] != null){
                                            this.logic.option[x.name].series[0].data[0].value = x.value
                                            this.logic.chart[x.name].setOption(this.logic.option[x.name], true);
                                        }else{
                                            this.setGaugeChart(x, document.getElementById(`${x.name}`))
                                        }
                                        isUpdated = false
                                    })
                                    this.master.activeParameter.forEach(x => {
                                        if(isUpdated == true){
                                            this.logic.chart_h[`${x.name}`] = null
                                        }
                                        if(this.logic.chart_h[`${x.name}`] == null){
                                            this.logic.series_h[`${x.name}`] = [{
                                                type: 'line',
                                                smooth: true,
                                                showSymbol: false,
                                                data: [x.value],
                                                itemStyle:{
                                                    color: '#2eb85c'
                                                },
                                                areaStyle: {
                                                    opacity: 0.8,
                                                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                                        offset: 0.5,
                                                        color:  x.status == 'Normal' ? '#BEE3F8' : 
                                                                x.status == 'High' ? '#FEFCBF' : 
                                                                x.status == 'High High' ? '#FED7D7' : 'white'
                                                    }, {
                                                        offset: 1,
                                                        color:  'white'
                                                    }])
                                                },
                                                markLine: {
                                                    data: [
                                                        {
                                                            xAxis: 0,
                                                            yAxis: parseFloat(x.high),
                                                            lineStyle:{
                                                                color: 'rgb(249 177 21)',
                                                            },
                                                            label: {
                                                                formatter: `{c}`
                                                            }
                                                        },
                                                        {
                                                            xAxis: 0,
                                                            yAxis: parseFloat(x.highHigh),
                                                            lineStyle:{
                                                                color: 'rgb(229 83 83)',
                                                            },
                                                            label: {
                                                                formatter: `{c}`
                                                            }
                                                        }
                                                    ]
                                                },
                                            }]
                                            this.logic.axis_h[`${x.name}`] = [x.timestamp]
                                            isUpdated = false
                                            this.setLineChart({
                                                name: x.name, 
                                                uom: x.uom, 
                                                high: x.high, 
                                                highHigh: x.highHigh}, 
                                                this.logic.series_h[`${x.name}`], this.logic.axis_h[`${x.name}`], document.getElementById(`${x.name}_h`))
                                        }
                                    })

                                    
                                    const cemsParamNotification = this.master.activeParameter.filter(x => x.maintenance == 1)
                                    const cemsNotification = this.master.cems.filter(x => x.status != 'Running')
                                    if(cemsNotification.length || cemsParamNotification.length){
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
                                                        ${x.name} is on Maintenance
                                                    </span>
                                                `)
                                            })
                                        }
                                    }
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
                                })
                            })
                            resolve()
                        })
                    }else {
                        this.control.error = true
                        this.control.loading = true
                    }

                },
                setGaugeChart(obj, DOM){
                    if (DOM == null) { return; }
                    // echarts.dispose(DOM);
                    this.logic.option[obj.name] = {
                        series: [{
                            center: ['50%', '90%'],
                            margin: 0,
                            type: 'gauge',
                            min: 0,
                            max: obj.highHigh,
                            radius: this.model.grid == 3 ? '150%' : '130%',
                            startAngle: 180,
                            endAngle: 0,
                            pointer: {
                                width: 5,
                                itemStyle: {
                                    color: obj.status == 'Normal' ? '#2eb85c' : 
                                    obj.status == 'High' ? 'rgb(249 177 21)' : 
                                    obj.status == 'High High' ? 'rgb(229 83 83)' : 'white'
                                }
                            },
                            axisLabel: {
                                show: false
                            },
                            detail: {
                                show: true
                            },
                            data: [{
                                value: obj.value
                            }],
                            splitNumber: 20,
                            itemStyle: {
                                color: obj.status == 'Normal' ? '#2eb85c' : 
                                    obj.status == 'High' ? 'rgb(249 177 21)' : 
                                    obj.status == 'High High' ? 'rgb(229 83 83)' : 'white',
                                shadowColor: 'gray',
                            },
                            progress: {
                                show: true,
                                roundCap: true,
                                width: 13
                            },
                            axisLine: {
                                roundCap: true,
                                lineStyle: {
                                    width: 13,
                                    color: [[1, '#E6EBF8']]
                                }
                            },
                            axisTick: {
                                show: false
                            },
                            axisLabel: {
                                show: false
                            },
                            title: {
                                show: false
                            },
                            detail: {
                                show: false
                            },
                        }]
                    };
                    this.logic.chart[`${obj.name}`] = echarts.init(DOM)
                    this.logic.chart[`${obj.name}`].setOption(this.logic.option[obj.name])
                },
                setLineChart(obj, series, axis, DOM){
                    if (DOM == null) { return; }
                    // echarts.dispose(DOM);
                    this.logic.series_h[obj.name] = {
                        tooltip: {
                            trigger: 'axis',
                            axisPointer:{
                                type: 'cross',
                            },
                            formatter: function(ev){
                                return `<span>${ev[0].name}</span><br/><span>${ev[0].value} ${obj.uom}</span>`
                            }
                        },
                        xAxis: [{
                            data: axis,
                            axisLabel: {
                                show: false
                            }
                        }],
                        yAxis: [{
                            type: 'value',
                            min: 0,
                            max: parseFloat(obj.highHigh)+20,
                            axisLabel: {
                                show: false
                            }
                        }],
                        grid: [{
                            bottom: 20,
                            top: 0
                        }],
                        series: series
                    };
                    this.logic.chart_h[`${obj.name}`] = echarts.init(DOM);
                    this.logic.chart_h[`${obj.name}`].setOption(this.logic.series_h[obj.name])
                },
                resize(){
                    this.logic.chart['CO'] ? this.logic.chart['CO'].resize() : false
                    this.logic.chart['CO2'] ? this.logic.chart['CO2'].resize() : false
                    this.logic.chart['FLOWRATE'] ? this.logic.chart['FLOWRATE'].resize() : false
                    this.logic.chart['NOX'] ? this.logic.chart['NOX'].resize() : false
                    this.logic.chart['O2'] ? this.logic.chart['O2'].resize() : false
                    this.logic.chart['OPACITY'] ? this.logic.chart['OPACITY'].resize() : false
                    this.logic.chart['PARTICULATE'] ? this.logic.chart['PARTICULATE'].resize() : false
                    this.logic.chart['PRESSURE'] ? this.logic.chart['PRESSURE'].resize() : false
                    this.logic.chart['SOX'] ? this.logic.chart['SOX'].resize() : false
                    this.logic.chart['TEMP'] ? this.logic.chart['TEMP'].resize() : false
                    // 
                    this.logic.chart_h['CO'] ? this.logic.chart_h['CO'].resize() : false
                    this.logic.chart_h['CO2'] ? this.logic.chart_h['CO2'].resize() : false
                    this.logic.chart_h['FLOWRATE'] ? this.logic.chart_h['FLOWRATE'].resize() : false
                    this.logic.chart_h['NOX'] ? this.logic.chart_h['NOX'].resize() : false
                    this.logic.chart_h['O2'] ? this.logic.chart_h['O2'].resize() : false
                    this.logic.chart_h['OPACITY'] ? this.logic.chart_h['OPACITY'].resize() : false
                    this.logic.chart_h['PARTICULATE'] ? this.logic.chart_h['PARTICULATE'].resize() : false
                    this.logic.chart_h['PRESSURE'] ? this.logic.chart_h['PRESSURE'].resize() : false
                    this.logic.chart_h['SOX'] ? this.logic.chart_h['SOX'].resize() : false
                    this.logic.chart_h['TEMP'] ? this.logic.chart_h['TEMP'].resize() : false
                }
            }
        })


    </script>
<?= $this->endSection() ?>