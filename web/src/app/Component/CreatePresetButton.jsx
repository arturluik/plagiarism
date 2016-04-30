import React from 'react';

import Button from 'react-bootstrap/lib/Button';
import Modal from 'react-bootstrap/lib/Modal';
import Tooltip from 'react-bootstrap/lib/Tooltip';

import Form from 'react-bootstrap/lib/Form';
import FormGroup from 'react-bootstrap/lib/FormGroup';
import Col from 'react-bootstrap/lib/Col';
import FormControl from 'react-bootstrap/lib/FormControl';
import Checkbox from 'react-bootstrap/lib/Checkbox';

import API from './../Service/Api.jsx';

import $ from 'jquery';

export default class CreatePresetButton extends React.Component {

    constructor(props, context) {
        super(props, context);

        this.state = {
            showModal: false,
            resourceProviders: [],
            plagiarismServices: [],
            supportedMimeTypes: [],
            resourceProviderSettings: []
        }
    };

    componentDidMount() {
        API.getPlagiarismServices().success(result => {
                this.updateState('plagiarismServices', result.content.map(service => {
                    return (
                        <Col sm={4} key={service}>
                            <Checkbox value={service}>{service}</Checkbox>
                        </Col>
                    );
                }));
            }
        );
        API.getSupportedMimeTypes().success(result => {
            this.updateState('supportedMimeTypes', result.content.map((mimeType, key)=> {
                return <option key={key}>{mimeType}</option>
            }));
        });
        API.getResourceProviders().success(result => {
            this.updateState('resourceProviders', result.content.map(resourceProvider => {
                return (
                    <Col sm={4} key={resourceProvider}>
                        <Checkbox value={resourceProvider}
                                  onChange={this.onResourceProviderSelect.bind(this)}>{resourceProvider}</Checkbox>
                    </Col>
                );
            }));
        });
    }

    savePreset() {


        console.log(this);
    }

    onResourceProviderSelect(element) {
        var checked = element.target.checked;
        var providerName = element.target.value;
        if (checked) {
            API.getResourceProvider(providerName).success(result => {
                    // make sure element is still checked
                    if (checked) {
                        var properties = result.content.payloadProperties.map(property => {
                            var content;
                            if (property.type == 'select') {
                                var options = Object.keys(property.values).map(function (value) {
                                    return <option key={value} value={value}>{property.values[value]}</option>
                                });
                                content = (
                                    <FormControl componentClass="select">
                                        {options}
                                    </FormControl>
                                );
                            } else if (property.type == 'textarea') {
                                content = <FormControl componentClass="textarea"/>
                            } else {
                                content = <FormControl type="text" name={property.name}></FormControl>
                            }
                            return (
                                <div key={property.name}>
                                    <Col sm={4}>
                                        {property.description}
                                    </Col>
                                    <Col sm={8}>
                                        {content}
                                    </Col>
                                </div>
                            );
                        });

                        this.setState(oldState => {
                            oldState.resourceProviderSettings.push(
                                <div key={result.content.name}>
                                    <h4>{result.content.name} seaded</h4>
                                    {properties}
                                </div>
                            );
                            return oldState;
                        });
                    }
                }
            )
        }
        else {
            this.setState(oldState => {
                oldState.resourceProviderSettings = oldState.resourceProviderSettings.filter(element => {
                    return providerName !== element.key;
                });

                return oldState;
            });
        }
    }

    openPopup() {
        this.setShowModal(true);
    }

    closePopup() {
        this.setShowModal(false);
    }

    setPlagiarismServices(services) {
        this.updateState('plagiarismServices', services);
    }

    setResourceProviders(resourceProviders) {
        this.updateState('resourceProviders', resourceProviders);
    }

    setShowModal(state) {
        this.updateState('showModal', state);
    }

    updateState(parameter, value) {
        this.setState(oldState => {
            oldState[parameter] = value;
            return oldState;
        });
    }


    render() {
        return (
            <div>
                <Button bsStyle="danger" bsSize="small" onClick={this.openPopup.bind(this)}>Lisa uus</Button>
                <Modal show={this.state.showModal} onHide={this.closePopup.bind(this)} backdrop={false}>
                    <Modal.Header closeButton>
                        <Modal.Title>Loo uus eeldefineeritud kontroll</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <Form horizontal>
                            <FormGroup controlId="preset-name">
                                <Col sm={4}>Kontrolli nimi</Col>
                                <Col sm={8}><FormControl type="text"/></Col>
                            </FormGroup>
                            <FormGroup controlId="preset-name">
                                <Col sm={4}>Tüüp</Col>
                                <Col sm={8}>
                                    <FormControl componentClass="select">
                                        {this.state.supportedMimeTypes}
                                    </FormControl>
                                </Col>
                            </FormGroup>
                            <FormGroup>
                                <Col sm={4}>Lubatud teenused</Col>
                                <Col sm={8}>
                                    {this.state.plagiarismServices}
                                </Col>
                            </FormGroup>
                            <FormGroup>
                                <Col sm={4}>Andmeallikad</Col>
                                <Col sm={8}>
                                    {this.state.resourceProviders}
                                </Col>
                                <Col sm={10} smOffset={2}>
                                    {this.state.resourceProviderSettings}
                                </Col>
                            </FormGroup>
                        </Form>
                    </Modal.Body>
                    <Modal.Footer>
                        <Button bsStyle="danger" onClick={this.closePopup.bind(this)}>Close</Button>
                        <Button bsStyle="success" onClick={this.savePreset.bind(this)}>Lisa</Button>
                    </Modal.Footer>
                </Modal>
            </div>
        );
    }
}
