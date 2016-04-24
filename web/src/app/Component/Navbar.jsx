import React from 'react';
import Config from '../Config.jsx';
import GoogleLogin from 'react-google-login';

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
                        <a className="navbar-brand" href="#">Plagiarism</a>
                    </div>
                    <div className="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul className="nav navbar-nav">
                        </ul>
                        <ul className="nav navbar-nav navbar-right">
                            <li>
                                <GoogleLogin
                                    clientId={Config.GOOGLE_ID}
                                    redirectUri={Config.GOOGLE_REDIRECT_URI}
                                    buttonText="Login"
                                    callback={responseGoogle}/>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        );
    }
}