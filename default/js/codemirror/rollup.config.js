import { nodeResolve } from "@rollup/plugin-node-resolve";
export default {
  input: "./source/editor.js",
  output: {
    file: "./build/bundle.js",
    format: "iife",
  },
  plugins: [nodeResolve()],
};