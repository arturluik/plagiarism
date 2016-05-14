import React from 'react';
import {Link} from 'react-router'

export default class CheckSuiteRow extends React.Component {
    
    render() {
        return (
            <li className="list-group-item">

                <b>{this.props.checkSuite.name}</b> 
                ({this.props.checkSuite.created.date.replace(".000000", "")})
                <Link className="pull-right" to={`/checksuite/${this.props.checkSuite.id}`}>Vaata lähemalt</Link>
            </li>
        )
    }
} 