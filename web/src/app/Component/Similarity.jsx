import React from 'react';

import Navbar from './Navbar.jsx';
import Panel from  'react-bootstrap/lib/Panel';
import Col from 'react-bootstrap/lib/Col';

import API from './../Service/Api.jsx';
import Highlight from 'react-highlight';
import Table from 'react-bootstrap/lib/Table';

import 'react-highlight/'

export default class Similarity extends React.Component {

    constructor(props, context) {
        super(props, context);

        this.state = {
            'similarity': {}
        };

        API.getSimilarity(this.props.params.id).success(data => {
            this.setState({
                'similarity': data.content,
                'results': data.content.results.map(result => {
                    var similarFileLines = result.similarResourceLines.map(line => {
                        return (
                            <tr key={"line" + line.id}>
                                <td>{line.firstResourceLineRange[0]} - {line.firstResourceLineRange[1]}</td>
                                <td>{line.secondResourceLineRange[0]} - {line.secondResourceLineRange[1]}</td>
                            </tr>
                        );
                    });
                    return (
                        <div key={"result" + result.id}>
                            <h4>{result.plagiarismService} tulemused</h4>
                            Sarnasus: {result.similarityPercentage}
                            <Table>
                                <thead>
                                <tr>
                                    <th>Esimese faili read</th>
                                    <th>Teise faili read</th>
                                </tr>
                                </thead>
                                <tbody>
                                {similarFileLines}
                                </tbody>
                            </Table>
                        </div>
                    );
                })
            });
        });
    };

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
                <Col sm={12}>
                    <Panel bsStyle="primary">
                        <div className="text-center">
                            <Col sm={6} smOffset={3}>
                                {this.state.results}
                            </Col>
                        </div>
                    </Panel>
                </Col>
                <Col sm={6}>
                    <Panel bsStyle={"info"} header={'Esimene fail'}>
                        <Highlight>
                            {this.state.similarity.firstContent}
                        </Highlight>
                    </Panel>
                </Col>
                <Col sm={6}>
                    <Panel bsStyle={"info"} header={'Teine fail'}>
                        <Highlight>
                            {this.state.similarity.secondContent}
                        </Highlight>
                    </Panel>
                </Col>
            </div>
        );
    }
}

