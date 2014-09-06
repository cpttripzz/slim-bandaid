<?php


    $app->get('/bands', function () use ($app) {
        $bands = $app->db->select('SELECT * FROM association WHERE `type` = "band"');
        $app->response()->header('Content-Type', 'application/json');
        echo json_encode($bands);
    });
