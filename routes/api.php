<?php

$app->group(['namespace' => 'v1', 'prefix' => 'v1'], function () use ($app) {

    $app->post('predictions',  'PredictionController@store');

});
