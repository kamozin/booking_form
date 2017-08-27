<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <title>Форма связи</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/jquery-ui.min.css">
    <link rel="stylesheet" href="/css/swal2.min.css">
</head>
<body>

<div class="wrapper">
    <div id="app" class="form-wrap">

        <input type="text" id="datepicker" placeholder="Дата заезда" readonly name="inn" onchange="data_change(this.name, this.value)">
        <input type="text" id="datepicker1" placeholder="Дата выезда" readonly name="out" onchange="data_change(this.name, this.value)">
        <input type="text" placeholder="Имя" name="nam" onchange="data_change(this.name, this.value)">
        <input type="text" id="phone" placeholder="Номер телефона" name="phone" onchange="data_change(this.name, this.value)">
        <button  v-on:click="send_form()">Забронировать</button>
    </div>
</div>

<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/js/vue.js"></script>
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/maskedinput.min.js"></script>
<script src="/js/swal2.min.js"></script>


<script>
    $(function () {
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}
        });
    });
    var vm = new Vue({
        el:'#app',
        data:{
            inn:'',
            out: '',
            nam: '',
            phone:''
        },
        methods: {
            send_form: function(){
                let data_in=this.inn;
                let data_out=this.out;
                let name=this.nam;
                let phone=this.phone.replace(/-/g,'');
                if (data_in=='' || data_out=='' || name=='' || phone==''){
                    swal(
                            'Ошибка',
                            'Вы заполнили не все поля',
                            'error'
                    );
                }else{
                    let dat=new Date(data_in.split('.').reverse().join());
                    let dat1=new Date(data_out.split('.').reverse().join());
                    if (!this.data_check(dat,dat1)){
                        swal(
                                'Ошибка',
                                'Дата заезда не может быть ранее даты выезда',
                                'error'
                        );
                    }else{
                        var param=[];
                        var param2=[];
                        var param3=[];
                        var param4=[];
                        var datas=[];
                        param['name']='Имя';
                        param['value']=name;
                        datas[0]=param;
                        param2['name']='Дата заезда'
                        param2['value']=data_in;
                        datas[1]=param2;
                        param3['name']='Дата выезда'
                        param3['value']=data_out;
                        datas[3]=param3;
                        param4['name']='Телефон'
                        param4['value']=phone;
                        datas[4]=param4;
                        da={
                            'site_id':'100',
                            'type': 'order',
                            'data':[]
                        }
                        $.each(datas,function(key,value) {
                            if (value!=undefined){
                                da.data.push({name: value.name, value: value.value});
                            }

                        })
                        $.ajax({
                            url:'/booking',
                            type: 'POST',
                            data:da
                        }).done(function(data){

                            if(data.success==0) {
                                if (data.message == 'error:403') {
                                    swal(
                                            'Ошибка',
                                            'Неверный доступ',
                                            'error'
                                    );
                                }
                                if (data.message == 'error:data') {
                                    swal(
                                            'Ошибка',
                                            'неверно передан параметр data',
                                            'error'
                                    );
                                }
                                if (data.message == 'error:data-value') {
                                    swal(
                                            'Ошибка',
                                            'неверные данные',
                                            'error'
                                    );
                                }
                            }else if(data.success==1) {

                                    swal(
                                            'Успешно',
                                            'заявка зафиксирована,'+ data.message +'- id в системе',
                                            'success'
                                    );

                            }

                        })
                    }
                }
            },
            data_check: function(val, val1){
                if (val>val1){
                    return 0
                }else if(val<val1){
                    return 1
                }else{
                    return 0
                }
            }

        },
        watch:{
            inn: function(val){
            },
            out: function(val){
            },
            'nam': function(val) {
            },
            'phone': function(val) {
            }
        }
    });

    jQuery(function ($) {
        $.datepicker.regional['ru'] = {
            closeText: 'Закрыть',
            prevText: '&#x3c;Пред',
            nextText: 'След&#x3e;',
            currentText: 'Сегодня',
            monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
                'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
            dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
            dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            weekHeader: 'Нед',
            dateFormat: 'dd.mm.yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['ru']);
    });

    function data_change(name, value){
        if (name==='inn')
        {
            vm.inn=value;
        }
        if (name==='out'){
            vm.out=value;
        }
        if (name==='nam'){
            vm.nam=value;
        }
        if (name==='phone'){
            vm.phone=value;
        }
    }
    $(function () {
        $('#datepicker').datepicker($.extend({
                    popup: true,
                    changeYear: true,
                    changeMonth: true,
                },
                $.datepicker.regional['ru']
        ));
        $('#datepicker1').datepicker($.extend({
                    popup: true,
                    changeYear: true,
                    changeMonth: true,
                },
                $.datepicker.regional['ru']
        ));
    });

    $('#phone').mask('+7-999-999-99-99');
</script>
</body>
</html>