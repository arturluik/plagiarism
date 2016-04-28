import React from 'react';
import Config from '../Config.jsx';
import GoogleLogin from 'react-google-login';

export default class Login extends React.Component {

    render() {
        <div>
            <Navbar/>
            <div className="container">
                <div className="row">
                    <div className="col-sm-6 col-sm-offset-3 index-login">
                        <img className="img img-responsive"
                             src={"http://www.campusreform.org/img/CROBlog/5841/Plag.jpg"}/>
                        <div>
                            Teretulemast <b>plagiaadituvastus testkeskkonda!</b> Funktsionaalsuse
                            ligipääsemiseks palun logige sisse oma Google'i kasutajaga.
                        </div>
                        <GoogleLogin
                            clientId={Config.GOOGLE_ID}
                            redirectUri={Config.GOOGLE_REDIRECT_URI}
                            buttonText="Logi sisse"
                            callback={this.loginResponse}/>
                    </div>
                </div>
            </div>
        </div>
    }
}
