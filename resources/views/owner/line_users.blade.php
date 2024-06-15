@extends('adminlte::page')

@section('title','LINEユーザ一覧')

@section('content_header')
  <h2>LINEユーザ一覧</h2>
@stop

@section('content')

  <div class="status-loader">
    <div class="loading">
      <div class="loading-bar"></div>
      <div class="loading-bar"></div>
      <div class="loading-bar"></div>
      <div class="loading-bar"></div>
    </div>
  </div>

  <div class="lineuser">

    <div class="entry-url">
      <label for="entry-url-form">LINE連携ページURL(読取)</label>
      <div class="input-group">
        <span class="btn btn-outline-secondary btn-copy"><i class="far fa-copy"></i></span>
        <input type="text" class="form-control input-entry-url" name="entry-url-form" value="{{isset($reg_url) ? $reg_url:'―'}}" readonly>
      </div>
    </div>

    <div class="taikai-check">
      <button form="upd-status-form" type="submit" class="btn btn-taikai">退会確認</button>
      <form id="upd-status-form" action="{{ route('line_users.upd.status') }}" method="get">
        @csrf
      </form>
    </div>

    <div class="lineuser-table">
      <table id="line-user-table" class="table table-striped table-bordered" style="table-layout:fixed;">
        <thead>
          <tr>
            @foreach (["状態","登録日時","LINE名"] as $col)
            <th class="text-center">{{$col}}</th>
            @endforeach
          </tr>
        </thead>

        <tbody>

          @if(isset($lines))
            @foreach ($lines as $line)
            <tr>
              <td class="omit_text">
                <button type="submit" class="btn btn-st-change{{$line->is_valid == 1 ? ' is-valid':''}}" for="tr-form-{{$loop->index}}">{{$line->is_valid == 1 ? '有効':'無効'}}</button>
                  <form id="tr-form-{{$loop->index}}">
                  @csrf
                  <input type="hidden" name="line_user_id" value={{$line->id}}>
                  <input type="hidden" name="is-valid" value={{$line->is_valid}}>
                  <input type="hidden" class="hid-line-user-name" value={{$line->user_name}}>
                </form>

              </td>
              <td class="omit_text">{{$line->created_at}}</td>
              <td class="omit_text">{{$line->user_name}}</td>
            </tr>
            @endforeach

          @endif
        </tbody>
      </table>
    </div>
  </div>  
  
 @stop

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
<link rel="stylesheet" href="{{ asset('build/assets/lineuser.css')}}">
@stop

@section('js')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}" defer></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}" defer></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}" defer></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}" defer></script>
<script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}" defer></script>
<script src="{{ asset('build/assets/lineUser.js')}}" defer></script>

<script>
@if (isset($get_lineuser_error_flushMsg))
  $(function () {toastr.error('{{ $get_lineuser_error_flushMsg }}');});
@endif

@if (session('edit_lineuser_success_flushMsg'))
  $(function () {toastr.success('{{ session('edit_lineuser_success_flushMsg') }}');});
@endif

@if (session('edit_lineuser_error_flushMsg'))
  $(function () {toastr.error('{{ session('edit_lineuser_error_flushMsg') }}');});
@endif

</script>
@stop

