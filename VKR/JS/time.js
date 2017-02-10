function showTime()
 {
 var date = new Date();
 var H = date.getHours();
 var M = date.getMinutes();
 if (H < 10) H = "0" + H;
 if (M < 10) M = "0" + M;
 document.getElementById('time').innerHTML = H + ":" + M;
 setTimeout(showTime, 1000);  // обновить  1 раз в секунду.
 }