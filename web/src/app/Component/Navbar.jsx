import React from 'react';
import {Link} from 'react-router'

const responseGoogle = (response) => {
    console.log(response);
};

export default class Navbar extends React.Component {

    render() {
        return (
            <nav className="navbar navbar-default">
                <div className="container-fluid">
                    <div className="navbar-header">
                        <button type="button" className="navbar-toggle collapsed" data-toggle="collapse"
                                data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span className="sr-only">Toggle navigation</span>
                            <span className="icon-bar"></span>
                            <span className="icon-bar"></span>
                            <span className="icon-bar"></span>
                        </button>
                        <Link className="navbar-brand" to="/">Plagiarism <span className="version">v1.0.0</span></Link>
                    </div>
                    <div className="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul className="nav navbar-nav">
                        </ul>
                        <ul className="nav navbar-nav navbar-right">
                        </ul>
                    </div>
                </div>
            </nav>
        );
    }
}