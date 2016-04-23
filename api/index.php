<?php

use eu\luige\plagiarism\endpoint\Check;

require __DIR__ . '/bootstrap.php';

/**
 * @api {post} /plagiarism/check Add asynchronous job to queue
 * @apiVersion 1.0.0
 * @apiGroup Plagiarism
 */
$app->post('/plagiarism/check', Check::class . ':enqueue');
/**
 * @api {get} /plagiarism/check Get all tasks ever executed
 * @apiVersion 1.0.0
 * @apiGroup Plagiarism
 */
$app->get('/plagiarism/check', Check::class . ':getChecks');


$app->run();