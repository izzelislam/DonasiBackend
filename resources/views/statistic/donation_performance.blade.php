<x-card 
    title="Statistic Performa Donasi / Tim" 
  >
  <div class="chart">
    <canvas id="chart-line" class="chart-canvas" height="400px" width="100%"></canvas>
  </div>
</x-card>

<script>
  const donations =  Object.entries( @json($donations) );

  var ctx1 = document.getElementById("chart-line").getContext("2d");
  var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);
  const a = [];

  donations.map( e => {
    res = []
    e[1].map(r => {
      res.push(r.sums)
    })
    a.push({
        label: e[0],
        tension: 0.4,
        borderWidth: 0,
        pointRadius: 0,
        borderColor: "#"+ Math.floor(Math.random()*16777215).toString(16),
        backgroundColor: gradientStroke1,
        borderWidth: 3,
        fill: true,
        data: res,
        maxBarThickness: 6
      })
  })

  new Chart(ctx1, {
    type: "line",
    data: {
      labels: ["Jan","Feb","Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: a
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
        }
      },
      interaction: {
        intersect: false,
        mode: 'index',
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: true,
            drawOnChartArea: true,
            drawTicks: false,
            borderDash: [5, 5]
          },
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
            borderDash: [5, 5]
          },
          ticks: {
            display: true,
            color: '#ccc',
            padding: 20,
            font: {
              size: 11,
              family: "Open Sans",
              style: 'normal',
              lineHeight: 2
            },
          }
        },
      },
    },
  });
</script>