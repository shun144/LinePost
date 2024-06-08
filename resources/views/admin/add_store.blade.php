
@extends('adminlte::page')

@section('title','店舗登録')

@section('content_header')
  <h2>店舗登録</h2>
@stop

@section('content')


<div class="storeadd">


  <div class="back">
    <a href="{{route('admin.store')}}">
        <i class="fas fa-arrow-left"></i>
        <span>店舗情報一覧に戻る</span>
    </a>
  </div>
  
  <div class="card card-custom">

    <div class="card-header">
      <div class="row">
        <div class="col-md-10 d-flex align-items-center">
          <h3 class="card-title">店舗登録フォーム</h3>
        </div>
        <div class="col-md-2 d-flex justify-content-end">
          <button form="formAddStore" type="submit" class="btn">店舗登録</button>
         </div>
      </div>
    </div>


    <div class="card-body"> 
           
      <form id="formAddStore" action="{{ route('store.add') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label for="store_name">店舗名</label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="store_name" name="name" 
          value="{{ old('name')}}" maxlength="{{config('field.store.name.max')}}">
          <div class="invalid-feedback">@error('name'){{ $message }}@enderror</div>
          <small class="form-text text-muted">最大文字数：{{config('field.store.name.max')}}</small>
        </div>
  
        <div class="form-group">
          <label for="store_url_name">店舗アルファベット名</label>
          <input type="text" class="form-control @error('url_name') is-invalid @enderror" id="store_url_name" 
          name="url_name" value="{{ old('url_name')}}" maxlength="{{config('field.store.url_name.max')}}">
          <div class="invalid-feedback">@error('url_name'){{ $message }}@enderror</div>
          <small class="form-text text-muted">最大文字数：{{config('field.store.url_name.max')}} / 入力例:tempoa, shop01</small>
        </div>
  
        <div class="form-group">
          <label for="login_id">ログインID</label>
          <input type="text" class="form-control @error('login_id') is-invalid @enderror" id="login_id" 
          name="login_id" value="{{ old('login_id')}}" maxlength="{{config('field.user.login_id.max')}}">
          <div class="invalid-feedback">@error('login_id'){{ $message }}@enderror</div>
          <small class="form-text text-muted">入力文字数範囲：{{config('field.user.login_id.min')}}-{{config('field.user.login_id.max')}}</small>
        </div>
  
  
        <div class="form-group">
          <label for="login_password">パスワード</label>
          <input type="text" class="form-control @error('login_password') is-invalid @enderror" id="login_password" 
          name="login_password" value="{{ old('login_password')}}" maxlength="{{config('field.user.login_password.max')}}">
          <div class="invalid-feedback">@error('login_password'){{ $message }}@enderror</div>
          <small class="form-text text-muted">入力文字数範囲：{{config('field.user.login_password.min')}}-{{config('field.user.login_password.max')}}</small>
        </div>
      
        <div class="form-group">
          <label for="client_id">LINEサービスID</label>
          <input type="text" class="form-control @error('client_id') is-invalid @enderror" id="client_id" 
          name="client_id" value="{{ old('client_id')}}">
          <div class="invalid-feedback">@error('client_id'){{ $message }}@enderror</div>
          <small class="form-text text-muted">LINE連携サービス登録時に発行されたClient IDを入力してください</small>
        </div>
  
        <div class="form-group">
          <label for="client_secret">LINEサービスパスワード</label>
          <input type="text" class="form-control @error('client_secret') is-invalid @enderror" id="client_secret" 
          name="client_secret" value="{{ old('client_secret')}}">
          <div class="invalid-feedback">@error('client_secret'){{ $message }}@enderror</div>
          <small class="form-text text-muted">LINE連携サービス登録時に発行されたClient Secretを入力してください</small>
        </div>


      </form>
    </div>

  </div>

</div>


@stop


@section('css')
<link rel="stylesheet" href="{{ asset('build/assets/storeadd.css')}}">
@stop
@section('js')
@stop


