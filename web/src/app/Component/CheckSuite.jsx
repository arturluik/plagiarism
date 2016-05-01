import React from 'react';

import API from './../Service/Api.jsx';

import {browserHistory} from 'react-router'
import Navbar from './Navbar.jsx';
import Col from 'react-bootstrap/lib/Col';
import Panel from 'react-bootstrap/lib/Panel';
import ListGroup from 'react-bootstrap/lib/ListGroup';
import ListGroupItem from 'react-bootstrap/lib/ListGroupItem';
import Button from 'react-bootstrap/lib/Button';

export default class CheckSuite extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {
            checkSuite: {},
            similarities: []
        };
        API.getCheckSuite(this.props.params.id).success(data => {
            this.updateState('checkSuite', data.content);
            this.createSimilarityRows(data.content.similarities);
        }).error(data => {
            browserHistory.push('/404')
        });
    }

    createSimilarityRows(similarities) {
        this.updateState('similarities', similarities.map(similarity => {
            return (
                <ListGroupItem key={similarity.id}>
                    {similarity.firstResource} and {similarity.secondResource}
                    ({similarity.weight}%)
                    <Button bsStyle="success" bsSize="xs" className="pull-right">Vaata</Button>
                </ListGroupItem>
            )
        }));
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
                <Navbar/>
                <Col sm={4}>
                    <Panel bsStyle={"danger"} header={'Omadused'}>
                        <span>Andmeallikas: </span>
                        <div>Hello {this.props.params.id}</div>
                    </Panel>
                </Col>
                <Col sm={8}>
                    <Panel bsStyle={"danger"} header={'Eeldefineeritud kontrollid'}>
                        <ListGroup>
                            {this.state.similarities}
                        </ListGroup>
                    </Panel>
                </Col>
            </div>
        )
    }
}