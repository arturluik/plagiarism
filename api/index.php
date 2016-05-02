<?php

use eu\luige\plagiarism\endpoint\Check;
use eu\luige\plagiarism\endpoint\CheckSuite;
use eu\luige\plagiarism\endpoint\PlagiarismService;
use eu\luige\plagiarism\endpoint\Preset;
use eu\luige\plagiarism\endpoint\ResourceProvider;
use eu\luige\plagiarism\endpoint\Similarity;

require __DIR__ . '/bootstrap.php';


/**
 * @api {get} /plagiarism/check/:id Get detailed check information
 * @apiVersion 1.0.0
 * @apiGroup Plagiarism
 * @apiParam {string} [check unique id]
 */
$app->get('/plagiarism/check/{id}', Check::class . ':get');

$app->get('/plagiarism/checksuite/{id}', CheckSuite::class . ':get');
$app->get('/plagiarism/checksuite', CheckSuite::class . ':all');
$app->put('/plagiarism/checksuite', CheckSuite::class . ':create');

$app->get('/plagiarism/resourceprovider/{id}', ResourceProvider::class . ':get');
$app->get('/plagiarism/resourceprovider', ResourceProvider::class . ':all');

$app->get('/plagiarism/plagiarismservice', PlagiarismService::class . ':all');
$app->get('/plagiarism/plagiarismservice/{id}', PlagiarismService::class . ':get');
$app->get('/plagiarism/supportedmimetypes', PlagiarismService::class . ':getSupportedTypes');

$app->put('/plagiarism/preset', Preset::class . ':create');
$app->get('/plagiarism/preset/{id}', Preset::class . ':read');
$app->post('/plagiarism/preset/{id}', Preset::class . ':update');
$app->delete('/plagiarism/preset/{id}', Preset::class . ':delete');
$app->get('/plagiarism/preset', Preset::class . ':all');
$app->post('/plagiarism/preset/{id}/run', Preset::class . ':run');

$app->get('/plagiarism/similarity/{id}', Similarity::class . ':get');
$app->run();