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

  <x-slot:addonscript>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script>
      $(document).ready(function() {
        $('#summernote').summernote({
          tabsize: 2,
          height: 220,
          toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
          ]
        });
      });
    </script>
  </x-slot>

</x-layouts.app>