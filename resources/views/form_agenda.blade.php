@extends('layouts.app')


@section('content')
<link href="{{asset('js/lib/main.css')}}" rel='stylesheet' />
<link href="{{ asset('css/full-calendar.css') }}" rel="stylesheet">
<style>
    /*
    O calendar da um margin top de 40px para o body
    o código a seguir serve para retirar
    */
    body{
      margin:0px;
    }
    
</style>


<script src="{{'js/lib/main.js'}}"></script>
<script src="{{'js/lib/locales-all.js'}}"></script>

<!--Para pegar os detalhes do evento, script do gabinete-old-->
<script src="./fullcalendar/moment/main.min.js"></script>
<script src="./fullcalendar/moment/moment-with-locales.min.js"></script>
<script src="./fullcalendar/moment/moment-timezone-with-data.min.js"></script>

<script type='text/javascript'>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      googleCalendarApiKey: '{{$chaveAgenda->api_key}}',
        headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
      locale:'pt-br',
      timeZone: 'America/Sao_Paulo',
      selectable: true,
      initialView: 'dayGridMonth',
      
      eventSources: [
        {
            googleCalendarId: '{{$chaveAgenda->calendar_id}}',
            className: 'gcal-event'
        }
      ],
      //function resize layout responsive 
      windowResize: function(view) {
          if ($(window).width() <= 767){
              calendar.changeView('listMonth');
              calendar.setOption('headerToolbar', {
                  left: 'prev',
                  center: 'title',
                  right: 'next'
                });
          } else {
              calendar.changeView('dayGridMonth');
              calendar.setOption('headerToolbar', { 
                  left: 'prev,today,next',
                  center: 'title', 
                  right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
              });
          }
      },

      eventClick: function (info) {
        info.jsEvent.preventDefault(); // prevent browser from visiting event's URL in the current tab
        // console.log(info.event);
        moment.locale('pt-BR');

        var fim = new moment.tz(info.event.end,"UTC");
        var ini = new moment.tz(info.event.start,"UTC");

        var duration = moment.duration(fim.diff(ini));
        var texto;
        if (ini.isValid() && !fim.isValid()) { //verificar se data inicial é válida e final não é válida  - mostrar apenas data e horario iniciais
            //OBS: Se as datas e horários inicial e final foram iguais no Google Agenda, 
            //o horário final não é considerado/exportado pelo Google Agenda e o FullCalendar não recebe uma data válida
            texto = ini.format("dddd, D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY") + ", a partir da(s) " + ini.format("HH:mm") + " h";
        } else if (moment(ini).isSame(fim, 'day')) { //verificar se data inicial e final são as mesmas sem considerar horário
            if ((ini.minutes() > 0 || ini.hours() > 0) && (fim.minutes() > 0 || fim.hours() > 0)) { //TEM HORARIO INICIAL CADASTRADO e TEM HORARIO FINAL CADASTRADO
                texto = ini.format("dddd, D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY, HH:mm") + " h - " + fim.format("HH:mm") + " h";
            } else if (ini.minutes() > 0 || ini.hours() > 0) {//TEM APENAS HORARIO INICIAL CADASTRADO
                texto = ini.format("dddd, D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY") + ", a partir da(s) " + ini.format("HH:mm") + " h";
            } else if (fim.minutes() > 0 || fim.hours() > 0) { //TEM APENAS HORARIO FINAL CADASTRADO
                texto = ini.format("dddd, D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY") + " até à(s) " + fim.format("HH:mm") + " h";
            } else {//NÃO TEM HORARIO CADASTRADO
                texto = ini.format("dddd, D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY");
            }
        } else { //dia inicial diferente do dia final
            if ((ini.minutes() == 0 && ini.hours() == 0) && (fim.minutes() == 0 && fim.hours() == 0) && duration.days() == 1) { //não tem horário definido e possui duração de 1 dia
                texto = ini.format("dddd, D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY");
            } else if ((ini.minutes() > 0 || ini.hours() > 0) && (fim.minutes() > 0 || fim.hours() > 0)) { // TEM HORARIO INICIAL CADASTRADO e TEM HORARIO FINAL CADASTRADO
                texto = ini.format("D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY, HH:mm") + " h - " + fim.format("D") + " de " + fim.format("MMMM") + " de " + fim.format("YYYY, HH:mm") + " h";
            } else if (ini.minutes() > 0 || ini.hours() > 0) {//TEM APENAS HORARIO INICIAL CADASTRADO
                texto = ini.format("D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY, HH:mm") + " h - " + fim.format("D") + " de " + fim.format("MMMM") + " de " + fim.format("YYYY");
            } else if (fim.minutes() > 0 || fim.hours() > 0) { //TEM APENAS HORARIO FINAL CADASTRADO
                texto = ini.format("D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY") + " - " + fim.format("D") + " de " + fim.format("MMMM") + " de " + fim.format("YYYY, HH:mm") + " h ";
            } else {//NÃO TEM HORARIO CADASTRADO
                fim = fim.subtract(1, 'days'); //subtrai 1 dia da data final

                if (moment(ini).isSame(fim, 'month')) { //verificar se mês e ano são os mesmos sem considerar horário
                    texto = ini.format("D") + " - " + fim.format("D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY");
                } else {
                    texto = ini.format("D") + " de " + ini.format("MMMM") + " de " + ini.format("YYYY") + " - " + fim.format("D") + " de " + fim.format("MMMM") + " de " + fim.format("YYYY");
                }
            }
        }

        //Limpar campos
        // $('#visualizar #titulo').text("");
        // $('#visualizar #duracao').text("");
        $('#visualizar #local').text("");
        $('#visualizar #descricao').text("");

        //Popular campos
        $('#visualizar #titulo').text(info.event.title);
        $('#visualizar #duracao').text(texto);

        if (!info.event.extendedProps.location) { //local vazio
            document.getElementById("titulolocal").style.display = "none";
            document.getElementById("local").style.display = "none";
        } else {//local com valor
            document.getElementById("titulolocal").style.display = "";
            document.getElementById("local").style.display = "";
            $('#visualizar #local').text(info.event.extendedProps.location);
        }

        if (!info.event.extendedProps.description) { //descrição vazia
            document.getElementById("titulodescricao").style.display = "none";
            document.getElementById("descricao").style.display = "none";
        } else {//descrição com valor
            //console.log(info.event.extendedProps.description); 
            document.getElementById("titulodescricao").style.display = "";
            document.getElementById("descricao").style.display = "";
            $('#visualizar #descricao').html(info.event.extendedProps.description);
        }

        $('#visualizar').modal('show');
        return false;
      }
  });
  calendar.render();
});
</script>

<div class="container">
  <h2 style="text-align:center;">Agenda</h2>
  <div id='calendar'></div>
  @include('Utils/modal_calendar')
</div>

@endsection