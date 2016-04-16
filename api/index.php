<?php

use eu\luige\plagiarism\endpoint\Documentation;
use eu\luige\plagiarism\endpoint\PlagiarismCheck;

require __DIR__ . '/bootstrap.php';

/**
 * @api {post} /plagiarism Add asynchronous job to queue
 * @apiVersion 1.0.0
 * @apiName Check
 * @apiGroup Plagiarism
 */
$app->post('/plagiarism/check/', PlagiarismCheck::class . ':enqueue');

$app->run();