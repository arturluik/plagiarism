import Config from './../Config.jsx';
import $ from 'jquery';

export default class API {


    static createPreset(serviceNames, resourceProviderNames, suiteName, resourceProviderPayloads, plagiarismServicePayloads) {
        return API.put('/plagiarism/preset',
            {
                'serviceNames': serviceNames.join(''),
                'resourceProviderNames': resourceProviderNames.join(','),
                'suiteName': suiteName,
                'resourceProviderPayloads': JSON.stringify(resourceProviderPayloads),
                'plagiarismServicePayloads': JSON.stringify(plagiarismServicePayloads)
            }
        );
    }

    static runPreset(id) {
        return API.post('/plagiarism/preset/' + id + '/run');
    }

    static getCheckSuite(id) {
        return API.get('/plagiarism/checksuite/' + id);
    }

    static updatePreset(id, serviceNames, resourceProviderNames, suiteName, resourceProviderPayloads, plagiarismServicePayloads) {
        return API.post('/plagiarism/preset/' + id, {
                'serviceNames': serviceNames.implode('.'),
                'resourceProviderNames': resourceProviderNames.implode(','),
                'suiteName': suiteName,
                'resourceProviderPayloads': JSON.stringify(resourceProviderPayloads),
                'plagiarismServicePayloads': JSON.stringify(plagiarismServicePayloads)
            }
        );
    }

    static getAllPresets(page) {
        if (!page) {
            page = 1;
        }
        return API.get('/plagiarism/preset', {'page': page});
    }

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

    static getCheckSuites(page) {
        return API.get("/plagiarism/checksuite", {'page': page});
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
            console.error("Ajax call failed: " + JSON.stringify(data));
        });
    }
}
