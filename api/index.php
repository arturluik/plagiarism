<?php

use eu\luige\plagiarism\endpoint\Check;

require __DIR__ . '/bootstrap.php';

/**
 * @api {post} /plagiarism/check Add asynchronous job to queue
 * @apiVersion 1.0.0
 * @apiName Check
 * @apiGroup Plagiarism
 */
$app->post('/plagiarism/check', Check::class . ':enqueue');

$app->run();