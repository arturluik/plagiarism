import React from 'react';

import Pagination from 'react-bootstrap/lib/Pagination';
import Pager from 'react-bootstrap/lib/Pager';
import PageItem from 'react-bootstrap/lib/PageItem';

export default class PaginationAdvanced extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {
            activePage: 1
        };
    }

    handleSelect(direction) {

        
        var newPage = Math.max(1, this.state.activePage + direction);

        this.setState({
            activePage: newPage
        });

        this.props.onPageChange(newPage);
        this.setState({
            activePage: newPage
        });
    }

    render() {
        return (
            <Pager>
                <PageItem previous href="#" onSelect={this.handleSelect.bind(this, -1)}>&larr; Eelmine lehekülg</PageItem>
                <PageItem next href="#" onSelect={this.handleSelect.bind(this, 1)}>Järgmine lehekülg&rarr;</PageItem>
            </Pager>
        );
    }
}
