import React from 'react';
import Navbar from './Navbar.jsx';
import Auth from './../Service/Auth.jsx';

import API from './../Service/Api.jsx';
import Col from 'react-bootstrap/lib/Col';
import Panel from 'react-bootstrap/lib/Panel';

import CreatePresetButton from './CreatePresetButton.jsx';
import CheckSuiteRow from './CheckSuiteRow.jsx';
import PresetRow from './PresetRow.jsx';

import NotificationSystem from 'react-notification-system';

export default class Index extends React.Component {


    constructor(props, context) {
        super(props, context);

        this.state = {
            checksSuites: [],
            presets: []
        };
    };

    notify(level, message) {
        this._notificationSystem.addNotification({
            message: message,
            level: level,
            autoDismiss: 2,
            position: 'tc'
        });
    }

    componentDidMount() {
        this.datasetChanged();
        this._notificationSystem = this.refs.notificationSystem;
    }

    datasetChanged() {
        console.info('Dataset changed');
        API.getCheckSuites().success(data => {
            this.updateState('checksSuites', data.content.map((e => {
                return <CheckSuiteRow checkSuite={e} key={e.id} onNotify={this.notify.bind(this)}/>
            })));
        });
        API.getAllPresets().success(data => {
            this.updateState('presets', data.content.map((e, i) => {
                return <PresetRow key={i} preset={e} onNotify={this.notify.bind(this)}
                                  onSuccess={this.datasetChanged.bind(this)}></PresetRow>
            }));
        });
    }

    updateState(parameter, value) {
        this.setState(oldState => {
            oldState[parameter] = value;
            return oldState;
        });
    }

    render() {

        var style = {
            NotificationItem: {
                DefaultStyle: {
                    margin: '0px'
                }
            }
        };

        return (
            <div className="container-fluid">
                <div className="row index">
                    <Navbar/>
                    <NotificationSystem ref="notificationSystem" style={style}/>
                    <Col sm={6}>
                        <Panel bsStyle={"danger"} header={'Eeldefineeritud kontrollid'}>
                            {this.state.presets}
                            <span class="pull-right">
                                <CreatePresetButton onNotify={this.notify.bind(this)}
                                                    onSuccess={this.datasetChanged.bind(this)}/>
                            </span>
                        </Panel>
                    </Col>
                    <Col sm={6}>
                        <Panel bsStyle="danger" header="Tulemused">
                            {this.state.checksSuites}
                        </Panel>
                    </Col>
                </div>
            </div>
        );
    }
}
