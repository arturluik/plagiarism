import React from 'react';
import {render}  from 'react-dom';
import {Router, Route, Link, browserHistory} from 'react-router';

import Index from './Component/Index.jsx';
import CheckSuite from './Component/CheckSuite.jsx';
import NotFound from './Component/NotFound.jsx';
import Similarity from './Component/Similarity.jsx';

import 'bootstrap/dist/css/bootstrap.min.css'
import './SCSS/style.scss';
import '../../../node_modules/prismjs/prism.js'
import '../../../node_modules/prismjs/themes/prism-coy.css'
import '../../../node_modules/prismjs/plugins/line-numbers/prism-line-numbers.min'
import '../../../node_modules/prismjs/plugins/line-numbers/prism-line-numbers.css'
import '../../../node_modules/prismjs/plugins/line-highlight/prism-line-highlight.css'
import '../../../node_modules/prismjs/plugins/line-highlight/prism-line-highlight.min'

render((
    <Router history={browserHistory}>
        <Route path="/" component={Index}/>
        <Route path="/checksuite/:id" component={CheckSuite}/>
        <Route path="/similarity/:id" component={Similarity}/>
        <Route path="/404" component={NotFound}/>
        <Route path="*" component={NotFound}/>
    </Router>
), document.getElementById("root-container"));
