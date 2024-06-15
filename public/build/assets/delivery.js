const URL_ROOT = $(location).attr('origin');
const URL_STORAGE =  `${URL_ROOT}/storage/owner/image/template`;
const URL_DASHBOARD = `${URL_ROOT}/dashboard`;
const URL_MEDSSAGE_POST = `${URL_DASHBOARD}/post`;


let iTextArea = null;
let feedback = null;
let csrfToken = null;
let imgName = null;
let imgPreview = null;

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// DOMContentLoaded
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
window.addEventListener('DOMContentLoaded', () => {

  iTextArea = document.querySelector('textarea.input-textarea');
  iFeedback = document.querySelector('.invalid-feedback');
  csrfToken = document.getElementById('postImiCsrfToken').value;

  iImg = document.querySelector('.input-img');
  imgName = document.querySelector('.img-name-text');
  imgPreview = document.querySelector('.img-preview');
  hasImg = document.querySelector('.has-file');
  
  // 送信
  document.querySelector('.btn-post').addEventListener('click', (e)=> {
    e.preventDefault();
    postImmediately();
  });

  // 画像プレビュー
  iImg.addEventListener('change', (e) => {
    previewImg(e)
  });

  // 配信内容テキストエリアキーダウン
  iTextArea.addEventListener('keydown', (e) => e.target.classList.remove('is-invalid'));

  // 画像削除ボタンクリック
  document.querySelector('.btn-del-file').addEventListener('click', () => clearImgForm());
});


// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 配信内容入力フォームクリア
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
const clearTextAreaForm = () => {
  iTextArea.value = '';
};

// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 画像入力フォームクリア
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
const clearImgForm = () => {
  iImg.value = '';
  imgName.value  = '';
  hasImg.value = 0;
  
  if (imgPreview.hasChildNodes('img')) {
    imgPreview.removeChild(imgPreview.querySelector('img'));
  } 
};



// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 配信
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
const postImmediately = () => {
  const tLeng = iTextArea.value.length;

  if (tLeng == 0) {
    iFeedback.textContent = '必須項目です';
    iTextArea.classList.add("is-invalid");
    return;
  }

  if (tLeng > 1000)
  {
    iFeedback.textContent = '入力可能文字数は1000文字です';
    iTextArea.classList.add("is-invalid");
    return;
  }

  if(!window.confirm('配信を開始してよろしいですか?')){ return false; }

  const form = $('#form-post-msg');
  $.ajax({
    headers: {'X-CSRF-TOKEN': csrfToken},
    url: URL_MEDSSAGE_POST,
    method: 'POST',
    contentType: false,
    processData: false,
    data: new FormData(form.get(0))
  });

  clearTextAreaForm();
  clearImgForm();

  toastr.info('配信を開始しました。<br/> 状況は配信履歴一覧をご確認ください');
};


// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
// 画像プレビュー表示
// /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
const previewImg = (event) => 
{
  const files = event.target.files;

  // 画像プレビュー初期化
  if (imgPreview.hasChildNodes('img')) {
    imgPreview.removeChild(imgPreview.querySelector('img'));
  } 

  if (files.length == 1) {
    const file = files[0];
    if (['image/jpeg','image/png'].indexOf(file.type) !== -1) {
      const reader = new FileReader();
      reader.onload = ((elem) => {
        imgElem = document.createElement('img');
        imgElem.src = elem.target.result;
        imgPreview.appendChild(imgElem);
      });
      
      reader.readAsDataURL(file);
      imgName.value = file.name;
      hasImg.value = 1;
    } 
    else {
      clearImgForm();
    }
  }
  else {
    clearImgForm();
    return false;
  }
}
