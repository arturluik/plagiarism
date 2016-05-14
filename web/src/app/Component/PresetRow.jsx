import React from 'react';

import API from './../Service/Api.jsx';
import Button from 'react-bootstrap/lib/Button';
import Tooltip from 'react-bootstrap/lib/Tooltip';
import OverlayTrigger from 'react-bootstrap/lib/OverlayTrigger'

export default class PresetRow extends React.Component {

    run() {
        API.runPreset(this.props.preset.id).success(_ => {
            if (_.error_code == 304) {
                this.props.onNotify("warning", this.props.preset.suiteName + " on hiljuti juba kävitatud");
            } else {
                this.props.onNotify("success", this.props.preset.suiteName + " käivitatud");
            }
            this.props.onSuccess();
        }).error(_ => {
            this.props.onNotify("error", this.props.preset.suiteName + " käivitamine ebaõnnestus");
        });
    }


    render() {
        
        const tooltip = (
            <Tooltip id={this.props.preset.id}>{JSON.stringify(this.props.preset)}</Tooltip>
        );

        return (
            <li className="list-group-item">
                {this.props.preset.suiteName}

                <OverlayTrigger placement="bottom" overlay={tooltip}>
                    <Button className="pull-right" bsStyle="info" bsSize="xs">Info</Button>
                </OverlayTrigger>

                <Button className="pull-right" onClick={this.run.bind(this)} bsStyle="default"
                        bsSize="xs">Start</Button>
            </li>
        );
    }
}
