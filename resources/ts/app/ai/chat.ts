import { marked } from 'marked';
import loadingGifUrl from '../../../images/loading.gif';
type HistoryRole = 'user' | 'model';
type DisplayRole = 'user' | 'ai';

interface ChatMessage {
  role: HistoryRole;
  text: string;
}

const chatHistory: ChatMessage[] = [];

export function aiChat(): void {
    console.log('AI Chat initialized');
    const sendPromptBtn = document.getElementById('user-input-btn') as HTMLButtonElement | null;
    const userInputEl = document.getElementById('user-input') as HTMLInputElement | null;

    if (sendPromptBtn) {
        sendPromptBtn.addEventListener('click', sendPrompt);
    }

    if (userInputEl) {
        userInputEl.addEventListener('keydown', (event: KeyboardEvent) => {
            if (event.key === 'Enter') sendPrompt();
        });
    } else {
        console.warn('user-input-btn not found');
    }
}

/**
 * @todo display message implementation
 */
function displayMessage(input: string, role: DisplayRole): HTMLElement { // This function now only creates the element
    const element = createMessageElement(role);
    element.textContent = input;
    return element; // Return the created element, don't append here
}

function createMessageElement(role: DisplayRole): HTMLElement {
    const chatContainer = document.getElementById('chat');
    if (!chatContainer) {
        throw new Error('chat element not found');
    }
    const element = document.createElement('div');
    element.classList.add(`message-${role}`);
    return element;
}

async function sendPrompt(): Promise<void> {
    const userInputEl = document.getElementById('user-input') as HTMLInputElement | null;
    console.log('Sending prompt...');
    if (!userInputEl) {
        console.error("Input with ID 'user-input' not found.");
        return;
    }
    
    const userInput = userInputEl.value.trim();
    const filePathInput = document.getElementById('file-path-input') as HTMLInputElement | null;
    const filePaths = filePathInput ? filePathInput.value.trim().split(',').map(p => p.trim()).filter(p => p) : [];

    if (!userInput && filePaths.length === 0) return;
    
    // Display user message immediately
    let userMessage = userInput;
    if (filePaths.length > 0) {
        const fileList = filePaths.join(', ');
        userMessage = `File(s): ${fileList}\n\n${userInput}`;
    }
    const userMessageElement = displayMessage(userMessage, 'user');
    const chatContainer = document.getElementById('chat');
    if (chatContainer) {
        chatContainer.appendChild(userMessageElement);
    }
    chatHistory.push({ role: 'user', text: userInput });
    userInputEl.value = '';

    const requestBody = {
        stream : true,
        prompt: userInput,
        history: chatHistory.slice(0, -1), // Send complete history except current message
        file_paths: filePaths.length > 0 ? filePaths : undefined
    };

    let fullResponse = '';
    const aiMessageElement = createMessageElement('ai'); // Create AI message element
    aiMessageElement.innerHTML = `<img src="${loadingGifUrl}" width="35" /> <span>Wachten..</span>`;
    if (chatContainer) {
        chatContainer.appendChild(aiMessageElement); // Append AI message placeholder
        chatContainer.scrollTop = chatContainer.scrollHeight; // Scroll to bottom
    }
    try {
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found.');
        }

        const response = await fetch('/ai-generate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestBody)
        });

        if (!response.body) {
            throw new Error("Response body is missing.");
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        while (true) {
            const { done, value } = await reader.read();
            if (done) {
                // Voeg het volledige AI-bericht toe aan de geschiedenis als de stream klaar is
                chatHistory.push({ role: 'model', text: fullResponse });
                break;
            }
            const chunk = decoder.decode(value, { stream: true });
            fullResponse += chunk; // Accumulate the full response
            aiMessageElement.innerHTML = await marked.parse(fullResponse); // Render markdown with each chunk
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight; // Scroll to bottom with each chunk
            }
        }
        chatHistory.push({ role: 'model', text: fullResponse }); // Add complete AI message to history
    } catch (error) {
        console.error('Fetch error:', error);
        aiMessageElement.textContent = 'Er is een fout opgetreden bij de communicatie met de AI.'; // Display error
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight; // Scroll to bottom on error
        }
    }
}