const URL_ROOT = $(location).attr('origin');
const URL_DASHBOARD = `${URL_ROOT}/dashboard`;
const URL_CHANGE_ST = `${URL_DASHBOARD}/line-users-edit`;

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// DOMContentLoaded
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
window.addEventListener('DOMContentLoaded', () => {

  // 退会ボタンクリック
  document.querySelector('.btn-taikai').addEventListener('click', (e) => {
    e.preventDefault();
    checkTaikai();
  });

  // コピーボタンクリック
  document.querySelector('.btn-copy').addEventListener('click', (e) => {
    e.preventDefault();
    copyToClipboard();
  });

  // 状態ボタンクリック
  document.querySelectorAll('.btn-st-change').forEach(elem => {
    elem.addEventListener('click', e => changeStatus(e));
  });
  
  // 連携LINEユーザ一覧テーブル作成
  createTable();
});


// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// LINEユーザ状態確認
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
const checkTaikai = () => {
  if(!window.confirm('退会済みになっているLINEユーザを無効にしてよろしいですか?')){return false;}

  // ローダー表示
  document.querySelector('.status-loader').classList.add('view');

  // 退会確認
  document.querySelector('#upd-status-form').submit();
};


// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// URLクリップボードコピー
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
const copyToClipboard = () => {
  const entryUrl = document.querySelector(".input-entry-url").value;
  if(navigator.clipboard == undefined) {
    window.clipboardData.setData('Text', entryUrl);
  } else {
    navigator.clipboard.writeText(entryUrl);
  }
}

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 状態変更（ajax処理）
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
const changeStatus = (e) => {
  e.preventDefault();
  const btn = e.currentTarget;
  const form = document.querySelector(`#${btn.getAttribute('for')}`);
  const name = form.querySelector('.hid-line-user-name').value;
  const valid = form.querySelector('input[name="is-valid"]');

  const msg = `${name} さんを${valid.value == 0 ? '有効化' : '無効化'}してよろしいですか?`
  if(!window.confirm(msg)){return false; }

  const csrfToken = form.querySelector('input[name="_token"]');

  $.ajax({
    headers: {'X-CSRF-TOKEN': csrfToken},
    url: URL_CHANGE_ST,
    method: 'POST',
    contentType: false,
    processData: false,
    data: new FormData($(form).get(0)),
  })
  .done(res => {
    toastr.success(res.message);

    if (res.newValid == '1') {
      btn.classList.add('is-valid');
      btn.textContent = '有効';
      valid.value = '1';
    } else {
      btn.classList.remove('is-valid');
      btn.textContent = '無効';
      valid.value = '0';
    }
  })
  .fail(res => {
    toastr.error(res.responseJSON.message);
  });
}



// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// LINE連携ユーザ一覧テーブル作成
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
const createTable = () => {
  $.extend( $.fn.dataTable.defaults, { 
    language: {url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json" } 
  }); 
  $('#line-user-table').DataTable({
    paging:true,
    lengthChange:false,
    searching:true,
    ordering:true,
    info:true,
    autoWidth: false,
    responsive:false,
    order: [[1,"desc"]],
    columnDefs:[
      { targets:0, width:'6%'},
      { targets:1, width:'25%'}, 
    ],
  });
};
