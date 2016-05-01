import React from 'react';
import {render}  from 'react-dom';
import {Router, Route, Link, browserHistory} from 'react-router';

import Index from './Component/Index.jsx';
import CheckSuite from './Component/CheckSuite.jsx';
import NotFound from './Component/NotFound.jsx';

import 'bootstrap/dist/css/bootstrap.css';
import './Sass/style.scss';

render((
    <Router history={browserHistory}>
        <Route path="/" component={Index}/>
        <Route path="/checksuite/:id" component={CheckSuite}/>
        <Route path="/404" component={NotFound}/>
        <Route path="*" component={NotFound}/>
    </Router>
), document.getElementById("root-container"));
