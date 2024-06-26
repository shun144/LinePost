@extends('adminlte::page')

@section('title', '配信')

@section('content_header')
  <h2>配信</h2>
@stop


@section('content')

  <div class="delivery">
    <form id="form-post-msg" action="{{ route('post') }}" method="post" enctype="multipart/form-data">
      <input id="postImiCsrfToken" type="hidden" name="_token" value="{{csrf_token()}}">


      <div class="btn-area">
        <button type="submit" class="btn btn-post">送信</button>
      </div>
      

      <div class="form-group">
        <label for="content" class="form-label">配信内容（上限文字数: 1,000文字）</label>
        <textarea class="form-control input-textarea" name="content" rows="10" maxlength="1000"  aria-describedby="contentHelp"></textarea>
        <div class="invalid-feedback"></div> 
      </div>

      <div class="form-group">
        <label for="input_file">配信画像（pngまたはjpgのみ。上限ファイル数: 1枚）</label>
        <div class="image_component" style="width:100%">
          <div class="input-group">
            <div class="btn-group">
              <label>
                <span class="btn btn-outline-primary">選択
                  <input type="file" style="display:none;" multiple="multiple" class="form-control-file input-img" name="imagefile[]" accept="image/jpeg,image/png"/>
                </span>
              </label>
              <label>
                <span class="btn btn-outline-danger btn-del-file">削除</span>
                <input type="hidden" name='has-file' class='has-file'>
              </label>
            </div>
            <input type="text" class="img-name-text form-control" readonly="" aria-describedby="imgFileHelp">
          </div>             
          <p class="img-preview"></p>
        </div>
      </div>

    </form>
  </div>
</div>  
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
<link rel="stylesheet" href="{{ asset('build/assets/delivery.css')}}">
@stop

@section('js')
<script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}" defer></script>
<script src="{{ asset('build/assets/delivery.js')}}" defer></script>
@stop


{{-- @extends('adminlte::page')

@section('title', '配信')

@section('content_header')
  <h2>配信</h2>
@stop


@section('content')

  <div class="delivery">
    <form id="form-post-msg" action="{{ route('post') }}" method="post" enctype="multipart/form-data">
      <input id="postImiCsrfToken" type="hidden" name="_token" value="{{csrf_token()}}">
      <div class="card">
        <div class="card-header">
          <button type="submit" class="btn btnPostImi">送 信</button>
        </div>
  
        <div class="card-body">
          <div class="form-group">
            <label for="content" class="form-label">配信内容</label>
            <small id="contentHelp" class="form-text text-muted">LINEで通知される内容です（上限文字数: 1,000）</small>
            <textarea class="form-control input-textarea" name="content" rows="10" maxlength="1000"  aria-describedby="contentHelp"></textarea>
            <div class="invalid-feedback"></div> 
          </div>
    
          <div class="form-group">
            <label for="input_file">配信画像</label>
            <small id="imgFileHelp" class="mt-0 mb-2 form-text text-muted">送信可能な拡張子はpng,jpegです（上限ファイル数: 1）</small>
            <div class="image_component" style="width:100%">
              <div class="input-group">
                <div class="btn-group">
                  <label>
                    <span class="btn btn-outline-primary">選択
                      <input type="file" style="display:none;" multiple="multiple" class="form-control-file input-img" name="imagefile[]" accept="image/jpeg,image/png" aria-describedby="imgFileHelp"/>
                    </span>
                  </label>
                  <label>
                    <span class="btn btn-outline-danger btn-del-file">削除</span>
                    <input type="hidden" name='has-file' class='has-file'>
                  </label>
                </div>
                <input type="text" class="img-name-text form-control" readonly="">
              </div>             
              <p class="img-preview"></p>
            </div>
          </div>
        </div>
  
      </div>
    </form>
  </div>
</div>  
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
<link rel="stylesheet" href="{{ asset('build/assets/delivery.css')}}">
@stop

@section('js')
<script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}" defer></script>
<script src="{{ asset('build/assets/delivery.js')}}" defer></script>
@stop --}}
