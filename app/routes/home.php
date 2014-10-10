<?php

$app->get('/home', function () use ($app) {
    $associationService = ZE\Bandaid\Factory\ServiceFactory::create($app->dbType, $app->db, 'Association');
    $params = $app->request()->params();
    $lastId = empty($params['last_element']) ? null : $params['last_element'];
    $direction = empty($params['page_direction']) ? null : $params['page_direction'];
    $bands = $associationService->getBandsWithVacancies($lastId,$direction);
    returnJson($bands);
});
