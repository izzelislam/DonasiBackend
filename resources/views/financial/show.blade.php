
<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Keuangan"
      page="Detail"
      link="financials.index"
    />
  </x-slot>

  <x-card title="Detail data keuangan">
    <div class="row">
      <div class="col-6">
        <table class="table">
          <tr>
            <td>Nama Transaksi</td>
            <td>{{ $transaction->title }}</td>
          </tr>

          <tr>
            <td>Kode Transaksi</td>
            <td>{{ $transaction->receipt_uid }}</td>
          </tr>

          <tr>
            <td>Jumlah</td>
            <td><b>RP {{ number_format($transaction->amount) }}</b></td>
          </tr>

          <tr>
            <td>Jenis Transaksi</td>
            <td>
              {{ $transaction->type == 'income' ? 'Masuk' : 'Keluar'  }} 
              <i class="fa fa-arrow-{{ $transaction->type == 'income' ? 'down' : 'up'  }} text-{{ $transaction->type == 'income' ? 'success' : 'danger'  }}"></i></td>
          </tr>

          <tr>
            <td>Tanggal Transaksi</td>
            <td>{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td>
          </tr>

          <tr>
            <td>Catatan</td>
            <td>
              <p>
                {{ $transaction->note }}
              </p>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </x-card>

</x-layouts.app>