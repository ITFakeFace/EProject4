<?php
use Illuminate\Support\Facades\Route;

//Department
Route::group(['prefix' => 'deparment'], function () {
    Route::get('/index', 'DepartmentController@index')->name('getListDeparment');
    Route::get('/detail', 'DepartmentController@detailDep')->name('detailDepartment');
    Route::post('/add', 'DepartmentController@CreateDepartment')->name('postAddDepartment');
    Route::get('/add', 'DepartmentController@add')->name('getAddDepartment');
    Route::get('/edit', 'DepartmentController@getEditDep')->name('getEditDepartment');
    Route::post('/edit', 'DepartmentController@postEditDep')->name('postEditDepartment');
    Route::get('/undo', 'DepartmentController@listUndo')->name('getUndoDepartment');
    Route::get('/delete', 'DepartmentController@getDeleteDep')->name('getDeleteDep');
    Route::get('/getundo', 'DepartmentController@getUndoDep')->name('getUndoDep');
});

//Staff
Route::group(['prefix' => 'staff'], function () {
    Route::get('/index', 'StaffController@index');
    Route::get('/add', 'StaffController@vaddStaff');
    Route::get('/detail', 'StaffController@getDetail');
    Route::get('/gedit', 'StaffController@getEditStaff');
    Route::post('/pedit', 'StaffController@postEditStaff');
    Route::post('/add', 'StaffController@CreateStaff')->name('postAddStaff');
    Route::get('/view-profile', 'StaffController@viewProfile');
    Route::post('/change-password', 'StaffController@changePassword');
    Route::get('/load-regional', 'StaffController@loadRegional');
    Route::get('/undo', 'StaffController@listUndo')->name('listUndo');
    Route::get('/delete', 'StaffController@getDeleteStaff')->name('getDeleteStaff');
    Route::get('/getundo', 'StaffController@getUndoStaff')->name('getUndoStaff');
    Route::get('export-word1/{id}','StaffController@exportWord1')->name('exportWord1');
});

//Education

Route::group(['prefix' => 'education'], function () {
    Route::get('/index', 'EducationController@index');
    Route::get('/add', 'EducationController@addEducation');
    Route::post('/add', 'EducationController@createEducation')->name('postEducation');
    Route::get('/delete', 'EducationController@deleteEducation')->name('getDeleteEdu');
    Route::get('/gedit', 'EducationController@getEditEducation');
    Route::post('/pedit', 'EducationController@postEditEducation');

 
});