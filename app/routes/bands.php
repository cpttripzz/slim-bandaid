<?php
$app->get('/band', function () use ($app) {
    $associationService = ZE\Bandaid\Factory\ServiceFactory::create($app->dbType, $app->db, 'Association');
    $params = $app->request()->params();

    $bands = $associationService->getBandsWithVacancies($lastId,$direction);
    returnJson($bands);
});
