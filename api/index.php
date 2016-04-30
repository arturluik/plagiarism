<?php

use eu\luige\plagiarism\endpoint\Check;
use eu\luige\plagiarism\endpoint\PlagiarismService;
use eu\luige\plagiarism\endpoint\Preset;
use eu\luige\plagiarism\endpoint\ResourceProvider;

require __DIR__ . '/bootstrap.php';

/**
 * @api {put} /plagiarism/check Add asynchronous job to queue
 * @apiVersion 1.0.0
 * @apiGroup Plagiarism
 */
$app->put('/plagiarism/check', Check::class . ':enqueue');
/**
 * @api {get} /plagiarism/check Get all tasks ever executed
 * @apiVersion 1.0.0
 * @apiGroup Plagiarism
 */
$app->get('/plagiarism/check', Check::class . ':all');
/**
 * @api {get} /plagiarism/check/:id Get detailed check information
 * @apiVersion 1.0.0
 * @apiGroup Plagiarism
 * @apiParam {string} [check unique id]
 */
$app->get('/plagiarism/check/{id}', Check::class . ':get');


$app->get('/plagiarism/resourceprovider/{id}', ResourceProvider::class . ':get');
$app->get('/plagiarism/resourceprovider', ResourceProvider::class . ':all');

$app->get('/plagiarism/plagiarismservice', PlagiarismService::class . ':all');
$app->get('/plagiarism/plagiarismservice/{id}', PlagiarismService::class . ':get');


$app->put('/plagiarism/preset', Preset::class . ':create');
$app->get('/plagiarism/preset/{id}', Preset::class . ':read');
$app->post('/plagiarism/preset/{id}', Preset::class . ':update');
$app->delete('/plagiarism/preset/{id}', Preset::class . ':delete');
$app->get('/plagiarism/preset', Preset::class . ':all');

$app->run();