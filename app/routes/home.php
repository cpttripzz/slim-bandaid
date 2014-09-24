<?php

$app->get('/home', function () use ($app) {
    $associationService = ZE\Bandaid\Factory\ServiceFactory::create($app->dbType, $app->db, 'Association');
    $bands = $associationService->getBandsWithVacancies();
    returnJson($bands);
});
