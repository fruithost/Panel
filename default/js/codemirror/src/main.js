import {EditorView} from "@codemirror/view";
import {Compartment, EditorState} from "@codemirror/state";
import {basicSetup} from "codemirror";
import {html} from "@codemirror/lang-html";
import {javascript} from "@codemirror/lang-javascript";
import {StreamLanguage} from "@codemirror/language"
import {properties} from "@codemirror/legacy-modes/mode/properties"
import {myTheme} from "./theme.js";
import {config} from "./languages/config.js";
import {json} from "@codemirror/lang-json";

class CodeEditor {
    constructor() {
        this._code = document.querySelector('#editor-content').innerHTML;
        this._container = document.querySelector('#code-editor');
        this._language = new Compartment;
        this.init();
    }

    getLanguage() {
        if (typeof (this._container.dataset) === 'undefined') {
            return 'unknown';
        }

        if (typeof (this._container.dataset.language) === 'undefined') {
            return 'unknown';
        }

        return this._container.dataset.language;
    }

    init() {
        this._state = EditorState.create({
            doc: this._code,
            extensions: [
                basicSetup,
                myTheme,
                this._language.of(this.findLanguage()),
                this.loadLanguage()
            ],
        });

        this._view = new EditorView({
            state: this._state,
            parent: this._container
        });
    }

    update() {
        this._view.dispatch({
            effects: this._language.reconfigure(this.findLanguage())
        })
    }

    findLanguage() {
        let language = null;

        switch (this.getLanguage()) {
            case "html":
                language = html();
                break;
            case "javascript":
                language = javascript();
                break;
            case "json":
                language = json();
                break;
            case "config":
                language = StreamLanguage.define(config);
                break;
            case "properties":
                language = StreamLanguage.define(properties);
                break;
            default:
                language = html();
                break;
        }

        if (language === null) {
            return null
        }

        return language;
    }

    loadLanguage() {
        return EditorState.transactionExtender.of(tr => {
            if (!tr.docChanged) {
                return null
            }

            return {
                effects: this._language.reconfigure(this.findLanguage())
            }
        });
    }
}

window.addEventListener('DOMContentLoaded', () => new CodeEditor);