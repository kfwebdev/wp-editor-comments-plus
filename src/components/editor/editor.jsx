'use strict';
import React from 'react';
import FBEmitter from 'fbemitter';

var emitter = tcp.emitter;

class EditorComponent extends React.Component {
   constructor() {
      super();
      this._bind( [ 'componentDidMount', 'toggleEditor', 'cancelEditor', 'initTinyMCE' ] );
      this.state = {
         showEditor: false,
         tinyMCEcontent: ''
      };
   }

   _bind( methods ) {
      methods.forEach( (method) => this[method] = this[method].bind(this) );
   }

   componentDidMount() {
      let that = this,
          content = document.getElementById( this.props.tcpGlobals.tcp_css_comment_content + this.props.commentId ).innerHTML;
      this.setState({ tinyMCEcontent: content });
      emitter.addListener( 'toggleEditor', function( editorId ) {
         that.toggleEditor( editorId );
      });
   }

  toggleEditor( editorId ) {
      if ( this.props.tcpGlobals.tcp_css_editor + this.props.commentId === editorId ) {
         this.setState({ showEditor: !this.state.showEditor });

         // if showEditor was false (before setState above)
         if ( !this.state.showEditor ) {
           this.initTinyMCE();
         }
      }
  }

  cancelEditor() {
    this.setState({ showEditor: false });
    this.removeTinyMCE();
  }

  initTinyMCE() {
    let that = this;
    tinymce.init({
        menubar: false,
        selector: "textarea.tinyMCEeditor",
        setup : function(editor) {
          editor.on('change', function(e) {
            that.setState({ tinyMCEcontent: editor.getContent()});
          });
        },
        plugins: [
          "charmap, colorpicker, fullscreen, lists, paste, tabfocus, textcolor, wordpress, wpdialogs, wpemoji, wplink, wpview"
        ],
        toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
    });
    // update tinyMCE content
    tinyMCE.get( this.props.tcpGlobals.tcp_css_editor + this.props.commentId ).setContent( this.state.tinyMCEcontent );
  }

  removeTinyMCE() {
    tinymce.remove( '#' + this.props.tcpGlobals.tcp_css_editor + this.props.commentId );
  }

  render() {
      return(
        <div className={ tcpGlobals.editor } style={ this.state.showEditor ? { display:'inline-block' }:{ display:'none' }}>
          <textarea id={ this.props.tcpGlobals.tcp_css_editor + this.props.commentId } className="tinyMCEeditor" rows="8"></textarea>
          <div className="reply tcp-reply-container">
            <span className="spinner"></span><a href="javascript:void(0);" className={ this.props.tcpGlobals.tcp_css_button + ' ' + this.props.tcpGlobals.tcp_css_submit_edit_button }>Submit</a>
            <a href="javascript:void(0);" onClick={ this.cancelEditor } className={ this.props.tcpGlobals.tcp_css_button + ' ' + this.props.tcpGlobals.tcp_css_cancel_edit_button }>Cancel</a>
          </div>
        </div>
      );
  }

  componentWillUnmount() {
    if ( tinyMCE.get( this.props.editorId ) ) {
      this.removeTinyMCE();
    }
  }
}

// EditorComponent.defaultProps = { showEditor: false };

module.exports = EditorComponent;
