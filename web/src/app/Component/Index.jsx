import React from 'react';
import Navbar from './Navbar.jsx';
import Auth from './../Service/Auth.jsx';

import API from './../Service/Api.jsx';


import CreatePresetButton from './CreatePresetButton.jsx';

export default class Index extends React.Component {


    constructor(props, context) {
        super(props, context);

        this.state = {
            checks: []
        };
    };

    componentDidMount() {
        API.getChecks().success(data => {
            this.setState({checks: data.content.map((e => <CheckRow check={e} key={e.messageId}/>))});
        });
    }

    render() {

        return (
            <div className="container-fluid">
                <div className="row index">
                    <CreatePresetButton/>
                    <SettingsSection/>
                    <CheckSection checkRows={this.state.checks}/>
                </div>
            </div>
        );
    }
}

class SettingsSection extends React.Component {
    render() {
        return (
            <div className="col-sm-6">
                <div className="panel panel-danger">
                    <div className="panel-heading"><b>Plagiaadikontrolli seaded</b></div>
                    <div className="panel-body">
                        <b>Globaalsed seaded</b>
                        <hr/>
                        <div className="row">
                            <Setting/>
                            <Setting/>
                        </div>
                        <b>Eeldefinieeritud kontrollid</b>
                        <hr/>
                        <ul className="list-group">
                            <PredefinedCheck/>
                            <PredefinedCheck/>
                        </ul>
                        <a className="btn btn-danger pull-right">Lisa uus</a>
                    </div>
                    <hr/>
                </div>
            </div>
        );
    }
}

class PredefinedCheck extends React.Component {
    render() {
        return (
            <li className="list-group-item">
                ITI0011 EX08
                <a className="btn-xs btn-success pull-right">Start</a>
                &nbsp;
                <a className="btn-xs btn-danger pull-right">Kustuta</a>
                &nbsp;
                <a className="btn-xs btn-primary pull-right">Settings</a>
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

class CheckRow extends React.Component {

    render() {
        return (
            <li className="list-group-item">
                {this.props.check.name} &
                {this.props.check.providerName} &
                {this.props.check.serviceName}
                <a className="btn-xs btn-primary pull-right">watch</a>
            </li>
        )
    }
} 