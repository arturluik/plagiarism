<?php
namespace eu\luige\plagiarism\endpoint;

use eu\luige\plagiarism\datastructure\ApiResponse;
use Slim\Http\Request;
use Slim\Http\Response;

class ResourceProvider extends Endpoint {

    /**
     * @api {post} /plagiarism/resourceprovider Get all resourceproviders
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":["GIT-1.0","MockProvider-1.0","SFTP-1.0"]}
     */
    public function all(Request $request, Response $response) {
        $apiResponse = new ApiResponse();

        $result = [];

        $resourceProviders = \eu\luige\plagiarism\resourceprovider\ResourceProvider::getProviders();
        foreach ($resourceProviders as $resourceProviderClass) {
            /** @var \eu\luige\plagiarism\resourceprovider\ResourceProvider $instance */
            $instance = new $resourceProviderClass($this->container);
            $result[] = $instance->getName();
        }

        $apiResponse->setContent($result);
        return $this->response($response, $apiResponse);
    }

    /**
     * @api {post} /plagiarism/resourceprovider/:id Get detailed resourceprovider information
     * @apiGroup Plagiarism
     * @apiVersion 1.0.0
     * @apiParam {int} id Resouceprovider identificator
     * @apiSuccessExample {json} Success-Response:
     * {"error_code":0,"error_message":"","total_pages":1,"content":{"name":"GIT-1.0","description":"V\u00f5imaldab alla laadida materjali git repositoorumitest nii parooliga kui ka avaliku v\u00f5tmega","payloadProperties":[{"values":{"privateKey":"avalik v\u00f5ti","password":"parooliga","noAuth":"Puudub"},"type":"select","name":"authMethod","description":"Autentimismeetod","required":true,"longDescription":"Giti kasutajatuvastusmeetod, mille abil t\u00f5mmatakse alla repositooriumi sisu"},{"type":"textarea","name":"clone","description":"Repositooriumid","required":false,"longDescription":"Giti repositooriumite URId, kui neid on rohkem, siis eraldada komaga"},{"type":"text","name":"username","description":"Kasutajatunnus","required":false,"longDescription":"Kasutajatunnus giti autentimiseks"},{"type":"text","name":"password","description":"Parool","required":false,"longDescription":"Parool giti autentimiseks"},{"type":"textarea","name":"pubkey","description":"Avalik v\u00f5ti","required":false,"longDescription":"Avalik v\u00f5ti giti autentimiseks (kui parooli pole)"},{"type":"text","name":"directoryPattern","description":"Sisumuster","required":false,"longDescription":"Repositoorumi t\u00e4psema sisu filtreerimiseks: n\u00e4iteks \/*\/EX08 otsib k\u00f5ikdiest repositooriumi kasutades EX08 kasuta ja kasutab selle sisu"}]}}
     */
    public function get(Request $request, Response $response) {

        $this->assertAttributesExist($request, ['id']);

        $resourceProviders = \eu\luige\plagiarism\resourceprovider\ResourceProvider::getProviders();
        foreach ($resourceProviders as $resourceProviderClass) {
            /** @var \eu\luige\plagiarism\resourceprovider\ResourceProvider $instance */
            $instance = new $resourceProviderClass($this->container);


            if (mb_strtolower($instance->getName()) == mb_strtolower($request->getAttribute('id'))) {
                $apiResponse = new ApiResponse();

                $apiResponse->setContent([
                    'name' => $instance->getName(),
                    'description' => $instance->getDescription(),
                    'payloadProperties' => $instance->getPayloadProperties()
                ]);

                return $this->response($response, $apiResponse);
            }


        }

        throw new \Exception("No such resourceProvider: {$request->getAttribute('id')}");
    }
}