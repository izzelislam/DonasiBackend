<x-layouts.app>

  <x-slot:breadcrumb>
    <x-breadcrumb
      title="Content"
      page="Tambah"
      link="contents.index"
    />
  </x-slot>

  <x-card title="Tambah Content">
    <form class="col-8" method="POST" action="{{$action}}" enctype="multipart/form-data">
      @csrf
      @isset($model)
          @method('PUT')
      @endisset
      <x-form-input
        label="Title"
        name="title"
        value="{{ $model->title ?? '' }}"
        placeholder="isikan judul "
      />


      <x-form-input
        label="Gambar"
        name="img"
        type="file"
        placeholder="isikan password bawaan"
      />

      <x-form-textarea
        label="Content"
        name="content"
        value="{{ $model->content ?? '' }}"
        placeholder="isikan password bawaan"
        rows="12"
      />

      <button class="btn btn-primary">
        @if (isset($model))
          Ubah
        @else
          Tambah
        @endif
        Content
      </button>
  </form>
  
  </x-card>


</x-layouts.app>