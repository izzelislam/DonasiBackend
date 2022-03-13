@props(['rows', 'data', 'actions'])

<div class="table-responsive p-0">
  <table id="myTable" class="table" style="width:100%">
    <thead>
      <tr>
        <th width="60">No</th>
        @foreach ($rows as $row)
          <th>{{ $row }}</th>
        @endforeach
        @isset($actions)
          <th>Action</th>
        @endisset
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $index => $item)
        <tr>
          <td>{{ $index + 1 }}</td>
          @foreach ($rows as $key => $row)
            <td>{{ $item[$key] }}</td>
          @endforeach
          @isset($actions)
            <td>
              @foreach ($actions as $action)
                <a href="{{ route($action['url'], $index) }}" class="btn btn-icon btn-3 btn-{{ $action['color'] }}" >
                  <span class="btn-inner--icon"><i class="fas fa-{{ $action['icon'] }}"></i></span>
                  <span class="btn-inner--text">{{ $action['label'] }}</span>
                </a>
              @endforeach
            </td>
          @endisset
        </tr>
      @endforeach
    </tbody>
  </table>
</div>