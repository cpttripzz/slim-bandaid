<?php

$app->get('/home', function () use ($app) {
    $associationService = ZE\Bandaid\Factory\ServiceFactory::create($app->dbType, $app->db, 'Association');
    $params = $app->request()->params();
    $lastId = empty($params['last_id']) ? null : $params['last_id'];
    $bands = $associationService->getBandsWithVacancies($lastId);
    returnJson($bands);
});
