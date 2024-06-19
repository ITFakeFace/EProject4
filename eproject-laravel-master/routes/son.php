<?php

use Illuminate\Support\Facades\Route;

Route::get('test', function(){
    return view('main.salary.exports.payroll_personal');
});
Route::group(['prefix' => 'auth'], function () {
    Route::get('login', 'AuthenticateController@getLogin');
    Route::post('login', 'AuthenticateController@postDoLogin')->name('postLogin');
    Route::get('logout', 'AuthenticateController@getLogout');
});

Route::group(['prefix' => 'contract'], function () {
    Route::get('list','ContractController@getList')->name('getListContract');
    Route::get('create','ContractController@getCreate')->name('getCreateContract');
    Route::get('detail/{id}','ContractController@getDetail')->name('getDetailContract');
    Route::post('save','ContractController@postSave')->name('postSaveContract');
    Route::get('stop/{id?}','ContractController@stopContract')->name('stopContractContract');
    Route::get('delete/{id}','ContractController@getDelete')->name('getDeleteContract');
    Route::get('export-word/{id}','ContractController@exportWord')->name('exportWord');
});

Route::group(['prefix' => 'salary'], function () {
    Route::get('list','SalaryController@getIndex')->name('getIndexSalary');
    Route::get('my-salary','SalaryController@mySalary')->name('mySalary');
    Route::get('details','SalaryController@getDetail')->name('getDetailSalary');
    Route::get('create','SalaryController@getCreate')->name('getCreateSalary');
    Route::get('change-status-success/{id?}','SalaryController@getChangeStatusSuccessSalary')->name('getChangeStatusSuccessSalary');
    Route::post('create','SalaryController@postCalculatedSalary')->name('postCalculatedSalary');
    Route::get('delete/{id?}','SalaryController@getDeleteSalary')->name('getDeleteSalary');
    Route::get('export/{id?}','SalaryController@exportPayroll')->name('exportPayroll');
    Route::get('export-staff-payroll/{id?}','SalaryController@exportStaffPayroll')->name('exportStaffPayroll');
});
