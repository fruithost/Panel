import { EditorView } from '@codemirror/view';
import { HighlightStyle, syntaxHighlighting as SyntaxHighlighting, defaultHighlightStyle as Default } from '@codemirror/language';
import { tags as Tags } from '@lezer/highlight';

/* Color Table */
const Colors = {
    Text:			'var(--bs-success-text-emphasis)',
    Key:			'var(--bs-code-color)',
    Braces:			'var(--bs-tertiary-color)',
    BracesActive:	'var(--bs-red)',
    SameSelection:	'rgb(from var(--bs-red) r g b / 50%)',
	Transparent:	'transparent'
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
const base0B = '#8abeb7';
const base0D = '#6fb3d2';
const base0F = '#6987AF';
const invalid = base09;
const darkBackground = '#292d30';
const highlightBackground = base02 + '30';
const background = base00;
const tooltipBackground = base01;
const selection = '#202325';
const cursor = base01;

/* Theme Colors */
const Theme = EditorView.theme({
    '&': {
        color:				base01,
        backgroundColor:	background
    },
    '.cm-content': {
        caretColor:			cursor
    },
    '.cm-cursor, .cm-dropCursor': {
		borderLeftColor:	cursor
	},
    '&.cm-focused > .cm-scroller > .cm-selectionLayer .cm-selectionBackground, .cm-selectionBackground, .cm-content ::selection': {
		backgroundColor:	selection
	},
    '.cm-activeLine': {
		backgroundColor:	highlightBackground
	},
    '.cm-selectionMatch': {
        backgroundColor:	Colors.SameSelection
    },
    '&.cm-focused .cm-matchingBracket, &.cm-focused .cm-matchingBracket *': {
        color:				Colors.BracesActive,
        backgroundColor:	Colors.Transparent,
        fontWeight:			'bolder'
    },
    '.cm-gutters': {
        borderRight:		'1px solid #ffffff10',
        color:				base06,
        backgroundColor:	darkBackground
    },
    '.cm-activeLineGutter': {
        backgroundColor:	highlightBackground
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
	color:	Colors.Key
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
	color:	Colors.Text
}, {
	tag:	[ Tags.brace ],
	color:	Colors.Braces
}, {
	tag:	[ Tags.annotation ],
	color:	invalid
}, {
	tag:	[ Tags.number, Tags.changed, Tags.annotation, Tags.modifier, Tags.self, Tags.namespace ],
	color:	base0A
}, {
	tag:	[ Tags.typeName, Tags.className ],
	color:	base0D
}, {
	tag:	[ Tags.operator, Tags.operatorKeyword ],
	color:	Colors.Text
}, {
	tag:	[ Tags.tagName ],
	color:	base0A
}, {
	tag:	[ Tags.squareBracket ],
	color:	Colors.Braces
}, {
	tag:	[ Tags.angleBracket ],
	color:	Colors.Braces
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
	color:	Colors.Text
}, {
	tag:					Tags.link,
	color:					base0F,
	textDecoration:			'underline',
	textUnderlinePosition:	'under'
}, {
	tag:	[ Tags.url, Tags.escape, Tags.special(Tags.string) ],
	color:	base0B
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
	color:		base0B
}, {
	tag:		[ Tags.processingInstruction, Tags.inserted ],
	color:		base0B
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
    SyntaxHighlighting(Syntax),
	SyntaxHighlighting(Default),
];
