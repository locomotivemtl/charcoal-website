/**
 * Outputs a message with color to the console.
 *
 * @see {@link https://coderwall.com/p/yphywg/printing-colorful-text-in-terminal-when-run-node-js-script}
 *
 * @param  {string} text - The text to output.
 * @param  {string} type - The context.
 * @return {void}
 */
export default function message(text, type) {
    switch (type) {
        case 'success':
            console.log(`\x1b[42m \x1b[30m`, `✅ ${text}`, `\x1b[0m`);
            break;

        case 'error':
            console.log(`\x1b[41m \x1b[37m`,`🚨 ${text}`, `\x1b[0m`);
            break;
    }
}
