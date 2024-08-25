import { EditorView } from '@codemirror/view';
import { HighlightStyle, syntaxHighlighting as SyntaxHighlighting, defaultHighlightStyle as Default } from '@codemirror/language';
import { tags as Tags } from '@lezer/highlight';

/* Color Table */
const Colors = {
	/* Defaults */
	Background:		'var(--bs-body-bg)',
	Foreground:		'var(--bs-body-color)',
	Transparent:	'transparent',
	
	/* Selections */
	Active: {
		Cursor:		'var(--bs-body-color)',
		Selection:	'rgb(from var(--bs-body-color) r g b / 20%)',
		Line:		'rgb(from var(--bs-body-color) r g b / 20%)',
		Matches:	'rgb(from var(--bs-red) r g b / 50%)',
		Braces:		'var(--bs-red)'
	},
	
	/* Code */
	Code: {
		Braces:		'var(--bs-tertiary-color)',
		Numbers:	'var(--bs-warning-text-emphasis)',
		Variables:	'var(--bs-success-text-emphasis)',
		Values:		'var(--bs-code-color)',
		Groups:		'var(--bs-primary-text-emphasis)'
	}
};

// @ToDo Replace
const base00 = '#2E3235';
const base01 = '#DDDDDD';
const base02 = '#B9D2FF';
const base03 = '#b0b0b0';
const base04 = '#d0d0d0';
const base05 = '#e0e0e0';
const base06 = '#808080';
const base07 = '#000000';
const base08 = '#A54543';
const base09 = '#fc6d24';
const base0A = '#fda331';
const base0D = '#6fb3d2';
const base0F = '#6987AF';
const invalid = base09;

/* Theme Colors */
const Theme = EditorView.theme({
    '&': {
        color:				Colors.Foreground,
        backgroundColor:	Colors.Background
    },
    '.cm-content': {
        caretColor:			Colors.Active.Cursor
    },
    '.cm-cursor, .cm-dropCursor': {
		borderLeftColor:	Colors.Active.Cursor
	},
    '&.cm-focused > .cm-scroller > .cm-selectionLayer .cm-selectionBackground, .cm-selectionBackground, .cm-content ::selection': {
		backgroundColor:	Colors.Active.Selection
	},
    '.cm-activeLine': {
		backgroundColor:	Colors.Active.Line
	},
    '.cm-selectionMatch': {
        backgroundColor:	Colors.Active.Matches
    },
    '&.cm-focused .cm-matchingBracket, &.cm-focused .cm-matchingBracket *': {
        color:				Colors.Active.Braces,
        backgroundColor:	Colors.Transparent,
        fontWeight:			'bolder'
    },
    '.cm-gutters': {
        borderRight:		'1px solid #ffffff10',
        color:				Colors.Foreground,
        backgroundColor:	Colors.Background
    },
    '.cm-activeLineGutter': {
        backgroundColor:	Colors.Active.Line
    },

    '.cm-foldPlaceholder': {
        backgroundColor:	Colors.Transparent,
        border:				'none',
        color:				base02
    }
}, {
    dark: true
});

/* Syntax Colors */
const Syntax = HighlightStyle.define([{
	tag:	Tags.keyword,
	color:	base0A
}, {
	tag:	[ Tags.name, Tags.deleted, Tags.character, Tags.propertyName, Tags.macroName ],
	color:	Colors.Code.Variables
}, {
	tag:	[ Tags.variableName ],
	color:	base0D
}, {
	tag:	[ Tags.function(Tags.variableName) ],
	color:	base0A
}, {
	tag:	[ Tags.labelName ],
	color:	base09
}, {
	tag:	[ Tags.color, Tags.constant(Tags.name), Tags.standard(Tags.name) ],
	color:	base0A
}, {
	tag:	[ Tags.definition(Tags.name), Tags.separator ],
	color:	Colors.Code.Values
}, {
	tag:	[ Tags.brace ],
	color:	Colors.Code.Braces
}, {
	tag:	[ Tags.annotation ],
	color:	invalid
}, {
	tag:	[ Tags.number ],
	color:	Colors.Code.Numbers
}, {
	tag:	[ Tags.changed, Tags.annotation, Tags.modifier, Tags.self, Tags.namespace ],
	color:	base0D
}, {
	tag:	[ Tags.typeName, Tags.className ],
	color:	base0D
}, {
	tag:	[ Tags.operator, Tags.operatorKeyword ],
	color:	Colors.Code.Values
}, {
	tag:	[ Tags.tagName ],
	color:	base0A
}, {
	tag:	[ Tags.squareBracket ],
	color:	Colors.Code.Braces
}, {
	tag:	[ Tags.angleBracket ],
	color:	Colors.Code.Braces
}, {
	tag:	[ Tags.attributeName ],
	color:	base0D
}, {
	tag:	[ Tags.regexp ],
	color:	base0A
}, {
	tag:	[ Tags.quote ],
	color:	base01
}, {
	tag:	[ Tags.string ],
	color:	Colors.Code.Values
}, {
	tag:					Tags.link,
	color:					base0F,
	textDecoration:			'underline',
	textUnderlinePosition:	'under'
}, {
	tag:	[ Tags.url, Tags.escape, Tags.special(Tags.string) ],
	color:	Colors.Code.Groups
}, {
	tag:	[ Tags.meta ],
	color:	base08
}, {
	tag:		[ Tags.comment ],
	color: 		base06,
	fontStyle:	'italic'
}, {
	tag:		Tags.monospace,
	color:		base01
}, {
	tag:		Tags.strong,
	fontWeight:	'bold',
	color:		base0A
}, {
	tag:		Tags.emphasis,
	fontStyle:	'italic',
	color:		base0D
}, {
	tag:			Tags.strikethrough,
	textDecoration:	'line-through'
}, {
	tag:		Tags.heading,
	fontWeight:	'bold',
	color:		base01
}, {
	tag:		Tags.special(Tags.heading1),
	fontWeight:	'bold',
	color:		base01
}, {
	tag:		Tags.heading1,
	fontWeight:	'bold',
	color:		base01
}, {
	tag:		[ Tags.heading2, Tags.heading3, Tags.heading4 ],
	fontWeight:	'bold',
	color:		base01
}, {
	tag:		[ Tags.heading5, Tags.heading6 ],
	color:		base01
}, {
	tag:		[ Tags.atom, Tags.bool, Tags.special(Tags.variableName) ],
	color:		Colors.Code.Groups
}, {
	tag:		[ Tags.processingInstruction, Tags.inserted ],
	color:		Colors.Code.Groups
}, {
	tag:		[ Tags.contentSeparator ],
	color:		base0D
}, {
	tag:			Tags.invalid,
	color:			base02,
	borderBottom:	"red"
}]);

export const theme = [
    Theme,
    SyntaxHighlighting(Syntax)
];
