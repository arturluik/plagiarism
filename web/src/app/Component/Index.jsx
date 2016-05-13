import React from 'react';
import Navbar from './Navbar.jsx';

import API from './../Service/Api.jsx';
import Col from 'react-bootstrap/lib/Col';
import Panel from 'react-bootstrap/lib/Panel';

import {Link} from 'react-router'
import CreatePresetButton from './CreatePresetButton.jsx';
import CheckSuiteRow from './CheckSuiteRow.jsx';
import PresetRow from './PresetRow.jsx';

import NotificationSystem from 'react-notification-system';
import AdvancedPagination from './AdvancedPagination.jsx';

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
        this.updateCheckSuites(1);
        this.updatePresets(1);

    }

    updateCheckSuites(page) {
        API.getCheckSuites(page).success(data => {
            this.updateState('checksSuites', data.content.map((e => {
                return <CheckSuiteRow checkSuite={e} key={e.id} onNotify={this.notify.bind(this)}/>
            })));
        });
    }

    updatePresets(page) {
        API.getAllPresets(page).success(data => {
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

    onCheckSuitePageChange(page) {
        this.updateState('currentCheckSuitePage', page);
        this.updateCheckSuites(page);
    }

    onPresetPageChange(page) {
        this.updateState('currentPresetPage', page);
        this.updatePresets(page);
    }

    render() {

        var style = {
            NotificationItem: {
                DefaultStyle: {
                    margin: '0px'
                }
            }
        };

        var presetHeader = (
            <div>
                Eeldefineeritud kontrollid
                <CreatePresetButton onNotify={this.notify.bind(this)}
                                    onSuccess={this.datasetChanged.bind(this)}/>
            </div>
        );

        return (
            <div className="container-fluid">
                <div className="row index">
                    <Navbar/>
                    <NotificationSystem ref="notificationSystem" style={style}/>
                    <Col sm={6}>
                        <Panel bsStyle={"info"} header={presetHeader}>
                            {this.state.presets}
                            <span class="pull-right">
                            <AdvancedPagination onPageChange={this.onPresetPageChange.bind(this)}/>
                            </span>
                        </Panel>
                    </Col>
                    <Col sm={6}>
                        <Panel bsStyle="info" header="Tulemused">
                            {this.state.checksSuites}
                            <AdvancedPagination onPageChange={this.onCheckSuitePageChange.bind(this)}/>
                        </Panel>
                    </Col>
                </div>
            </div>
        );
    }
}
