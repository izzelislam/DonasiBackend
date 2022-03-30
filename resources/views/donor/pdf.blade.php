@php
   $data =  \App\Models\Donor::whereBetween('id', [request('start') - 1, request('end') + 1])->get();
   $donors = $data->chunk(3);
@endphp

<table cellspacing="10" cellpadding="10">
  @foreach ( $donors as $donor )
    <tr>
      @foreach ( $donor as $item )
        <td>
          <span style="display: initial;">
            <div>
              <img src="{{ asset($item->qr) }}" alt="" style="width: 200px">
            </div>
            <div>
              <table>
                <tr>
                  <td>nama</td>
                  <td>{{ $item->name }}</td>
                </tr>
                <tr>
                  <td>uid</td>
                  <td>{{ $item->uuid }}</td>
                </tr>
                <tr>
                  <td>no telepon</td>
                  <td>{{ $item->phone_number }}</td>
                </tr>
              </table>
            </div>
          </span>
        </td>
      @endforeach
    </tr>
  @endforeach
</table>
  