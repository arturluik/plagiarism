import React from 'react';
import {Link} from 'react-router'

export default class CheckSuiteRow extends React.Component {
    
    render() {
        return (
            <li className="list-group-item">

                {this.props.checkSuite.name} &
                {this.props.checkSuite.created.date}
                <Link className="pull-right" to={`/checksuite/${this.props.checkSuite.id}`}>Vaata lähemalt</Link>
            </li>
        )
    }
} 