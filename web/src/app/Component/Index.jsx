import React from 'react';
import Navbar from './Navbar.jsx';
import Auth from './../Service/Auth.jsx';

import API from './../Service/Api.jsx';
import Col from 'react-bootstrap/lib/Col';
import Panel from 'react-bootstrap/lib/Panel';

import CreatePresetButton from './CreatePresetButton.jsx';
import CheckSuiteRow from './CheckSuiteRow.jsx';

export default class Index extends React.Component {


    constructor(props, context) {
        super(props, context);

        this.state = {
            checksSuites: [],
            presets: []
        };
    };

    componentDidMount() {
        this.datasetChanged();

    }

    datasetChanged() {
        API.getCheckSuites().success(data => {
            this.updateState('checksSuites', data.content.map((e => <CheckSuiteRow checkSuite={e} key={e.id}/>)));
        });
        API.getAllPresets().success(data => {
            this.updateState('presets', data.content.map((e, i) => <Preset key={i} name={e.suiteName}></Preset>));
        });
    }

    updateState(parameter, value) {
        this.setState(oldState => {
            oldState[parameter] = value;
            return oldState;
        });
    }

    render() {

        return (
            <div className="container-fluid">
                <div className="row index">
                    <Navbar/>
                    <Col sm={6}>
                        <Panel bsStyle={"danger"} header={'Eeldefineeritud kontrollid'}>
                            {this.state.presets}
                            <span class="pull-right">
                                <CreatePresetButton/>
                            </span>
                        </Panel>
                    </Col>
                    <CheckSection checkRows={this.state.checksSuites}/>
                </div>
            </div>
        );
    }
}

class Preset extends React.Component {
    render() {
        return (
            <li className="list-group-item">
                {this.props.name}
                <a className="btn-xs btn-success pull-right">Start</a>
            </li>
        );
    }
}

class CheckSection extends React.Component {
    render() {
        return (
            <div className="col-sm-6">
                <div className="panel panel-danger">
                    <div className="panel-heading"><b>Tulemused</b></div>
                    <div className="panel-body">
                        <ul className="list-group">
                            {this.props.checkRows}
                        </ul>
                    </div>
                </div>
            </div>
        );
    }
}

class Setting extends React.Component {
    render() {
        return (
            <div className="col-sm-6">
                <div className="input-group">
                    <span className="input-group-addon">Threshold</span>
                    <input type="text" className="form-control"/>
                </div>
            </div>
        )
    }
}

