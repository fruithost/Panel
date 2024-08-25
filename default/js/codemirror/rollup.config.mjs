import {nodeResolve} from "@rollup/plugin-node-resolve";
import terser from '@rollup/plugin-terser';

export default {
    input: './src/main.js',
    output: [{
		file:			'./build/bundle.src.js',
		format:			'iife'
	}, {
		file:			'./build/bundle.min.js',
		sourcemap:		true,
		format:			'iife',
		plugins:		[
			terser()
		]
	}],
    plugins: [
		nodeResolve()
	],
};