import {EditorView} from "@codemirror/view"
import {HighlightStyle, syntaxHighlighting} from '@codemirror/language'
import {tags as t} from '@lezer/highlight'

const Colors = {
    Text: 'var(--bs-success-text-emphasis)',
    Key: 'var(--bs-code-color)',
    Braces: 'var(--bs-tertiary-color)',
    BracesActive: 'var(--bs-red)',
    SameSelection: 'rgb(from var(--bs-red) r g b / 50%)',
};

const base00 = '#2E3235',
    base01 = '#DDDDDD',
    base02 = '#B9D2FF',
    base03 = '#b0b0b0',
    base04 = '#d0d0d0',
    base05 = '#e0e0e0',
    base06 = '#808080',
    base07 = '#000000',
    base08 = '#A54543',
    base09 = '#fc6d24',
    base0A = '#fda331',
    base0B = '#8abeb7',
    base0D = '#6fb3d2',
    base0F = '#6987AF';
const invalid = base09,
    darkBackground = '#292d30',
    highlightBackground = base02 + '30',
    background = base00,
    tooltipBackground = base01,
    selection = '#202325',
    cursor = base01;

const Theme = EditorView.theme({
    '&': {
        color: base01,
        backgroundColor: background
    },

    '.cm-content': {
        caretColor: cursor
    },

    '.cm-cursor, .cm-dropCursor': {borderLeftColor: cursor},
    '&.cm-focused > .cm-scroller > .cm-selectionLayer .cm-selectionBackground, .cm-selectionBackground, .cm-content ::selection':
        {backgroundColor: selection},

    '.cm-activeLine': {backgroundColor: highlightBackground},
    '.cm-selectionMatch': {
        backgroundColor: Colors.SameSelection
    },

    '&.cm-focused .cm-matchingBracket, &.cm-focused .cm-matchingBracket *': {
        color: Colors.BracesActive,
        backgroundColor: 'transparent',
        fontWeight: 'bolder'
    },

    '.cm-gutters': {
        borderRight: `1px solid #ffffff10`,
        color: base06,
        backgroundColor: darkBackground
    },

    '.cm-activeLineGutter': {
        backgroundColor: highlightBackground
    },

    '.cm-foldPlaceholder': {
        backgroundColor: 'transparent',
        border: 'none',
        color: base02
    }
}, {
    dark: true
});

const Syntax = HighlightStyle.define([
    {tag: t.keyword, color: base0A},
    {
        tag: [t.name, t.deleted, t.character, t.propertyName, t.macroName],
        color: Colors.Key
    },
    {tag: [t.variableName], color: base0D},
    {tag: [t.function(t.variableName)], color: base0A},
    {tag: [t.labelName], color: base09},
    {
        tag: [t.color, t.constant(t.name), t.standard(t.name)],
        color: base0A
    },
    {tag: [t.definition(t.name), t.separator], color: Colors.Text},
    {tag: [t.brace], color: Colors.Braces},
    {
        tag: [t.annotation],
        color: invalid
    },
    {
        tag: [t.number, t.changed, t.annotation, t.modifier, t.self, t.namespace],
        color: base0A
    },
    {
        tag: [t.typeName, t.className],
        color: base0D
    },
    {
        tag: [t.operator, t.operatorKeyword],
        color: Colors.Text
    },
    {
        tag: [t.tagName],
        color: base0A
    },
    {
        tag: [t.squareBracket],
        color: Colors.Braces
    },
    {
        tag: [t.angleBracket],
        color: Colors.Braces
    },
    {
        tag: [t.attributeName],
        color: base0D
    },
    {
        tag: [t.regexp],
        color: base0A
    },
    {
        tag: [t.quote],
        color: base01
    },
    {tag: [t.string], color: Colors.Text},
    {
        tag: t.link,
        color: base0F,
        textDecoration: 'underline',
        textUnderlinePosition: 'under'
    },
    {
        tag: [t.url, t.escape, t.special(t.string)],
        color: base0B
    },
    {tag: [t.meta], color: base08},
    {tag: [t.comment], color: base06, fontStyle: 'italic'},
    {tag: t.monospace, color: base01},
    {tag: t.strong, fontWeight: 'bold', color: base0A},
    {tag: t.emphasis, fontStyle: 'italic', color: base0D},
    {tag: t.strikethrough, textDecoration: 'line-through'},
    {tag: t.heading, fontWeight: 'bold', color: base01},
    {tag: t.special(t.heading1), fontWeight: 'bold', color: base01},
    {tag: t.heading1, fontWeight: 'bold', color: base01},
    {
        tag: [t.heading2, t.heading3, t.heading4],
        fontWeight: 'bold',
        color: base01
    },
    {
        tag: [t.heading5, t.heading6],
        color: base01
    },
    {tag: [t.atom, t.bool, t.special(t.variableName)], color: base0B},
    {
        tag: [t.processingInstruction, t.inserted],
        color: base0B
    },
    {
        tag: [t.contentSeparator],
        color: base0D
    },
    {tag: t.invalid, color: base02, borderBottom: `1px dotted ${invalid}`}
]);

export const myTheme = [
    Theme,
    syntaxHighlighting(Syntax)
]