<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Export Kode Donatur</title>

  <style>
    #app {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
      height: 100vh;
      width: 100vw;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }
  </style>
</head>
<body>
  <div id="app">
  </div>
</body>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    var donatur = @json($donors);

    // append and loop donatur array of array
    donatur.forEach(donatur => {
      var createelement = document.createElement('div');
      createelement.innerHTML = donatur.uuid;

      // set style
      createelement.style = 'margin: 10px; padding: 10px; border: 1px solid #ccc; text-align: center; font-size: 12px; font-weight: bold; background-color: #f5f5f5; margin: 10px; padding: 10px; border: 1px solid #ccc; text-align: center; font-size: 12px; font-weight: bold; background-color: #f5f5f5;';

      document.getElementById('app').appendChild(createelement);

      var qr = new QRCode(document.getElementById("app").appendChild(createelement), {
        text: donatur.uuid,
        width: 128,
        height: 128,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
      });

      // var qr = new QRCode({
      //   element: document.getElementById("app"),
      //   value: donatur.uuid
      // });
    });

    // trigger ctrl + p on load
    // document.addEventListener('keydown', function(e) {
    //   if (e.ctrlKey && e.key === 'p') {
    //     e.preventDefault();
    //   }
    // });
    window.print();
    

  </script>

</html>