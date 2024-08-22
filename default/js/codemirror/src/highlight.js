import {HighlightStyle, tags} from '@codemirror/highlight';

export const myHighlightStyle = HighlightStyle.define(
    {tag: tags.link, textDecoration: 'underline'},
    {fontWeight: 'bold', tag: tags.heading, textDecoration: 'underline'},
    {fontStyle: 'italic', tag: tags.emphasis},
    {fontWeight: 'bold', tag: tags.strong},
    {color: '#a77b51', tag: tags.keyword},
    {
        color: '#219',
        tag: [
            tags.atom,
            tags.bool,
            tags.url,
            tags.contentSeparator,
            tags.labelName,
        ],
    },
    {color: '#164', tag: [tags.literal, tags.inserted]},
    {color: '#a11', tag: [tags.string, tags.deleted]},
    {color: '#e40', tag: [tags.regexp, tags.escape, tags.special(tags.string)]},
    {color: '#00f', tag: tags.definition(tags.variableName)},
    {color: '#30a', tag: tags.local(tags.variableName)},
    {color: '#085', tag: [tags.typeName, tags.namespace]},
    {color: '#167', tag: tags.className},
    {
        color: '#256',
        tag: [
            tags.special(tags.variableName),
            tags.macroName,
            tags.local(tags.variableName),
        ],
    },
    {color: '#00c', tag: tags.definition(tags.propertyName)},
    {color: '#940', tag: tags.comment},
    {color: '#7a757a', tag: tags.meta},
    {color: '#f00', tag: tags.invalid},
);