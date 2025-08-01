import { fetchStream } from "../fetchStream";
export function aiClickwords() {
    /**
     * AI Clickwords
     */
    [...document.querySelectorAll('.click-me')].forEach( word => {
        const textContent = word.textContent;
        const explanationElement = document.querySelector('#ai-explanation');
        word.addEventListener('click',event => {
            explanationElement.innerHTML = '<img src="/images/loading.gif" width="35" /> <span>Wachten..</span>';
            fetchStream({
                prompt : 'explanation',
                input : textContent,
                stream : true
            },'/ai-generate',explanationElement);
        });
    });
}