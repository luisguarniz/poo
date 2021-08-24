<?php

use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\UserController;
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

//Rutas del Room
Route::post("Room/makeRoom",[RoomController::class, 'makeRoom'])->name('Room.makeRoom');
Route::put("Room/desactivateRoom",[RoomController::class, 'desactivateRoom'])->name('Room.desactivateRoom');
Route::get("Room/getRoomInvited/{roomCode}",[RoomController::class, 'getRoomInvited'])->name('Room.getRoomInvited');
Route::get("Room/getRoomhost/{roomCode}",[RoomController::class, 'getRoomhost'])->name('Room.getRoomhost');
Route::post("Room/makeStatus",[RoomController::class, 'makeStatus'])->name('Room.makeStatus');

Route::put("Room/changeStatusCartas",[RoomController::class, 'changeStatusCartas'])->name('Room.changeStatusCartas');
Route::get("Room/getStatusCartas/{roomCode}",[RoomController::class, 'getStatusCartas'])->name('Room.getStatusCartas');

Route::put("Room/changeStatusbtnVoting",[RoomController::class, 'changeStatusbtnVoting'])->name('Room.changeStatusbtnVoting');
Route::get("Room/getStatusbtnVoting/{roomCode}",[RoomController::class, 'getStatusbtnVoting'])->name('Room.getStatusbtnVoting');

Route::put("Room/changeStatusbtnStopVoting",[RoomController::class, 'changeStatusbtnStopVoting'])->name('Room.changeStatusbtnStopVoting');
Route::get("Room/getStatusbtnStopVoting/{roomCode}",[RoomController::class, 'getStatusbtnStopVoting'])->name('Room.getStatusbtnStopVoting');

Route::get("Room/makeRoomSolo/{idParticipantes}",[RoomController::class, 'makeRoomSolo'])->name('Room.makeRoomSolo');

//Rutas del Host
Route::get("User/makeUser",[UserController::class, 'makeUser'])->name('User.makeUser');
Route::get("User/isAdmin/{id}",[UserController::class, 'isAdmin'])->name('User.isAdmin');
Route::get("User/getUserTurn/{id}",[UserController::class, 'getUserTurn'])->name('User.getUserTurn');
Route::post("User/loginHost",[UserController::class, 'loginHost'])->name('api.auth.login');
Route::get("User/getAdmin/{roomID}",[UserController::class, 'getAdmin'])->name('User.getAdmin');
Route::get('User/me',[UserController::class, 'me'])
->name('UserController.me')
->middleware('auth:api');
Route::put("User/editNameUser",[UserController::class, 'editNameUser'])->name('user.editNameUser');

//Rutas del Invited
Route::get("User/makeInvited",[UserController::class, 'makeInvited'])->name('User.makeInvited');


//rutas para mensajes websocket
//ruta para desblokear las cartas de los participantes
Route::post('Message/messageWebsocket',[MessageController::class, 'messageWebsocket'])
->name('MessageController.messageWebsocket')
->middleware('auth:api');