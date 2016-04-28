import React from 'react';
import {render}  from 'react-dom';
import {Router, Route, Link, browserHistory} from 'react-router';

import Index from './Component/Index.jsx';

import 'bootstrap/dist/css/bootstrap.css';
import './Sass/style.scss';

render((
    <Router history={browserHistory}>
        <Route path="/web" component={Index}>
        </Route>
    </Router>
), document.getElementById("root-container"));
