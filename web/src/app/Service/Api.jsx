import Config from './../Config.jsx';
import $ from 'jquery';

export default class API {

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
        console.info(method + " " + resource + "data : " + data);
        return $.ajax({
            type: method,
            url: Config.API_ROOT + resource,
            data: data
        }).error(function (data) {
            console.error("Ajax call failed: " + data);
        });
    }
}
