
<x-layouts.app>
  
  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Content"
      page="Detail"
      link="contents.index"
    />
  </x-slot>

  <x-card title="Detail Content">
    <div class="row">
      <div class="col-6">
        <img src="{{ asset($model->image) }}" alt="" style="width: 100%;">

        <div>
          <h4>{{ $model->title }}</h4>
        </div>

        <div class="my-4">
          <b><i>{{ $model->created_at->format('d/m/Y H:i:s') }}</i></b>
        </div>
  
        <div>
          <p>
            {{ $model->content }}
          </p>
        </div>

      </div>
    </div>
  </x-card>

</x-layouts.app>