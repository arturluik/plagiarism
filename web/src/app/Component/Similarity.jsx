import React from 'react';

import Navbar from './Navbar.jsx';
import Panel from  'react-bootstrap/lib/Panel';
import Col from 'react-bootstrap/lib/Col';

import API from './../Service/Api.jsx';
import Table from 'react-bootstrap/lib/Table';

import {PrismCode} from "react-prism";

export default class Similarity extends React.Component {

    constructor(props, context) {
        super(props, context);

        this.state = {
            'similarity': {},
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

                    var firstLines = [];
                    var secondLines = [];

                    result.similarResourceLines.forEach((line) => {
                        firstLines.push(line.firstResourceLineRange[0] + "-" + line.firstResourceLineRange[1])
                        secondLines.push(line.secondResourceLineRange[0] + "-" + line.secondResourceLineRange[1])
                    });

                    firstLines = firstLines.join(",");
                    secondLines = secondLines.join(",");

                    return (
                        <div key={"result" + result.id}>
                            <h4>{result.plagiarismService} tulemused</h4>
                            Sarnasus: {result.similarityPercentage} <br/>
                            <a onClick={this.showSimilarities.bind(this, firstLines, secondLines)}>Kuva sarnasused</a>
                            <Table>
                                <thead>
                                <tr>
                                    <th className="text-center">Esimese faili read</th>
                                    <th className="text-center">Teise faili read</th>
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

    showSimilarities(firstLines, secondLines) {
        this.state.similarity.firstContent += " ";
        this.state.similarity.secondContent += " ";
        this.setState({
            'firstLines': firstLines,
            'secondLines': secondLines,
            'similarity': this.state.similarity
        });
        this.forceUpdate();
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
                    <Panel bsStyle={"info"} header={this.state.similarity.firstFile}>
                        <pre className="line-numbers" data-line={this.state.firstLines}>
                            <PrismCode className="language-clike">
                                {this.state.similarity.firstContent}
                            </PrismCode>
                        </pre>
                    </Panel>
                </Col>
                <Col sm={6}>
                    <Panel bsStyle={"info"} header={this.state.similarity.secondFile}>
                        <pre className="line-numbers" data-line={this.state.secondLines}>
                            <PrismCode className="language-javascript">
                                {this.state.similarity.secondContent}
                            </PrismCode>
                        </pre>
                    </Panel>
                </Col>
            </div>
        );
    }
}

