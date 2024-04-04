<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoraitController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix("auth")->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/change', [AuthController::class, 'change'])->middleware('auth:api');
    Route::get('/check', [AuthController::class, 'check'])->middleware('auth:api');
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::delete('/deleteAccount', [AuthController::class, 'deleteAccount'])->middleware('auth:api');
    Route::post('/fcmtoken', [AuthController::class, 'fcmToken'])->middleware('auth:api');
});


Route::prefix("comment")->group(function () {
    Route::get('/list/{id}', [CommentController::class, 'index']);
    Route::post('/add/{id}', [CommentController::class, 'store'])->middleware('auth:api');
    Route::post('/edit/{IdComment}', [CommentController::class, 'update'])->middleware('auth:api');
    Route::delete('/delete/{IdComment}', [CommentController::class, 'destroy'])->middleware('auth:api');
});


Route::prefix("company")->group(function () {

    Route::prefix("user")->group(function () {

        Route::get('/list', [CompanyController::class, 'index']);
        Route::get('/preview/{IdCompany}', [CompanyController::class, 'showUser']);
    });

    Route::prefix("admin")->middleware('auth:api')->group(function () {

        Route::post('/add', [CompanyController::class, 'store']);
        Route::get('/preview', [CompanyController::class, 'show']);
        Route::post('/edit', [CompanyController::class, 'update']);
        Route::get('/search', [CompanyController::class, 'search']);
    });
});


Route::prefix("service")->group(function () {

    Route::prefix("user")->group(function () {

        Route::get('/list/{IdSubCategory}', [ServiceController::class, 'index']);
        Route::get('/listOfferUser', [ServiceController::class, 'indexOfferUser']);
        Route::get('/preview/{id}', [ServiceController::class, 'show']);
        Route::get('/search', [ServiceController::class, 'search']);
        Route::post('/filter', [ServiceController::class, 'filter']);
        Route::get('/nameCompany', [ServiceController::class, 'indexCompany']);
        Route::get('/listBook/{Id}/{date}', [ServiceController::class, 'bookserv']);
    });
    Route::prefix("admin")->group(function () {

        Route::post('/add', [ServiceController::class, 'store'])->middleware('auth:api');
        Route::post('/edit/{id}', [ServiceController::class, 'update'])->middleware('auth:api');
        Route::delete('/delete/{id}', [ServiceController::class, 'destroy'])->middleware('auth:api');
        Route::post('/addOffer/{id}', [ServiceController::class, 'storeOffer']);
        Route::get('/preview/{id}', [ServiceController::class, 'show']);
        Route::post('/de/{string}', [ServiceController::class, 'deletimg']);
        Route::get('/listOfferAdmin', [ServiceController::class, 'indexOfferAdmin'])->middleware('auth:api');
    });
});


Route::prefix("order")->group(function () {

    Route::prefix("user")->group(function () {
        Route::get('/list', [OrderController::class, 'index'])->middleware('auth:api');;
        Route::post('/add/{id}', [OrderController::class, 'store'])->middleware('auth:api');
    });

    Route::prefix("admin")->group(function () {
        Route::post('/check/{orderId}', [OrderController::class, 'status']);
        Route::get('/listOrderPending', [OrderController::class, 'orderPending'])->middleware('auth:api');
        Route::get('/listOrderAccept', [OrderController::class, 'orderAccept'])->middleware('auth:api');
        Route::get('/listOrderExecute', [OrderController::class, 'orderExecute'])->middleware('auth:api');
    });

    Route::get('/preview/{IdOrder}', [OrderController::class, 'show']);
});

Route::prefix("rating")->group(function () {
    Route::post('/add/{id}', [RatingController::class, 'store'])->middleware('auth:api');
});


Route::prefix("category")->group(function () {
    Route::get('/preview/{IdCategory}', [SubCategoryController::class, 'show']);
    Route::prefix("admin")->middleware('auth:api')->group(function () {
     Route::get('/preview/{IdSubCategory}', [SubCategoryController::class, 'showinfo']);

    });
});
Route::prefix("favorite")->middleware('auth:api')->group(function () {

    Route::get('/list', [FavoraitController::class, 'index']);
    Route::post('/add/{id}', [FavoraitController::class, 'store']);
});

Route::prefix("like")->group(function () {
    Route::get('/list/{$IdEvent}', [LikeController::class, 'index']);
    Route::post('/add/{IdEvent}', [LikeController::class, 'store'])->middleware('auth:api');
});



Route::prefix("conversation")->middleware('auth:api')->group(function () {
    Route::post('/add/{ID}', [ConversationController::class, 'store']);
    //عرض رسائل محادثه وحده
    Route::get('/preview/{IdConversation}', [ConversationController::class, 'show']);
    Route::get('/listUser', [ConversationController::class, 'indexUser']);
    Route::get('/listCompany', [ConversationController::class, 'indexCompany']);
    Route::delete('/delete/{IdConversation}', [ConversationController::class, 'destroy']);
    Route::get('/convCust/{IdUser}', [ConversationController::class, 'indexCus']);
    Route::get('/convAd/{IdUser}', [ConversationController::class, 'indexAd']);
    Route::delete('/delete/{IdConversation}', [ConversationController::class, 'destroy']);
});


Route::prefix("message")->middleware('auth:api')->group(function () {
    Route::post('/add/{IdConversation}', [MessageController::class, 'store']);
    Route::post('/edit/{IdMessage}', [MessageController::class, 'update']);
    Route::delete('/delete/{IdMessage}', [MessageController::class, 'destroy']);
});
Route::prefix("dashboard")->group(function () {

    Route::get('/new', [DashboardController::class, 'index']);
    Route::get('/list', [DashboardController::class, 'indexAccept']);
    Route::get('/preview/{idAdmin}', [DashboardController::class, 'show']);
    Route::get('/delete/{idAdmin}', [DashboardController::class, 'destroy']);
    Route::post('/check/{IdAdmin}', [DashboardController::class, 'admission']);
});







Route::get('/j', function () {
    return 'welcome';
});
Route::get('/notification', [UserController::class, 'listNotifications'])->middleware('auth:api');
 Route::get('/noti/{id}', [UserController::class, 'sendNotificationrToUser']);
