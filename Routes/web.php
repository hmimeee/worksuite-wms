<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Admin Route
Route::prefix('admin')->group(function() {
	
	//Articles
	Route::get('article-management/articles', 'AdminArticleController@index')->name('admin.article.index');
	Route::get('article-management/articles/{id}', 'AdminArticleController@show')->name('admin.article.show');

	//Reports
	Route::get('article-management/reports', 'AdminReportController@index')->name('admin.article.reports');
	Route::get('article-management/reports/print', 'AdminReportController@show')->name('admin.article.reportPrint');

	//Settings
	Route::get('article-management/settings', 'AdminSettingController@index')->name('admin.article.settings');
	Route::post('article-management/settings', 'AdminSettingController@update');
	Route::post('article-management/settings/update', 'AdminSettingController@updateModule')->name('admin.article.update-module');
	Route::post('article-management/settings/temp', 'AdminSettingController@temp')->name('admin.article.temp');
	//Writers
	Route::get('article-management/writers', 'AdminArticleController@writers')->name('admin.article.writers');
	//Invoices
	Route::get('article-management/invoices', 'AdminInvoiceController@index')->name('admin.article.invoices');
});

//Member Route
Route::prefix('member')->group(function() {
	
	//Articles
	Route::get('article-management/articles', 'ArticleController@index')->name('member.article.index');
	Route::get('article-management/articles/create', 'ArticleController@create')->name('member.article.create');
	Route::post('article-management/articles/create', 'ArticleController@store')->name('member.article.store');
	Route::get('article-management/articles/project/{id}', 'ArticleController@projectData')->name('member.article.projectData');
	Route::get('article-management/articles/{id}', 'ArticleController@show')->name('member.article.show');
	Route::get('article-management/articles/{id}/modal', 'ArticleController@showModal')->name('member.article.showModal');
	Route::get('article-management/articles/{id}/edit', 'ArticleController@edit')->name('member.article.edit');
	Route::post('article-management/articles/{id}/edit', 'ArticleController@update')->name('member.article.update');

	//Article status
	Route::post('article-management/articles/{id}/update/{status}', 'ArticleController@updateStatus')->name('member.article.updateStatus');
	Route::post('article-management/articles/{id}/work/{status}', 'ArticleController@workStatus')->name('member.article.workStatus');
	Route::get('article-management/articles/{id}/publish/{status}/', 'ArticleController@updatePublishStatus')->name('member.article.updatePublishStatus');
	Route::get('article-management/articles/{article}/publish/', 'ArticleController@publishing')->name('member.article.startPublishing');
	Route::post('article-management/articles/{article}/reminder', 'ArticleController@sendReminder')->name('member.article.sendReminder');
	Route::delete('article-management/articles/{id}/delete', 'ArticleController@destroy')->name('member.article.delete');
	Route::post('article-management/articles/{article}/review', 'ArticleController@review')->name('member.article.review');

	//Article Files
	Route::post('article-management/store-file', 'ArticleFileController@store')->name('member.article.storeFiles');
	Route::get('article-management/article-file/{id}', 'ArticleFileController@download')->name('member.article.downloadFile');
	Route::post('article-management/article-file/removearticle/{id}', 'ArticleFileController@removeArticle')->name('member.article.removeArticle');
	Route::post('article-management/article-file/{id}/delete', 'ArticleFileController@destroy')->name('member.article.deleteFile');

	//Article Comment
	Route::post('article-management/comment/store', 'ArticleCommentController@store')->name('member.article.storeComment');
	Route::get('article-management/comment/{comment}/show', 'ArticleCommentController@show')->name('member.article.showComment');
	Route::post('article-management/comment/{comment}/delete', 'ArticleCommentController@destroy')->name('member.article.delComment');
	Route::get('article-management/comment-file/{file}/download', 'ArticleCommentController@download')->name('member.article.commentDownload');
	Route::post('article-management/comment/store-files', 'ArticleCommentController@storeFiles')->name('member.article.storeCommentFiles');

	//Reports
	Route::get('article-management/reports', 'ReportController@index')->name('member.article.reports');
	Route::get('article-management/reports/print', 'ReportController@show')->name('member.article.reportPrint');

    //Settings
	Route::get('article-management/settings', 'SettingController@index')->name('member.article.settings');
	Route::post('article-management/settings', 'SettingController@update');
	Route::get('article-management/settings/type/create', 'ArticleTypeController@create')->name('member.article.createType');
	Route::post('article-management/settings/type/create', 'ArticleTypeController@store')->name('member.article.storeType');
	Route::post('article-management/settings/type/{type}/delete', 'ArticleTypeController@destroy')->name('member.article.deleteType');

	//Writers
	Route::get('article-management/writers', 'ArticleController@writers')->name('member.article.writers');
	Route::get('article-management/writers/{id}', 'ArticleController@writerView')->name('member.article.writer');
	Route::get('article-management/writers/{id}/add-payment-details', 'ArticleController@writerPaymentDetails')->name('member.article.writerPaymentDetails');
	Route::post('article-management/writers/{payment}/delete-payment-details', 'ArticleController@writerPaymentDetailsDelete')->name('member.article.writerPaymentDetailsDelete');
	Route::post('article-management/writers/{id}/update-payment-details', 'ArticleController@writerPaymentUpdate')->name('member.article.writerPaymentUpdate');
	Route::post('article-management/writers/{id}/store-payment-details', 'ArticleController@writerPaymentDetailsStore')->name('member.article.writerPaymentDetailsStore');
	Route::post('article-management/writers/{writer}/rate-update', 'WriterRateController@update')->name('member.article.writerRateUpdate');
	Route::post('article-management/writers/{writer}/active', 'ArticleController@writerActive')->name('member.article.writerActive');

	//Invoices
	Route::get('article-management/invoices', 'InvoiceController@index')->name('member.article.invoices');
	Route::get('article-management/invoices/create', 'InvoiceController@create')->name('member.article.createInvoice');
	Route::get('article-management/invoices/get-data/{writer}', 'InvoiceController@data')->name('member.article.invoiceData');
	Route::post('article-management/invoices/generate/{writer}', 'InvoiceController@store')->name('member.article.invoiceGenerate');
	Route::get('article-management/invoices/{invoice}/view', 'InvoiceController@show')->name('member.article.invoice');
	Route::get('article-management/invoices/{invoice}/modal', 'InvoiceController@showModal')->name('member.article.modalInvoice');
	Route::post('article-management/invoices/{invoice}/status', 'InvoiceController@update')->name('member.article.invoiceStatus');
	Route::post('article-management/invoices/{invoice}/delete', 'InvoiceController@destroy')->name('member.article.invoiceDelete');
});

