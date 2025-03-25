<?php

use App\Http\Controllers\AddNewUsersController;
use App\Http\Controllers\AktivController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FileController;

use App\Http\Controllers\SearchController;
use App\Http\Controllers\StreetController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SubStreetController;
use App\Http\Controllers\Blade\HomeController;
use App\Http\Controllers\Blade\RoleController;
use App\Http\Controllers\Blade\UserController;
use App\Http\Controllers\Blade\RegionController;
use App\Http\Controllers\Blade\ApiUserController;
use App\Http\Controllers\Blade\DistrictController;
use App\Http\Controllers\Blade\PermissionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\YerTolaController;

// Default laravel auth routes
Auth::routes(['register' => false]);

// Welcome page
Route::get('/', function () {
    return view('welcome');
});


Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/statistics', [HomeController::class, 'statistics'])->name('statistics.index');
// Web pages
Route::group(['middleware' => ['auth', 'checkUserRole']], function () {

    Route::get('aktivs', [AktivController::class, 'index'])->name('aktivs.index');
    Route::get('aktivs/create', [AktivController::class, 'create'])->name('aktivs.create');
    Route::post('aktivs', [AktivController::class, 'store'])->name('aktivs.store');
    Route::get('aktivs/{aktiv}', [AktivController::class, 'show'])->name('aktivs.show');
    Route::get('aktivs/{aktiv}/edit', [AktivController::class, 'edit'])->name('aktivs.edit');
    Route::put('aktivs/{aktiv}', [AktivController::class, 'update'])->name('aktivs.update');
    Route::delete('aktivs/{aktiv}', [AktivController::class, 'destroy'])->name('aktivs.destroy');
    Route::get('aktiv/users', [AktivController::class, 'userAktivCounts'])->name('aktivs.userAktivCounts');
    Route::get('aktiv/tumanlar', [AktivController::class, 'userTumanlarCounts'])->name('aktivs.userTumanlarCounts');


    Route::get('aktiv/kadastr/tumanlar', [AktivController::class, 'kadastrTumanlarCounts'])->name('aktivs.kadastrTumanlarCounts');
    Route::get('/aktiv/kadastr_borlar', [AktivController::class, 'kadastrBorlar'])->name('aktivs.kadastrBorlar');
    Route::get('/aktiv/kadastr/{district_id}', [AktivController::class, 'kadastrByDistrict'])->name('aktivs.kadastrByDistrict');
    Route::get('/aktiv/get_kadastr_by_district', [AktivController::class, 'getKadastrByDistrict']);



    Route::post('/aktivs/export', [AktivController::class, 'export'])->name('aktivs.export');


    Route::get('/kadastr', [AktivController::class, 'kadastr_index'])->name('aktivs.kadastr_index');
    Route::post('/aktivs/kadastr', [AktivController::class, 'kadastr'])->name('aktivs.kadastr');
    Route::post('/aktivs/{id}/comments', [CommentController::class, 'store'])->name('comments.store');



    Route::get('/my-map', [AktivController::class, 'myMap'])->name('aktivs.myMap');


    Route::get('/getDistricts', [AktivController::class, 'getDistricts'])->name('getDistricts');
    Route::get('/getStreets', [AktivController::class, 'getStreets'])->name('getStreets');
    Route::get('/getSubStreets', [AktivController::class, 'getSubStreets'])->name('getSubStreets');
    Route::post('/create/streets', [AktivController::class, 'createStreet'])->name('create.streets');
    Route::post('/create/substreets', [AktivController::class, 'createSubStreet'])->name('create.substreets');
    // Route::get('maps/aktivs', [AktivController::class, 'getLots']);


    Route::get('/get-districts', [AktivController::class, 'getObDistricts'])->name('get.Obdistricts');
    Route::get('/get-streets', [AktivController::class, 'getObStreets'])->name('get.Obstreets');
    Route::get('/get-substreets', [AktivController::class, 'getObSubstreets'])->name('get.Obsubstreets');


    Route::get('/optimize-cache', [HomeController::class, 'optimize'])->name('optimize.command');


    // Permissions
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permissionIndex');
        Route::get('/add', [PermissionController::class, 'add'])->name('permissionAdd');
        Route::post('/create', [PermissionController::class, 'create'])->name('permissionCreate');
        Route::get('/{id}/edit', [PermissionController::class, 'edit'])->name('permissionEdit');
        Route::post('/update/{id}', [PermissionController::class, 'update'])->name('permissionUpdate');
        Route::delete('/delete/{id}', [PermissionController::class, 'destroy'])->name('permissionDestroy');
    });
    // Roles
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roleIndex');
        Route::get('/add', [RoleController::class, 'add'])->name('roleAdd');
        Route::post('/create', [RoleController::class, 'create'])->name('roleCreate');
        Route::get('/{role_id}/edit', [RoleController::class, 'edit'])->name('roleEdit');
        Route::post('/update/{role_id}', [RoleController::class, 'update'])->name('roleUpdate');
        Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('roleDestroy');
    });
    // ApiUsers
    Route::prefix('api-users')->group(function () {
        Route::get('/', [ApiUserController::class, 'index'])->name('api-userIndex');
        Route::get('/add', [ApiUserController::class, 'add'])->name('api-userAdd');
        Route::post('/create', [ApiUserController::class, 'create'])->name('api-userCreate');
        Route::get('/show/{id}', [ApiUserController::class, 'show'])->name('api-userShow');
        Route::get('/{id}/edit', [ApiUserController::class, 'edit'])->name('api-userEdit');
        Route::post('/update/{id}', [ApiUserController::class, 'update'])->name('api-userUpdate');
        Route::delete('/delete/{id}', [ApiUserController::class, 'destroy'])->name('api-userDestroy');
        Route::delete('-token/delete/{id}', [ApiUserController::class, 'destroyToken'])->name('api-tokenDestroy');
    });

    // File
    Route::get('/test/download', [FileController::class, 'downloadDocument'])->name('download.document');
    Route::prefix('files')->group(function () {
        Route::get('/doc/{id}', [FileController::class, 'show'])->name('word');
        Route::get('/test/{id}', [FileController::class, 'test'])->name('test.word');

        Route::get('/downloading-excel/{id}', [FileController::class, 'downloadTableData'])->name('download.table.data');
        Route::get('/select-columns', [FileController::class, 'showColumnSelectionForm'])->name('select.columns');
        Route::get('/download-excel', [FileController::class, 'downloadExcel'])->name('download.excel');
    });

    // History
    Route::get('/histories', [HistoryController::class, 'index'])->name('histories.index');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/request-confirm', [HistoryController::class, 'confirm'])->name('request.confirm');
    Route::get('/history/{id}', [HistoryController::class, 'showHistory'])->name('history.show');
});

