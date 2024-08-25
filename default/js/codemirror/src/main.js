import {EditorView, ViewPlugin} from "@codemirror/view";
import {Compartment, EditorState} from "@codemirror/state";
import {basicSetup} from "codemirror";
import {html} from "@codemirror/lang-html";
import {javascript} from "@codemirror/lang-javascript";
import {StreamLanguage} from "@codemirror/language";
import {properties} from "@codemirror/legacy-modes/mode/properties"
import {theme} from "./theme.js";
import {config} from "./languages/config.js";
import {json} from "@codemirror/lang-json";

class CodeEditor {
    constructor() {
        this._code		= '';
        this._container	= this.getElement('#code-editor');
        this._textarea	= this.getElement('textarea#code-area');
        this._language	= new Compartment;
		
		if(this.exists('#editor-content')) {
			this._code	= this.getElement('#editor-content').innerHTML;
		}
		
        this.init();
    }
	
	exists(query) {
		if(typeof(query) === 'undefined' || query === null) {
			return false;
		}
		
		let search = (typeof(query) === 'string' ? document.querySelector(query) : query);
		
		return !(typeof(search) === 'undefined' || search === null);
	}
	
	getElement(query) {
		return document.querySelector(query);
	}

    getLanguage() {
		if(!this.exists(this._container)) {
			throw 'Container not exists.';
			return 'unknown';
		}
		
        if(typeof(this._container.dataset) === 'undefined') {
			throw 'Container has no DataSet.';
            return 'unknown';
        }

        if(typeof(this._container.dataset.language) === 'undefined') {
			throw 'Container has no language in DataSet.';
            return 'unknown';
        }

        return this._container.dataset.language;
    }

    init() {
		// Create the Editor instance
        this._state = EditorState.create({
            doc:		this._code,
            extensions:	[
                basicSetup,
				theme,
                EditorView.lineWrapping,
                this._language.of(this.findLanguage()),
                this.loadLanguage(),
                this.onChange()
            ],
        });

		// Create the Editor view
        this._view = new EditorView({
            state:	this._state,
            parent:	this._container
        });
    }

    onChange() {
		let _instance = this;
		
        return ViewPlugin.fromClass(class {
            constructor(view) {
				if(_instance.exists(_instance._textarea)) {
					_instance._textarea.innerHTML = view.state.doc.toString();
				}
            }

            update(update) {
                if(update.docChanged && _instance.exists(_instance._textarea)) {
                    _instance._textarea.innerHTML = update.view.state.doc.toString();
                }
            }

            destroy() {
                /* Do Nothing */
            }
        });
    }

    update() {
        this._view.dispatch({
            effects: this._language.reconfigure(this.findLanguage())
        });
    }

    findLanguage() {
        let language = null;

        switch(this.getLanguage()) {
            case 'html':
                language	= html();
            break;
            case 'javascript':
                language	= javascript();
            break;
            case 'json':
                language	= json();
			break;
            case 'config':
                language	= StreamLanguage.define(config);
            break;
            case 'properties':
                language	= StreamLanguage.define(properties);
            break;
            default:
                language	= html();
            break;
        }

        if(language === null) {
			throw 'No language found!';
            return null
        }
		
		console.info('language', language);

        return language;
    }

    loadLanguage() {
        return EditorState.transactionExtender.of(tr => {
            if(!tr.docChanged) {
                return null
            }

            return {
                effects: this._language.reconfigure(this.findLanguage())
            }
        });
    }
}

window.addEventListener('DOMContentLoaded', () => new CodeEditor);