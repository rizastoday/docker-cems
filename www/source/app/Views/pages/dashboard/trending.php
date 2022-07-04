<?= $this->extend('layouts/layout') ?>

<?= $this->section('content') ?>

    <div id="trending" v-cloak>
        <loading :show="control.loading"></loading>
        <div class="row d-flex flex-row justify-content-between my-2">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-body d-flex flex-column">
                        <div class="row">
                            <div class="col-md-6 d-flex flex-column" >
                                    <div class="btn-group btn-group-toggle my-1"  v-if="!master.cems.every(x => x['group'])" data-toggle="buttons" style="overflow-x: auto">
                                        <label v-for="(item, index) in master.cems" :key="index" :class="model.cemsId == item.cemsId ? 'btn-primary' : ''" class="btn rounded-md mr-1 mb-2 shadow-md">
                                            <input @change="setModelParameter(); Search(baseURL);" v-model="model.cemsId" :value="item.cemsId" :id="item.cemsId" type="radio" :label="item.name" autocomplete="off" :checked="model.cemsId == item.cemsId"> <span style="white-space: nowrap"> {{ item.name }} <br/> ({{ item.status }}) </span>
                                        </label>
                                    </div>
                                <!-- <div style="overflow-x: scroll" v-if="!master.cems.every(x => x['group'])">
                                    <div class="btn-group" role="group" aria-label="CEMS" style="display: grid; grid-template-columns: repeat(100, calc((100% / 5) - 0.25rem));">
                                        <button v-for="(item, index) in master.cems" @click="model.cemsId = item.cemsId; setModelParameter(); Search(baseURL);" :key="index" :class="model.cemsId == item.cemsId ? 'btn-primary' : ''" class="btn rounded-md mr-1 shadow-md" style="min-width: 100%;">
                                            {{ item.name }} ({{ item.status }})
                                        </button>
                                    </div>
                                </div> -->
                                <!--  -->
                                <div class="d-flex flex-lg-row flex-md-column mb-2" v-if="master.cems.every(x => x['group'])">
                                    <select class="form-control rounded-md shadow-md mr-2" v-model="model.group">
                                        <option v-for="item in _.keys(_.groupBy(master.cems, 'group'))" v-text="item" :value="item"></option>
                                    </select>
                                    <select class="form-control rounded-md shadow-md" v-model="model.cemsId" @change="setModelParameter(); Search(baseURL);">
                                        <option v-for="item in master.cems.filter(x => x.group == model.group)" v-text="`${item.name} - (${item.status})`" :value="item.cemsId"></option>
                                    </select>
                                </div>
                                <div class="btn-group" role="group" aria-label="Parameter" style="display: grid; grid-template-columns: repeat(5, calc((100% / 5) - 0.25rem));">
                                    <button @click="setParameter(item)" v-for="(item, index) in master.masterParameter.filter(x => x.cemsId == model.cemsId)" :key="index" :class="model.parameter.map(x => x.codeVal).includes(item.codeVal) ? 'btn-primary' : ''" type="button" class="btn rounded-md mr-1 shadow-md" style="min-width: 100%;">
                                        {{ item.parameterName }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <?php if(in_array('WEB.FILTER.TIME', session()->get('role'))): ?>
                                    <div class="form-group">
                                        <v-date-picker :popover="{ visibility: 'click' }" :is-dark="dark" @input="Search(baseURL)"  mode="dateTime" is-range is24hr :input-debounce="500" :update-on-input="false" :masks="{ inputDateTime24hr: 'YYYY-MM-DD HH:mm' }" v-model="model.times">
                                            <template v-slot="{ inputValue, inputEvents }">
                                                <input :value="`${inputValue.start} ~ ${inputValue.end}`" :class="`bg-${themes}`" v-on="inputEvents.start" class="form-control rounded-md mr-1 shadow-md" readonly>
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
                        <div class="d-flex flex-row row mt-5" v-if="master.data.length > 0"  v-cloak>
                            <div class="col-md-12 m-auto">
                                <!-- <div class="my-auto">
                                    <h2>Result</h2>
                                </div> -->
                                <hr/>
                                <div style="width: 100%; height: 400px;" id="chart"></div>
                            </div>
                        </div>
                        <div class="d-flex flex-column mt-5 py-5" v-cloak v-if="master.data.length == 0 && control.loading == false">
                            <span class="text-primary display-4 text-center text-capitalize">data empty</span>
                            <img src="<?= base_url() ?>/image/empty.svg" class="w-50 mx-auto">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        var time = '<?= $time ?>'
        $(()=>{
            $('.timepicker-trending').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                startDate: moment().add('-60', 'minutes'),
                endDate: moment(),
                maxDate: moment(),
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });
            $('.timepicker-trending span').html(`${moment().add('-60', 'minutes').format('YYYY-MM-DD HH:mm')} ~ ${moment().format('YYYY-MM-DD HH:mm')}`)

            $('.timepicker-trending').on('apply.daterangepicker', function(ev, picker) {
                $('.timepicker-trending span').html(`${moment(picker.startDate).format('YYYY-MM-DD HH:mm')} ~ ${moment(picker.endDate).format('YYYY-MM-DD HH:mm')}`)
                v.model.timeFrom = moment(picker.startDate).format('YYYY-MM-DD HH:mm')
                v.model.timeTo = moment(picker.endDate).format('YYYY-MM-DD HH:mm')
                v.Search(v.baseURL)
            });
        })
        window.addEventListener('resize', function(){
            if(v.logic.chart != null && v.logic.chart != undefined){
                v.logic.chart.resize()
            }
        })
        let v = new Vue({
            el: '#trending',
            data:()=>({
                table: null,
                master:{
                    cems: _.uniq(JSON.parse('<?= json_encode($cems) ?>')),
                    masterParameter: _.uniq(JSON.parse('<?= json_encode($parameter) ?>')),
                    // masterParameter: backup,
                    data: []
                },
                logic: {
                    series:[],
                    axis: [],
                    chart: {}
                },
                model: {
                    group: 'SIG'
                }
            }),
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
            computed:{
                parameter(){
                    let data = []
                    this.model.parameter.map(x => {
                        data.push(`${x.parameterName}_Terukur`)
                        data.push(`${x.parameterName}_Terkoreksi`)
                    })
                    return data
                }
            },
            methods:{
                setModelParameter(){
                    this.model.parameter = _.take(this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).map(x => x), 2)
                },
                setParameter(str){
                    if(this.model.parameter.indexOf(str) != -1){
                        if(this.model.parameter.length >= 2){
                            this.model.parameter.splice(this.model.parameter.indexOf(str), 1)
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
                        this.Search(this.baseURL);
                    }
                },
                async Search(URL){
                    if(this.model.cemsId != null && this.model.parameter.length > 0){
                        this.control.loading = true
                        this.waiting()
                        let cemsId = this.model.cemsId ? `cemsid = ${this.model.cemsId} AND` : ''
                        let parameter = this.model.interval != null ? this.model.parameter.map(x => `mean("u${x.codeVal}") AS "u${x.codeVal}",mean("k${x.codeVal}") AS "k${x.codeVal}"`).join() : this.model.parameter.map(x => `"u${x.codeVal}",k${x.codeVal}"`).join()
                        await fetch(`${URL}/queryreport?cemsId=${cemsId}&parameter=${parameter}&fromDate=${moment(this.model.times.start).format('YYYY-MM-DD')}&fromTime=${moment(this.model.times.start).format('HH:mm')}&toDate=${moment(this.model.times.end).format('YYYY-MM-DD')}&toTime=${moment(this.model.times.end).format('HH:mm')}&interval=${this.model.interval}`)
                        .then((res) => res.json())
                        .then((res) => {
                            this.master.data = []
                            if(res.length > 0){
                                let key = res.map(r => {
                                    return Object.keys(r)
                                })[0]
                                let result = res.map(x => {
                                    return Object.keys(x).filter(k => key.includes(k)).reduce((obj, k) => {
                                        let flag = k[0] == 'u' ? 'Terukur' : 'Terkoreksi';
                                        let name = v.master.masterParameter.filter(x => x.cemsId == v.model.cemsId && x.codeVal == k.substring(1)).map(x => x.parameterName)[0];
                                        let value = name != undefined ? `${name}_${flag}` : k;
                                        let values =  x[k] ? (_.isNumber(x[k]) ? x[k] : (moment(new Date(x[k])).format())) : 0; 
                                        obj[value] = values;
                                        return obj;
                                    }, {})
                                });
                                this.master.data = result;
                                this.master.data = this.master.data.filter(x => {
                                    return !_.values(_.omit(x, 'time')).every(y => y == 0)
                                })
                                // this.master.data.pop()
                                swal.close()
                            }else{
                                this.notfound()
                            }

                        })
                        .then(() => {
                            if(this.master.data.length > 0){
                                let series = []
                                this.logic.axis = []
                                this.parameter.map(x => {
                                    this.logic.axis = this.master.data.filter(y => y[x] != null).map(y => {
                                        return {
                                            name: x,
                                            value: this.$moment(y.time).format('DD-MM-YYYY HH:mm:ss')
                                        }
                                    })
                                    let name = x.replace('_', ' ').split(' ')[0].toLowerCase()
                                    series.push({
                                        name: x.replace('_', ' '),
                                        type: 'line',
                                        smooth: false,
                                        symbol: 'none',
                                        showSymbol: false,
                                        data: this.master.data.filter(y => y[x] != null).map(y => name == 'mercury' ? parseFloat(y[x]).toFixed(10) : parseFloat(y[x]).toFixed(2)),
                                        markLine: {
                                            data: [
                                                {
                                                    xAxis: 0,
                                                    yAxis: parseFloat(this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).filter(m => m.parameterName == x.split('_')[0])[0][`high_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`]),
                                                    lineStyle:{
                                                        color: 'rgb(249 177 21)',
                                                    },
                                                    label: {
                                                        formatter: `{c} : ${x.split('_')[0]} High `
                                                    }
                                                },
                                                {
                                                    xAxis: 0,
                                                    yAxis: parseFloat(this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).filter(m => m.parameterName == x.split('_')[0])[0][`highHigh_${this.master.type.filter(f => f.value == this.model.flag)[0].label.toLowerCase()}`]),
                                                    lineStyle:{
                                                        color: 'rgb(229 83 83)',
                                                    },
                                                    label: {
                                                        formatter: `{c} : ${x.split('_')[0]} High High`
                                                    }
                                                }
                                            ]
                                        },
                                    })
                                    this.setChart([x], series, this.logic.axis, document.getElementById('chart'))
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
                setChart(obj, series, axis, DOM){
                    if (DOM == null) { return; }
                    echarts.dispose(DOM);
                    this.logic.series[obj[0]] = {
                        title: {
                            text: 'Parameter Trending',
                            left: 'center',
                            top: 0,
                        },
                        legend:{
                            show: true,
                            top: 30,
                            type: 'scroll'
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
                        tooltip: {
                            trigger: 'axis',
                            formatter: (x) => {
                                // console.log(x)
                                let data = `Parameters value at ${x[0]['axisValue']} <br/>`
                                x.forEach((i, index) => {
                                    let uom = i.seriesName.split(' ')[1].toLowerCase()
                                    let res = `${this.master.masterParameter.filter(x => x.cemsId == this.model.cemsId).filter(m => m.parameterName == i.seriesName.split(' ')[0])[0][`uom_${uom}`] ?? ''}`
                                    data += `<span class="text-sm">${i.marker} ${i.seriesName} : ${i.value} ${res} <br/></span>`
                                    // ${this.logic.axis[index].value}
                                })
                                return data
                            }
                        },
                        xAxis: [{
                            show: true,
                            data: axis
                        }],
                        yAxis: [{
                            show: true,
                        }],
                        dataZoom: [{
                            type: 'inside',
                            start: 0,
                            end: 100,
                            width: 10
                        }, {
                            start: 0,
                            end: 100
                        },
                        {
                            type: 'slider',
                            yAxisIndex: 0,
                            left: 80,
                            width: 10
                        },],
                        series: series
                    };
                    this.logic.chart = echarts.init(DOM);
                    this.logic.chart.setOption(this.logic.series[obj[0]])
                }
            }
        })//.mount('#dashboard')


        
        
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
                        v.logic.chart.resize()
                    }
                    if(list.includes('c-sidebar-show')){
                        v.logic.chart.resize()
                    }
                },
            }
        })
    </script>
<?= $this->endSection() ?>