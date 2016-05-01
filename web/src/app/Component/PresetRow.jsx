import React from 'react';

import API from './../Service/Api.jsx';
import Button from 'react-bootstrap/lib/Button';

export default class PresetRow extends React.Component {

    run() {
        API.runPreset(this.props.preset.id).success(_ => {
            this.props.onNotify("success", this.props.preset.suiteName + " käivitatud");
            this.props.onSuccess();
        }).error(_ => {
            this.props.onNotify("error", this.props.preset.suiteName + " käivitamine ebaõnnestus");
        });
    }

    render() {
        return (
            <li className="list-group-item">
                {this.props.preset.suiteName}
                <Button className="pull-right" onClick={this.run.bind(this)} bsStyle="success"
                        bsSize="xs">Start</Button>
            </li>
        );
    }
}
