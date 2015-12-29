'use strict';
import React from 'react';

var
  $ = jQuery
;

class EditComponent extends React.Component {
   constructor() {
    super();
    this._bind( [ 'editClick' ] );
    this.state = {
       hideEdit: false
    };

   }

   _bind( methods ) {
      methods.forEach( ( method ) => this[ method ] = this[ method ].bind( this ) );
   }

   componentDidMount() {
     let that = this;
     $( window ).on( 'toggleEdit', function( event ) {
        that.toggleEdit( event.editId );
     });
   }

   toggleEdit( editId ) {
      if ( this.props.editId === editId ) {
        this.setState({ hideEdit: !this.state.hideEdit });

        $( '#' + this.props.editId )
        .parent()
        .siblings()
        .toggle();
      }
   }

   editClick( event ) {
      event.preventDefault();
      $( window ).trigger({
        type: 'toggleEditor',
        editorId: this.props.editorId
      });
   }

    render() {
        return(
                <a href="javascript:void(0);" className={
                    this.props.tcpGlobals.tcp_css_button + ' ' +
                    this.props.tcpGlobals.tcp_css_edit_button + ' ' +
                    this.props.tcpGlobals.tcp_css_button_custom + ' ' +
                    this.props.tcpGlobals.tcp_css_edit_button_custom } id={ this.props.editId } onClick={ this.editClick } style={ this.state.hideEdit ? { display:'none' }:{ display:'inline-block' } }>Edit</a>
        );
    }
}

module.exports = EditComponent;
