
// colors reference  : https://coderwall.com/p/yphywg/printing-colorful-text-in-terminal-when-run-node-js-script
export default function message(text, type) {
    
    if(type === 'success') {
        console.log(`\x1b[42m \x1b[30m`, `✅ ${text}`, `\x1b[0m`);
    } else if (type === 'error') {
        console.log(`\x1b[41m \x1b[37m`,`🚨 ${text}`, `\x1b[0m`);

    }
}