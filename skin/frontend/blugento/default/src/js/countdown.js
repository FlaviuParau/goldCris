function getTimer(dt,no) {
    var end = new Date(dt);
    var now_date= new Date(no);
    var _second = 1000;
    var _minute = _second * 60;
    var _hour = _minute * 60;
    var _day = _hour * 24;
    flag_time = true;
    timer = '';

    setInterval(function() {
        showRemaining();
    }, 1000);

    function showRemaining() {
        var date = no;
        var now = new Date(date);
        var de= now.getTime();

        if (flag_time) {
            timer = de;
        }

        var d = new Date(timer);
        currentYear = d.getFullYear();
        month = d.getMonth()+1;
        var currentDate = d.getDate();
        currentDate = currentDate < 10 ? '0'+currentDate : currentDate;
        var hours = d.getHours();
        var minutes = d.getMinutes();
        var seconds = d.getSeconds();
        minutes = minutes < 10 ? '0'+minutes : minutes;
        seconds = seconds < 10 ? '0'+seconds : seconds;
        var strTime = hours + ':' + minutes+ ':' + seconds;
        timer = timer + 1000;
        var now_time = currentYear+'/' + month+'/' + currentDate + ' ' + strTime ;
        var now = new Date(now_time);
        var distance = end - now;

        if (distance < 0) {
            if (distance > -3) {
                location.reload(true);
                return;
            }

            clearInterval(timer);
            document.getElementById('countdown').innerHTML = '';
            return;
        }

        var days = Math.floor(distance / _day);
        var hours = Math.floor((distance % _day) / _hour);
        var minutes = Math.floor((distance % _hour) / _minute);
        var seconds = Math.floor((distance % _minute) / _second);

        if (days <10) {
            days = '0' + days;
        }

        if (hours <10) {
            hours = '0' + hours;
        }

        if (minutes < 10) {
            minutes = '0' + minutes;
        }

        if (seconds < 10) {
            seconds = '0' + seconds;
        }

        if (days == 0) {
            dytext = '';
        } else if (days == 1) {
            dytext = '<div class="countdown-wrapper"><span class="countdown-text">' + days + '</span> <span class="countdown-info">' + Translator.translate('Day') + '</span></div> ';
        } else {
            dytext = '<div class="countdown-wrapper"><span class="countdown-text">' + days + '</span> <span class="countdown-info">' + Translator.translate('Days') + '</span></div> ';
        }

        if (hours == 0) {
            hrtext='';
        } else {
            hrtext = '<div class="countdown-wrapper"><span class="countdown-text">' + hours + '</span> <span class="countdown-info">' + Translator.translate('Hours') + '</span></div> ';
        }

        if (minutes == 0) {
            mintext='';
        } else {
            mintext = '<div class="countdown-wrapper"><span class="countdown-text">' + minutes + '</span> <span class="countdown-info">' + Translator.translate('Minutes') + '</span></div> ';
        }

        sectext = '<div class="countdown-wrapper"><span class="countdown-text">' + seconds + '</span> <span class="countdown-info">' + Translator.translate('Seconds') + '</span></div> ';
        document.getElementById('countdown').innerHTML = dytext + hrtext +  mintext + sectext;
        flag_time = false;
    }
}
