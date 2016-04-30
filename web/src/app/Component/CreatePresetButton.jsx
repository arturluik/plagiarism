import React from 'react';

import Button from 'react-bootstrap/lib/Button';
import Modal from 'react-bootstrap/lib/Modal';
import Tooltip from 'react-bootstrap/lib/Tooltip';

import Form from 'react-bootstrap/lib/Form';
import FormGroup from 'react-bootstrap/lib/FormGroup';
import Col from 'react-bootstrap/lib/Col';
import FormControl from 'react-bootstrap/lib/FormControl';
import Checkbox from 'react-bootstrap/lib/Checkbox';


export default class CreatePresetButton extends React.Component {

    constructor(props, context) {
        super(props, context);

        this.state = {showModal: false};
    };

    close() {
        this.setState({showModal: false});
    }

    open() {
        this.setState({showModal: true});
    }

    render() {
        return (
            <div>
                <Button bsStyle="danger" bsSize="small" onClick={this.open.bind(this)}>Lisa uus</Button>
                <Modal show={this.state.showModal} onHide={this.close.bind(this)} backdrop={false}>
                    <Modal.Header closeButton>
                        <Modal.Title>Loo uus eeldefineeritud kontroll</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <Form horizontal>
                            <FormGroup controlId="preset-name">
                                <Col sm={4}>Kontrolli nimi</Col>
                                <Col sm={8}><FormControl type="text"/></Col>
                            </FormGroup>
                            <FormGroup>
                                <Col sm={4}>Lubatud teenused</Col>
                                <Col sm={8}>
                                    <Checkbox>Moss-1.0</Checkbox>
                                </Col>
                            </FormGroup>
                            <FormGroup>
                                <Col sm={4}>Andmeallikas</Col>
                                <Col sm={8}>
                                    <FormControl componentClass="select">
                                        <option value="select">MockProvider-1.0</option>
                                        <option value="other">Git-1.0</option>
                                    </FormControl>
                                </Col>
                                <Col sm={8} smOffset={4}>
                                    <h4>Andmeallika seaded</h4>
                                    <FormControl type="text" placeholder="nimetus"></FormControl>
                                    <FormControl componentClass="textarea" placeholder="ssh võti"/>
                                </Col>
                            </FormGroup>
                        </Form>
                    </Modal.Body>
                    <Modal.Footer>
                        <Button onClick={this.close.bind(this)}>Close</Button>
                    </Modal.Footer>
                </Modal>
            </div>
        );
    }
}

