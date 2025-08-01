

let chatHistory = []; // Array to store the conversation
export function aiChat() {
    const sendPromptBtn = document.getElementById('user-input-btn');
    sendPromptBtn.addEventListener('click',sendPrompt);
}
/**
 * 
 * @param {*} input 
 * @param {*} role 
 */
function displayMessage(input,role) {

}
/**
 * 
 * @param {*} role 
 * @returns 
 */
function createMessageElement(role){
    const element = document.createElement('div');
    const chat = document.getElementById('chat');
    element.classList.add(`message-${role}`);
    chat.append(element);
    return element;
}
/**
 * 
 * @returns 
 */
async function sendPrompt() {
    const userInput = document.getElementById('user-input').value;
    if (!userInput) return;
    chatHistory.push({ role: 'user', text: userInput });
    // Display user message in the UI
    displayMessage(userInput, 'user'); 
    const requestBody = {
        prompt: userInput,
        history: chatHistory.slice(0, -1) // Send all history *except* the current prompt
    };
    let fullResponse = '';
    const decoder = new TextDecoder();
    const targetElement = createMessageElement('ai'); // Function to create a new message bubble
    try {
        const response = await fetch('/ai-generate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestBody)
        });

        const reader = response.body.getReader();
        while (true) {
            const { done, value } = await reader.read();
            if (done) {
                chatHistory.push({ role: 'model', text: fullResponse });
                break;
            }
            const chunk = decoder.decode(value, { stream: true });
            fullResponse += chunk;
            targetElement.innerHTML = marked.parse(fullResponse); // Render markdown
        }
    } catch (error) {
        console.error('Fetch error:', error);
        targetElement.textContent = 'An error occurred.';
    }
}

