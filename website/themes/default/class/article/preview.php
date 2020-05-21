<?php
/**
 * 预览
 * preview.php
 */

$website->appendTemplateByFile(THEME_DIR.'common/header.html');
$website->appendTemplateByFile(THEME_DIR.'class/article/preview.html');
$website->appendTemplateByFile(THEME_DIR.'common/footer.html');

$website->setSubData('customFooter',"
    <script src='https://cdn.jsdelivr.net/npm/vue'></script>
");

$website->setSubData('customJS',"

var app = new Vue({
  el: '#app',
  data: {
    title: '',
    description:'',
    introduce:null,
    cover:'',
    category_:'',
    createtime_:''
  }
});

window.addEventListener('message', receiveMessage, false);
function receiveMessage(event){
    if (event.data=='hello'){
        event.source.postMessage('ok','*');
    }else{
        // alert('ok');
        console.log(event.data);
        app.cover = event.data.cover;
        app.title = event.data.title;
    }
}

");

$website->rend();
