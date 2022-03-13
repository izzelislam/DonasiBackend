<div class="row">
  <div class="col-6">
    <x-card 
      title="Rekap Statistic Donasi" 
    >
      <div class="chart">
        <canvas id="chart-bar" class="chart-canvas" height="300px" width="100%"></canvas>
      </div>
    </x-card>
  </div>
  <div class="col-6">
    <x-card 
      title="Rekap Statistic Keuangan" 
    >
      <div class="chart">
        <canvas id="chart-bar-finance" class="chart-canvas" height="300px" width="100%"></canvas>
      </div>
    </x-card>
  </div>
</div>

<script>
  var ctx2 = document.getElementById("chart-bar").getContext("2d");

  const recaps = Object.values( @json($recaps) );
  
  const data_ = [];
  recaps.map(e => {
    data_.push(e.sums)
  })

  new Chart(ctx2,{
    type: "bar",
    data: {
      labels: ["Jan","Feb","Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
      barPercentage: 0.5,
      barThickness:15,
      maxBarThickness:10,
      minBarLength: 2,
      data: data_,
      backgroundColor: '#0acc72',
    }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
          legend: {
            display: false,
          }
        },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    },
  });
</script>

<script>
  var ctx3 = document.getElementById("chart-bar-finance").getContext("2d");

  const income = Object.values( @json($income) );
  const expense = Object.values( @json($expense) );

  const data_income = [];
  const data_expense = [];

  income.map(e => {
    data_income.push(e.sums)
  })

  expense.map(e => {
    data_expense.push(e.sums)
  })


  new Chart(ctx3,{
    type: "bar",
    data: {
      labels: ["Jan","Feb","Mar","Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [
        {
          label: 'Pengeluaran',
          barPercentage: 0.5,
          barThickness: 10,
          maxBarThickness: 10,
          minBarLength: 2,
          data: data_expense,
          backgroundColor: '#f87979',
        },
        {
          label: 'Pemasukan',
          barPercentage: 0.5,
          barThickness: 10,
          maxBarThickness: 10,
          minBarLength: 2,
          data: data_income,
          backgroundColor: '#0acc72',
        }
    ]
    },
    options: {
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    },
  });
</script>