import { fetchStream } from "../fetchStream";
export function aiSummarize() {
    const pageContent = document.querySelector('.content').textContent;
    const summarizeElement = document.querySelector('#ai-summarize');
    const summarizeButton = document.querySelector('.summarize-btn');
    summarizeButton?.addEventListener('click', () => {
        summarizeElement.innerHTML = '<img src="/images/loading.gif" width="35" /> <span>Wachten..</span>';
        fetchStream({
            prompt : 'summarize',
            input : pageContent,
            stream : true
        },'/ai-generate',summarizeElement);
    })
}