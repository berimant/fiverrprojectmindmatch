<?php
/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');
$router->get('/interests', 'InterestController@getInterests');
$router->get('/all_interests', 'InterestController@getAllInterests');
$router->post('/interests', 'InterestController@addInterest');
$router->delete('/interests', 'InterestController@deleteInterest');
$router->delete('/interests/{id}', 'InterestController@deleteInterestFromTable');
$router->post('/user_interests', 'InterestController@addUserInterest');
$router->post('/match', 'InterestController@match');
$router->get('/chat/{match_id}', 'ChatController@getChat');
$router->post('/chat/skip', 'ChatController@skipChat');
$router->post('/chat', 'ChatController@sendMessage');
$router->post('/friends', 'ChatController@addFriend');
$router->delete('/chat/{match_id}', 'ChatController@deleteChat');
$router->delete('/chat/{match_id}', 'ChatController@deleteChat');
$router->get('/friends/{user_id}', 'FriendController@getFriends');
//$router->post('/friends', 'FriendController@addFriend');
$router->get('/private_chat/{user_id}/{friend_id}', 'FriendController@getPrivateChat');
$router->post('/private_chat', 'FriendController@sendPrivateMessage');
$router->delete('/private_chat', 'FriendController@clearPrivateChat');
$router->get('/check_active_match/{user_id}', 'ChatController@checkActiveMatch');
// Route baru untuk memperbarui status online
$router->post('/users/online-status', 'InterestController@updateUserOnlineStatus');

