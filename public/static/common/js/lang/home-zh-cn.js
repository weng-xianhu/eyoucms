
/**
 * 前台的JS文件的多语言包
 */
function eyou_lang(key, str) {
    var langArr = new Array();
    langArr['Being dealt with']='正在处理';
    langArr['There is no list of documents in this column']='该栏目没有文档列表';
    langArr['Network request failed']='网络失败，请刷新页面后重试';
    langArr['Successful operation']='操作成功';
    langArr['Operation failed']='操作失败';
    langArr['Do not refresh the page']=str+'...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请勿刷新页面';

    return langArr[key];
}