Route::group(['middleware' => ['auth']], function () {
    // Users
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('userIndex');
        Route::get('/add', [UserController::class, 'add'])->name('userAdd');
        Route::post('/create', [UserController::class, 'create'])->name('userCreate');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('userEdit');
        Route::post('/update/{id}', [UserController::class, 'update'])->name('userUpdate');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('userDestroy');
        Route::get('/theme-set/{id}', [UserController::class, 'setTheme'])->name('userSetTheme');
    });

    Route::get('/yertola', [YerTolaController::class, 'index'])->name('yertola.index');

    // Show form to create new YerTola
    Route::get('/yertola/create', [YerTolaController::class, 'create'])->name('yertola.create');

    // Store new YerTola record
    Route::post('/yertola', [YerTolaController::class, 'store'])->name('yertola.store');

    // Show a specific YerTola record
    Route::get('/yertola/{id}', [YerTolaController::class, 'show'])->name('yertola.show');

    // Show form to edit YerTola
    Route::get('/yertola/{id}/edit', [YerTolaController::class, 'edit'])->name('yertola.edit');

    // Update YerTola record
    Route::put('/yertola/{id}', [YerTolaController::class, 'update'])->name('yertola.update');

    // Delete YerTola record
    Route::delete('/yertola/{id}', [YerTolaController::class, 'destroy'])->name('yertola.destroy');


    Route::post('user/update/users', [UserController::class, 'updateUserNames'])->name('userUpdateNames');
    // Constructions
});
Route::get('/gerb/{id}', [FileController::class, 'gerb'])->name('file.mobile');
Route::get('/dopShow/{id}', [FileController::class, 'dop'])->name('dopShow');


