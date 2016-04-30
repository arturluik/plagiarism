import Config from './../Config.jsx';
import $ from 'jquery';

export default class API {


    static getSupportedMimeTypes() {
        return API.get('/plagiarism/supportedmimetypes');
    }

    static getPlagiarismServices() {
        return API.get('/plagiarism/plagiarismservice');
    }

    static getPlagiarismService(id) {
        return API.get('/plagiarism/plagiarismservice/' + id)
    }

    static getResourceProvider(id) {
        return API.get('/plagiarism/resourceprovider/' + id)

    }

    static getResourceProviders() {
        return API.get('/plagiarism/resourceprovider');
    }

    static getChecks() {
        return API.get("/plagiarism/check");
    }

    static put(resource, data) {
        return API.apiWrapper('put', resource, data);
    }

    static get(resource, data) {
        return API.apiWrapper('get', resource, data);
    }

    static post(resource, data) {
        return API.apiWrapper('post', resource, data);
    }

    static apiWrapper(method, resource, data) {
        console.info(method + " " + resource + " data : " + JSON.stringify(data));
        return $.ajax({
            type: method,
            url: Config.API_ROOT + resource,
            data: data
        }).error(function (data) {
            console.error("Ajax call failed: " + data);
        });
    }
}
