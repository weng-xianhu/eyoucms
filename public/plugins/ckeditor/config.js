/*
Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.language = 'zh-cn';
	config.font_names = '宋体/宋体;黑体/黑体;仿宋/仿宋_GB2312;楷体/楷体_GB2312;隶书/隶书;幼圆/幼圆;Arial/Arial;Comic Sans MS/Comic Sans MS;';
	config.skin = 'kama';
	config.width = 'auto';
	config.height = 450;
	config.fontSize_sizes = '30/30%;50/50%;100/100%;120/120%;150/150%;200/200%;300/300%';
	config.uiColor = '#F1F5F2';

	config.fullPage = false;

	config.autoUpdateElement = true;

	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;

	config.toolbar = 'Full';
	config.toolbar_Full = [
		['Source', '-', 'Templates'],
		['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Print'],
		['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
		['ShowBlocks'],
		['Image','Addon'],
		['Maximize'],
		['Table', 'HorizontalRule', 'Smiley', 'SpecialChar'],
		['Link', 'Unlink', 'Anchor'],
		'/',
		['Bold', 'Italic', 'Underline', 'Strike', '-'],
		['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
		['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
		['Styles', 'Format', 'Font', 'FontSize'],
		['TextColor', 'BGColor']
	];
    config.extraPlugins = 'addon';
};