Route::get('/language/{lang}', function ($lang) {
    $lang = strtolower($lang);
    if ($lang == 'ru' || $lang == 'uz') {
        session([
            'locale' => $lang
        ]);
    }
    return redirect()->back();
})->name('changelang');

// new -----------------------


Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('search', [SearchController::class, 'search'])->name('search');


// product



// Regions
Route::get('/regions', [RegionController::class, 'index'])->name('regionIndex');
Route::get('/region/add', [RegionController::class, 'add'])->name('regionAdd');
Route::post('/region/create', [RegionController::class, 'create'])->name('regionCreate');
Route::get('/region/edit/{id}', [RegionController::class, 'edit'])->name('regionEdit');
Route::post('/region/update/{id}', [RegionController::class, 'update'])->name('regionUpdate');
Route::delete('/region/delete/{id}', [RegionController::class, 'destroy'])->name('regionDestroy');
// Districts
Route::get('/districts', [DistrictController::class, 'index'])->name('districtIndex');
Route::get('/district/add', [DistrictController::class, 'add'])->name('districtAdd');
Route::post('/district/create', [DistrictController::class, 'create'])->name('districtCreate');
Route::get('/district/edit/{id}', [DistrictController::class, 'edit'])->name('districtEdit');
Route::post('/district/update/{id}', [DistrictController::class, 'update'])->name('districtUpdate');
Route::delete('/district/delete/{id}', [DistrictController::class, 'destroy'])->name('districtDestroy');
Route::get('/get-districts/{region_id}', [DistrictController::class, 'getDistricts'])->name('get.districts');
Route::get('/get-streets/{district_id}', [DistrictController::class, 'getStreets']);
// streets
Route::get('/streets', [StreetController::class, 'index'])->name('streetIndex');
Route::get('/street/add', [StreetController::class, 'add'])->name('streetAdd');
Route::post('/street/create', [StreetController::class, 'create'])->name('streetCreate');
Route::get('/street/edit/{id}', [StreetController::class, 'edit'])->name('streetEdit');
Route::post('/street/update/{id}', [StreetController::class, 'update'])->name('streetUpdate');
Route::delete('/street/delete/{id}', [StreetController::class, 'destroy'])->name('streetDestroy');
Route::get('/get-product-by-street/{street_id}', [StreetController::class, 'getProductByStreet'])->name('getProductByStreet');
Route::post('/create/street', [StreetController::class, 'create_new'])->name('create.street');

// Substreet
Route::get('/substreets', [SubStreetController::class, 'index'])->name('substreetIndex');
Route::get('/substreet/add', [SubStreetController::class, 'add'])->name('substreetAdd');
Route::post('/substreet/create', [SubStreetController::class, 'create'])->name('substreetCreate');
Route::get('/substreet/edit/{id}', [SubStreetController::class, 'edit'])->name('substreetEdit');
Route::post('/substreet/update/{id}', [SubStreetController::class, 'update'])->name('substreetUpdate');
Route::delete('/substreet/delete/{id}', [SubStreetController::class, 'destroy'])->name('substreetDestroy');
Route::post('/create/substreet', [SubStreetController::class, 'create_new'])->name('create.substreet');


Route::get('/generate/doc', [FileController::class, 'index'])->name('generate_doc');


Route::post('/import-users', [AddNewUsersController::class, 'importUsers'])->name('import.users');
