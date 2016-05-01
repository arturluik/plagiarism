import React from 'react';

import Pagination from 'react-bootstrap/lib/Pagination';

export default class PaginationAdvanced extends React.Component {

    constructor(props, context) {
        super(props, context);
        this.state = {
            activePage: 1
        };
    }

    handleSelect(eventKey) {
        console.log(eventKey);
        this.props.onPageChange(eventKey);
        this.setState({
            activePage: eventKey
        });
    }

    render() {
        return (
            <Pagination
                prev
                next
                ellipsis
                boundaryLinks
                items={10}
                maxButtons={0}
                activePage={this.state.activePage}
                onSelect={this.handleSelect.bind(this)}/>
        );
    }
}
